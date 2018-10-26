DROP TABLE IF EXISTS staging.t_clan_members;

CREATE TABLE staging.t_clan_members (
	effective_date DATE NOT NULL DEFAULT CURRENT_DATE,
	clan_id int4 NOT NULL,
	bungie_id INT4,
	bungie_name VARCHAR(100),
	bungie_membership_type INT4,
	bungie_icon_path VARCHAR(250),
	destiny_id INT8 NOT NULL,
	destiny_name VARCHAR(100) NOT NULL,
	destiny_membership_type INT4 NOT NULL,
	destiny_icon_path VARCHAR(250)
);

GRANT SELECT, INSERT, UPDATE, DELETE, TRUNCATE ON staging.t_clan_members TO node_batch;