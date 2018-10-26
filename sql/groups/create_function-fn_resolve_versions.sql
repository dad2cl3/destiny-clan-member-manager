CREATE OR REPLACE FUNCTION groups.fn_resolve_versions (IN p_versions_owned INTEGER)
RETURNS VARCHAR
AS $BODY$

DECLARE
    v_version_owned VARCHAR;
    CONST_FORSAKEN INT := 8;
    CONST_WARMIND INT := 4;
    CONST_CURSE_OSIRIS INT := 2;
    CONST_DESTINY_2 INT := 1;
BEGIN
    IF p_versions_owned & CONST_FORSAKEN = CONST_FORSAKEN THEN
        v_version_owned := 'Forsaken';
    ELSIF p_versions_owned & CONST_WARMIND = CONST_WARMIND THEN
        v_version_owned := 'Warmind';
    ELSIF p_versions_owned & CONST_CURSE_OSIRIS = CONST_CURSE_OSIRIS THEN
        v_version_owned := 'Curse of Osiris';
    ELSIF p_versions_owned & CONST_DESTINY_2 = CONST_DESTINY_2 THEN
        v_version_owned := 'Destiny 2';
    ELSE
        v_version_owned := 'None';
    END IF;

    RETURN v_version_owned;
END;
$BODY$
LANGUAGE plpgsql;