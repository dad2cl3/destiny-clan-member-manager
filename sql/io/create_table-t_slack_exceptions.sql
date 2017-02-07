DROP TABLE IF EXISTS io.t_slack_exceptions;

CREATE TABLE io.t_slack_exceptions (
	exception_id SERIAL NOT NULL,
	slack_name varchar(25) NOT NULL,
	destiny_name varchar(25) NOT NULL,
	CONSTRAINT pk_exception_id PRIMARY KEY (exception_id)
);

COMMENT ON TABLE io.t_slack_exceptions IS 'Table stores slack account names that do not match destiny account names.';