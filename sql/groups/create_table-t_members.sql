DROP TABLE IF EXISTS groups.t_members;

CREATE TABLE groups.t_members (
    member_id SERIAL NOT NULL,
    clan_id INT8 NOT NULL,
    bungie_id INT8,
    bungie_name VARCHAR(100),
    bungie_membership_type INTEGER,
    bungie_icon_path VARCHAR(250),
    destiny_id INT8 NOT NULL,
    destiny_name VARCHAR(100) NOT NULL,
    destiny_membership_type INTEGER NOT NULL,
    destiny_icon_path VARCHAR(250),
    deleted DATE,
    deleted_flag BOOLEAN NOT NULL DEFAULT FALSE,
    added DATE NOT NULL DEFAULT CURRENT_DATE,
    admin BOOLEAN NOT NULL DEFAULT FALSE,
    versions_owned INTEGER NOT NULL DEFAULT 0,
    CONSTRAINT pk_member_id PRIMARY KEY (member_id)
);
