CREATE OR REPLACE FUNCTION stg.prc_update_membership(IN p_effective_date date)
RETURNS varchar 
AS $BODY$

DECLARE
   v_some_count   INTEGER := 0;
   v_output VARCHAR(500);
BEGIN

	-- Create the container for the JSON output
	v_output := '{"effective_date":"' || p_effective_date || '","members":{';
	
   /* Former members */
   UPDATE io.t_members
      SET deleted = CURRENT_DATE
    WHERE destiny_id IN
             (SELECT destiny_id
                FROM io.t_members tm
               WHERE     NOT EXISTS
                            (SELECT 'x'
                               FROM stg.t_clan_members tcm
                              WHERE tcm.destiny_id = tm.destiny_id)
                     AND deleted IS NULL);

	GET DIAGNOSTICS v_some_count = ROW_COUNT;
	v_output := v_output || '"disabled":"' || v_some_count || '"';
	
   INSERT INTO stg.t_daily_counts
        VALUES (p_effective_date, 'Former members', v_some_count);

   /* Returning members */
   UPDATE io.t_members
      SET deleted = NULL
     FROM (SELECT destiny_id FROM stg.t_clan_members) tcm
    WHERE     t_members.destiny_id = tcm.destiny_id
          AND t_members.deleted IS NOT NULL;

   GET DIAGNOSTICS v_some_count = ROW_COUNT;
   v_output := v_output || ',"return":"' || v_some_count || '"';

   INSERT INTO stg.t_daily_counts
        VALUES (p_effective_date, 'Returning members', v_some_count);

   /* New members */
   INSERT INTO io.t_members (bungie_id,
                             bungie_name,
                             destiny_id,
                             destiny_name,
                             approval_date)
      (SELECT bungie_id,
              bungie_name,
              destiny_id,
              destiny_name,
              approval_date
         FROM stg.t_clan_members tcm
        WHERE NOT EXISTS
                 (SELECT 'x'
                    FROM io.t_members tm
                   WHERE tcm.destiny_id = tm.destiny_id));

   GET DIAGNOSTICS v_some_count = ROW_COUNT;
   v_output := v_output || ',"new":"' || v_some_count || '"}';

   INSERT INTO stg.t_daily_counts
        VALUES (p_effective_date, 'New members', v_some_count);

   /* Old clan/member records */
   
   DELETE FROM io.t_clan_members
   WHERE (clan_id, destiny_id) IN (
    SELECT clan_id, destiny_id
    FROM io.t_clan_members tcm
    WHERE NOT EXISTS (
     SELECT 'x'
     FROM stg.t_clan_members
     WHERE tcm.destiny_id = t_clan_members.destiny_id
     AND tcm.clan_id = t_clan_members.clan_id)
   );
   

   /* New clan/member records */
   INSERT INTO io.t_clan_members (clan_id, destiny_id)
      (SELECT clan_id, destiny_id
         FROM stg.t_clan_members tcm
        WHERE NOT EXISTS
                 (SELECT 'x'
                    FROM io.t_clan_members
                   WHERE     tcm.destiny_id = t_clan_members.destiny_id
                         AND tcm.clan_id = t_clan_members.clan_id));

   GET DIAGNOSTICS v_some_count = ROW_COUNT;

   /* Former characters */
   -- DELETE FROM io.t_characters
   UPDATE io.t_characters
      SET deleted = CURRENT_DATE
    WHERE character_id IN
             (SELECT character_id
                FROM io.t_characters tc
               WHERE NOT EXISTS
                        (SELECT 'x'
                           FROM stg.t_member_characters tmc
                          WHERE tc.character_id = tmc.character_id));

   GET DIAGNOSTICS v_some_count = ROW_COUNT;
   v_output := v_output || ',"characters":{"deleted":"' || v_some_count || '"';

   INSERT INTO stg.t_daily_counts
        VALUES (p_effective_date, 'Deleted characters', v_some_count);

   /* Returning characters */
   UPDATE io.t_characters
      SET deleted = NULL
     FROM (SELECT character_id FROM stg.t_member_characters) tmc
    WHERE     t_characters.character_id = tmc.character_id
          AND t_characters.deleted IS NOT NULL;

   GET DIAGNOSTICS v_some_count = ROW_COUNT;

   INSERT INTO stg.t_daily_counts
        VALUES (p_effective_date, 'Returning characters', v_some_count);

   /* New characters */
   INSERT INTO io.t_characters (character_id,
                                class_type,
                                last_played,
                                total_min_played)
      (SELECT character_id,
              class_type,
              last_played,
              total_min_played
         FROM stg.t_member_characters tmc
        WHERE NOT EXISTS
                 (SELECT 'x'
                    FROM io.t_characters tc
                   WHERE tmc.character_id = tc.character_id));

   GET DIAGNOSTICS v_some_count = ROW_COUNT;
   v_output := v_output || ',"new":"' || v_some_count || '"}';

   INSERT INTO stg.t_daily_counts
        VALUES (p_effective_date, 'New characters', v_some_count);

   /* Former characters */
   /*
   DELETE FROM io.t_member_characters
   WHERE (destiny_id, character_id) IN (
    SELECT destiny_id, character_id
    FROM io.t_member_characters tmc
    WHERE NOT EXISTS (
     SELECT 'x'
     FROM stg.t_member_characters
     WHERE tmc.destiny_id = t_member_characters.destiny_id
     AND tmc.character_id = t_member_characters.character_id)
   );
   */

   /* New characters */
   INSERT INTO io.t_member_characters (destiny_id, character_id)
      (SELECT destiny_id, character_id
         FROM stg.t_member_characters tmc
        WHERE NOT EXISTS
                 (SELECT 'x'
                    FROM io.t_member_characters
                   WHERE     tmc.destiny_id = t_member_characters.destiny_id
                         AND tmc.character_id =
                                t_member_characters.character_id));

   GET DIAGNOSTICS v_some_count = ROW_COUNT;

   /* Update numbers for last played and total minutes played */
   UPDATE io.t_characters
      SET last_played = tmc.last_played,
          total_min_played = tmc.total_min_played
     FROM (SELECT character_id, last_played, total_min_played
             FROM stg.t_member_characters) tmc
    WHERE tmc.character_id = t_characters.character_id;

   GET DIAGNOSTICS v_some_count = ROW_COUNT;

   /* Push the final data to the archive table t_clan_members */
   -- CREATE TABLE archive.t_clan_members AS (
   INSERT INTO archive.t_clan_members
      (SELECT CURRENT_DATE effective_date,
              tclans.clan_id,
              tclans.clan_name,
              tm.bungie_id,
              tm.bungie_name,
			  tm.destiny_id,
			  tm.destiny_name,
              tm.added member_added,
			  tm.deleted member_deleted,
              tm.approval_date,
              tchar.character_id,
              tchar.class_type,
              dcd.class_name,
              dcd.class_hash,
              tchar.added char_added,
			  tchar.deleted char_deleted,
              tchar.last_played,
              tchar.total_min_played
         FROM io.t_clans tclans,
              io.t_clan_members tcm,
              io.t_members tm,
              io.t_member_characters tmc,
              io.t_characters tchar,
              manifest.t_class dcd
        WHERE     tclans.clan_id = tcm.clan_id
              AND tcm.destiny_id = tm.destiny_id
              AND tm.destiny_id = tmc.destiny_id
              AND tmc.character_id = tchar.character_id
              AND tchar.class_type = dcd.class_type
              -- AND tm.deleted IS NULL
              -- AND tchar.deleted IS NULL
			  );

   GET DIAGNOSTICS v_some_count = ROW_COUNT;
	v_output := v_output || ',"archived":"' || v_some_count || '"';
	
   INSERT INTO stg.t_daily_counts
        VALUES (p_effective_date, 'Archived clan data', v_some_count);

	v_output := v_output || '}';
	
   RETURN v_output;
END;
$BODY$
LANGUAGE plpgsql;