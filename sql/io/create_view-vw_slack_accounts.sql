CREATE OR REPLACE VIEW io.vw_slack_accounts AS

SELECT tsa.slack_id,
        CASE
            WHEN (tse.destiny_name IS NULL) THEN tsa.slack_name
            ELSE tse.destiny_name
        END AS user_name
   FROM (io.t_slack_accounts tsa
     LEFT JOIN io.t_slack_exceptions tse ON tsa.slack_name = tse.slack_name);