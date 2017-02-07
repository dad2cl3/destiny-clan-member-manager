DROP TABLE IF EXISTS io.t_member_characters;

CREATE TABLE io.t_member_characters (
	destiny_id int8 NOT NULL,
	character_id int8 NOT NULL,
	deleted date
);