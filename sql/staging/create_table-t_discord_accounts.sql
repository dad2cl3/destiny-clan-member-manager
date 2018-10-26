DROP TABLE IF EXISTS staging.t_discord_accounts;

CREATE TABLE staging.t_discord_accounts (
  effective_date DATE NOT NULL DEFAULT CURRENT_DATE,
  server_id INT8 NOT NULL,
  server_name VARCHAR(100) NOT NULL,
  discord_id INT8 NOT NULL,
  discord_name VARCHAR(100) NOT NULL,
  discord_roles JSONB NOT NULL,
  discord_bot BOOLEAN NOT NULL,
  discord_avatar_url VARCHAR,
  discord_display_name VARCHAR
);

ALTER TABLE staging.t_discord_accounts OWNER TO jachal;

GRANT SELECT, UPDATE, INSERT, DELETE, TRUNCATE ON staging.t_discord_accounts TO node_batch;
