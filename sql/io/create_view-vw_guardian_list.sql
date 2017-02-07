CREATE OR REPLACE VIEW io.vw_guardian_list AS

SELECT ('https://www.bungie.net/en/Profile/254/' || tm.bungie_id) AS profile,
    tm.destiny_name AS psndisplayname,
        CASE
            WHEN tm.destiny_name IN ('ZombWie', 'martankr', 'Peu_leger') THEN '1. Founders'
            WHEN tm.destiny_name IN ('dad2cl3', 'emerik', 'GreatDaemon', 'ICEMANJ71', 'JB_rocks', 'Klismo', 'mark-vader87', 'Matrix015', 'viper00723') THEN '2. Admins'
            WHEN ((tm.destiny_name) = 'WardCleaver101') THEN '3. In Memory'
            WHEN ((upper(substr((tm.destiny_name), 1, 1)) >= 'A') AND (upper(substr((tm.destiny_name), 1, 1)) <= 'F')) THEN '4. A-F'
            WHEN ((upper(substr((tm.destiny_name), 1, 1)) >= 'G') AND (upper(substr((tm.destiny_name), 1, 1)) <= 'N')) THEN '5. G-N'
            WHEN ((upper(substr((tm.destiny_name), 1, 1)) >= 'O') AND (upper(substr((tm.destiny_name), 1, 1)) <= 'Z')) THEN '6. O-Z'
            ELSE NULL
        END AS grouping
   FROM io.t_members tm
  WHERE (tm.deleted IS NULL)
  ORDER BY
        CASE
            WHEN tm.destiny_name IN ('ZombWie', 'martankr', 'Peu_leger') THEN '1. Founders'
            WHEN tm.destiny_name IN ('dad2cl3', 'emerik', 'GreatDaemon', 'ICEMANJ71', 'JB_rocks', 'Klismo', 'mark-vader87', 'Matrix015', 'viper00723') THEN '2. Admins'
            WHEN ((tm.destiny_name) = 'WardCleaver101') THEN '3. In Memory'
            WHEN ((upper(substr((tm.destiny_name), 1, 1)) >= 'A') AND (upper(substr((tm.destiny_name), 1, 1)) <= 'F')) THEN '4. A-F'
            WHEN ((upper(substr((tm.destiny_name), 1, 1)) >= 'G') AND (upper(substr((tm.destiny_name), 1, 1)) <= 'N')) THEN '5. G-N'
            WHEN ((upper(substr((tm.destiny_name), 1, 1)) >= 'O') AND (upper(substr((tm.destiny_name), 1, 1)) <= 'Z')) THEN '6. O-Z'
            ELSE NULL
        END, tm.destiny_name;
