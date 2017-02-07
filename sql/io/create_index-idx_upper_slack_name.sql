DROP INDEX IF EXISTS io.idx_upper_slack_name;

CREATE INDEX idx_upper_slack_name ON io.t_slack_accounts (upper(slack_name));