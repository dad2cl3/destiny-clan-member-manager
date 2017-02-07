DROP TABLE IF EXISTS stg.t_slack_accounts;

CREATE TABLE stg.t_slack_accounts (
	effective_date date NOT NULL,
	slack_id varchar(25) NOT NULL,
	user_name varchar(50) NOT NULL
);

COMMENT ON TABLE stg.t_slack_accounts IS 'Raw staging of Slack accounts';

