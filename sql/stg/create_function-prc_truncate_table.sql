CREATE OR REPLACE FUNCTION stg.prc_truncate_table(IN p_table_name varchar)
RETURNS bool 
AS $BODY$
BEGIN
   EXECUTE 'TRUNCATE TABLE STG.' || p_table_name;

	RETURN true;
   
	EXCEPTION WHEN others THEN
   		RETURN false;
END;
$BODY$
LANGUAGE plpgsql;
