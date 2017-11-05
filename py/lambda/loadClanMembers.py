import pg8000, json, os


def handler(event, context):
    pg = pg8000.connect(host=os.environ['host'], port=int(os.environ['port']), database=os.environ['database'],
                        user=os.environ['user'], password=os.environ['password'])

    loadSQL = 'SELECT staging.prc_update_membership(CURRENT_DATE)'

    pgCursor = pg.cursor()
    pgCursor.execute(loadSQL)
    changes = pgCursor.fetchall()
    pg.commit()
    pg.close()

    return json.loads(changes[0][0])