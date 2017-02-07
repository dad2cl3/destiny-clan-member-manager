DROP INDEX IF EXISTS io.idx_upper_destiny_name;

CREATE INDEX idx_upper_destiny_name ON io.t_members (upper(destiny_name));