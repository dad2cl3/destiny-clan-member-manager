DROP TABLE IF EXISTS groups.t_characters;

CREATE TABLE groups.t_characters (
	character_id int8 NOT NULL,
	member_id INT8 NOT NULL,
	class_hash INT8 NOT NULL,
	added DATE NOT NULL DEFAULT CURRENT_DATE,
	deleted DATE,
	deleted_flag BOOLEAN NOT NULL DEFAULT FALSE,
	minutes_played_total INT4 NOT NULL,
	last_played TIMESTAMPTZ NOT NULL,
	CONSTRAINT pk_character_id PRIMARY KEY (character_id)
);

CREATE INDEX idx_character_class_hash ON groups.t_characters USING BTREE(class_hash ASC);