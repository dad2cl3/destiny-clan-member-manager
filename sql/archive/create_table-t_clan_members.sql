DROP TABLE IF EXISTS archive.t_clan_members;

CREATE TABLE archive.t_clan_members (
	effective_date date,
	clan_id int4,
	clan_name varchar(100),
	bungie_id int4,
	bungie_name varchar(25),
	destiny_id int8,
	destiny_name varchar(25),
	member_added date,
	member_deleted date,
	approval_date date,
	character_id int8,
	class_type int4,
	class_name varchar(20),
	class_hash varchar(20),
	char_added date,
	char_deleted date,
	last_played timestamp(6) WITH TIME ZONE,
	total_min_played int4
);