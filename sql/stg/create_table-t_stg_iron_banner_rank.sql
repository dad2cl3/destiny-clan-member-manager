DROP TABLE IF EXISTS stg.t_iron_banner_rank;

CREATE TABLE stg.t_iron_banner_rank (
	destiny_id int8 NOT NULL,
	character_id int8 NOT NULL,
	current_rank int4 NOT NULL,
	effective_date date NOT NULL
);