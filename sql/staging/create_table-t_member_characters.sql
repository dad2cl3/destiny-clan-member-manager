DROP TABLE IF EXISTS staging.t_member_characters;

CREATE TABLE staging.t_member_characters (
	effective_date DATE NOT NULL DEFAULT CURRENT_DATE,
	destiny_id int8 NOT NULL,
	destiny_membership_type INT4 NOT NULL,
	character_id int8 NOT NULL,
	class_hash INT8 NOT NULL,
	versions_owned INT NOT NULL DEFAULT 0,
	minutes_played_total INT4 NOT NULL,
	last_played TIMESTAMPTZ NOT NULL
);