import json, os, pg8000


def handler(event, context):
    pg = pg8000.connect(host=os.environ['database_host'], port=int(os.environ['database_port']), database=os.environ['database_name'],
                        user=os.environ['database_user'], password=os.environ['password'])

    loadSQL = 'SELECT staging.prc_update_membership(CURRENT_DATE)'

    pg_cursor = pg.cursor()
    pg_cursor.execute(loadSQL)
    changes = pg_cursor.fetchall()
    pg.commit()
    pg.close()

    return json.loads(changes[0][0])