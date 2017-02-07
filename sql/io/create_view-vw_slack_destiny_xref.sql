CREATE OR REPLACE VIEW io.vw_slack_destiny_xref AS
SELECT tsa.slack_id,
    tsa.slack_name,
    COALESCE(tse.destiny_name, tsa.slack_name) AS destiny_name
   FROM (io.t_slack_accounts tsa
     LEFT JOIN io.t_slack_exceptions tse ON tsa.slack_name = tse.slack_name)
  WHERE (tsa.disabled IS NULL);