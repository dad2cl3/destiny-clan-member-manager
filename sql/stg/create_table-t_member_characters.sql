DROP TABLE IF EXISTS stg.t_member_characters;

CREATE TABLE stg.t_member_characters (
	effective_date date NOT NULL,
	clan_id int4 NOT NULL,
	destiny_id int8 NOT NULL,
	character_id int8 NOT NULL,
	class_type int4 NOT NULL,
	last_played date NOT NULL,
	total_min_played int4 NOT NULL
);