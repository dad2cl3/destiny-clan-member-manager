DROP TABLE IF EXISTS stg.t_clan_members;

CREATE TABLE stg.t_clan_members (
	effective_date date NOT NULL,
	clan_id int4 NOT NULL,
	bungie_id int4,
	bungie_name varchar(100) DEFAULT NULL,
	destiny_id int8 NOT NULL,
	destiny_name varchar(100) NOT NULL,
	approval_date date NOT NULL
);
