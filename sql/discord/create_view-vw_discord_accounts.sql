DROP VIEW IF EXISTS discord.vw_discord_accounts;

CREATE VIEW discord.vw_discord_accounts AS (
    SELECT jsonb_build_object(
        'discord_id',
        ta.discord_id,
        'discord_name',
        ta.discord_name,
        'discord_roles',
        ta.discord_roles,
        'discord_avatar_url',
        ta.discord_avatar_url) AS discord_account
    FROM discord.t_accounts ta
    WHERE
        ta.deleted IS NULL
        AND ta.discord_bot = FALSE
        AND ta.discord_roles != '["@everyone", "Exos"]'
        AND NOT EXISTS (
            SELECT 'x'
            FROM discord.t_destiny_discord_xref tddx
            WHERE tddx.account_id = ta.account_id)
    ORDER BY ta.discord_name
);

GRANT SELECT ON discord.vw_discord_accounts TO node_batch;