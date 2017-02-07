DROP TABLE IF EXISTS io.t_members;

CREATE TABLE io.t_members (
	destiny_id int8 NOT NULL,
	destiny_name varchar(25) NOT NULL,
	bungie_id int4,
	bungie_name varchar(25),
	added date NOT NULL DEFAULT now(),
	deleted date,
	approval_date date
);