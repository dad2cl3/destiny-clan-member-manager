DROP TABLE IF EXISTS groups.t_groups;

CREATE TABLE groups.t_groups (
    group_id SERIAL NOT NULL,
    group_name VARCHAR(100) NOT NULL,
    CONSTRAINT pk_group_id PRIMARY KEY (group_id)
);
