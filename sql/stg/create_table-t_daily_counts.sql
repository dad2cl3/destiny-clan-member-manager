DROP TABLE IF EXISTS stg.t_daily_counts;

CREATE TABLE stg.t_daily_counts (
	effective_date date NOT NULL,
	key varchar(50) NOT NULL,
	value int4 NOT NULL
);