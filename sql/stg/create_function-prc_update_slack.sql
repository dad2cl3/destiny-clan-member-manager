CREATE OR REPLACE FUNCTION stg.prc_update_slack(IN p_effective_date date)
RETURNS varchar
AS $BODY$

DECLARE
   v_output      VARCHAR (500);
   v_row_count   INTEGER := 0;
BEGIN
   v_output := '{"effective_date":"' || p_effective_date || '"';

   -- New Slack accounts
   INSERT INTO io.t_slack_accounts (slack_id, slack_name, added)
      (SELECT slack_id, user_name, CURRENT_DATE FROM stg.t_slack_accounts
       EXCEPT
       SELECT slack_id, slack_name, CURRENT_DATE
         FROM io.t_slack_accounts
        WHERE disabled IS NULL);

   GET DIAGNOSTICS v_row_count = ROW_COUNT;

   v_output := v_output || ',"new":"' || v_row_count || '"';

   -- Disable Slack accounts
   /*
   UPDATE io.t_slack_accounts
      SET disabled = CURRENT_DATE
    WHERE (slack_id, slack_name) IN
             (SELECT slack_id, slack_name
                FROM io.t_slack_accounts
               WHERE disabled IS NULL
              EXCEPT
              SELECT slack_id, user_name FROM stg.t_slack_accounts);
	*/
	UPDATE io.t_slack_accounts
	SET disabled = CURRENT_DATE
	WHERE NOT EXISTS (
		SELECT 'x'
		FROM stg.t_slack_accounts tsa
		WHERE t_slack_accounts.slack_id = tsa.slack_id);

   GET DIAGNOSTICS v_row_count = ROW_COUNT;

   v_output := v_output || ',"disabled":"' || v_row_count || '"';

   -- Returning Slack accounts
   UPDATE io.t_slack_accounts
      SET disabled = NULL
    WHERE (slack_id) IN
             (SELECT slack_id
                FROM io.t_slack_accounts
               WHERE disabled IS NOT NULL
              INTERSECT
              SELECT slack_id FROM stg.t_slack_accounts);


   GET DIAGNOSTICS v_row_count = ROW_COUNT;

   v_output := v_output || ',"returning":"' || v_row_count || '"';

   -- Archive accounts
   INSERT INTO archive.t_slack_accounts
      (SELECT CURRENT_DATE effective_date,
              tsa.slack_id,
              tsa.slack_name,
              COALESCE (tse.destiny_name, tsa.slack_name) destiny_name,
              tsa.added,
              tsa.disabled
         FROM io.t_slack_accounts tsa
              LEFT OUTER JOIN io.t_slack_exceptions tse
                 ON tsa.slack_name = tse.slack_name);

   GET DIAGNOSTICS v_row_count = ROW_COUNT;

   v_output := v_output || ',"archived":"' || v_row_count || '"';

   v_output := v_output || '}';

   RETURN v_output;
   
  EXCEPTION
	WHEN others THEN
		v_output := 'Error occurred.';
END;
$BODY$
LANGUAGE plpgsql;