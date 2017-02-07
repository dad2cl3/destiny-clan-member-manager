DROP TABLE IF EXISTS io.t_slack_accounts;

CREATE TABLE io.t_slack_accounts (
	slack_id varchar(25) NOT NULL,
	slack_name varchar(25) NOT NULL,
	added date NOT NULL DEFAULT now(),
	disabled date
);