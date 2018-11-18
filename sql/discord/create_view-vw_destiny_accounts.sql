DROP VIEW IF EXISTS discord.vw_destiny_accounts;

CREATE VIEW discord.vw_destiny_accounts AS (
    SELECT JSONB_BUILD_OBJECT(
        'destiny_id',
        tm.destiny_id,
        'destiny_name',
        tm.destiny_name,
        'destiny_membership_type',
        tm.destiny_membership_type,
        'clan_name',
        tc.clan_name) AS destiny_account
    FROM groups.t_members tm, groups.t_clans tc
    WHERE tm.deleted IS NULL
    AND tm.clan_id = tc.clan_id
		AND tc.clan_name NOT IN ('Iron Orange 3rd Bn', 'Iron Orange Moon')
    AND NOT EXISTS (
        SELECT 'x'
        FROM discord.t_destiny_discord_xref tddx
        WHERE tddx.member_id = tm.member_id)
    ORDER BY tm.destiny_name
);

GRANT SELECT ON discord.vw_destiny_accounts TO node_batch;