DROP TABLE IF EXISTS groups.t_clans;

CREATE TABLE groups.t_clans (
	clan_id INT8 NOT NULL,
	clan_name VARCHAR(100) NOT NULL,
	CONSTRAINT pk_clan_id PRIMARY KEY (clan_id)
);