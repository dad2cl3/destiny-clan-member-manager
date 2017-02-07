DROP TABLE IF EXISTS archive.t_slack_accounts;

CREATE TABLE archive.t_slack_accounts (
	effective_date date,
	slack_id varchar(25),
	slack_name varchar(25),
	destiny_name varchar(25),
	added date,
	disabled date
);