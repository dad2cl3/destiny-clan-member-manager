DROP TABLE IF EXISTS io.t_characters;

CREATE TABLE io.t_characters (
	character_id int8 NOT NULL,
	class_type int4 NOT NULL,
	last_played date,
	total_min_played int4,
	added date DEFAULT NOW(),
	deleted date
);