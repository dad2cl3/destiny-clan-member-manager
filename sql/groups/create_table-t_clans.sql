DROP TABLE IF EXISTS io.t_clans;

CREATE TABLE io.t_clans (
	clan_id int4 NOT NULL,
	clan_name varchar(100) NOT NULL,
	CONSTRAINT pk_clan_id PRIMARY KEY (clan_id)
);