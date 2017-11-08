import pg8000, requests, json
from discord import Embed

with open('config.json', 'r') as configFile:
    config = json.load(configFile)

dbConfig = config['Database']
discordConfig = config['Discord']
sqlConfig = config['SQL']

def runQuery(db, sql):

    pgCursor = db.cursor()
    pgCursor.execute(sql)
    data = pgCursor.fetchall()

    return data

def buildReport(data):
    print('Building the report...')

    '''Holy inconsistent data types Batman...SMDH'''

    # Initialize the RichEmbed
    embed = Embed()

    # Add the footer
    embed.set_footer(text='The Dude Abides!', icon_url='https://s3.amazonaws.com/dad2cl3-manifest/public/IronOrangeNewLogo.png')

    for key in data.keys():
        if key == 'counts':
            total = 0

            counts = data[key]

            value = '\n'.join((' - '.join(str(val) for val in count)) for count in counts)

            for count in counts:
                total += count[1]

            embed.add_field(name='Total Membership = {0}'.format(total), value=value, inline=False)
        elif key == 'newMembers':
            newMembers = json.loads(data[key][0][0])

            value = ''

            if int(newMembers['count']) == 0:
                value = 'None'
            else:
                for newMember in newMembers['members']:
                    if len(value) > 0:
                        value += '\n'

                    value += newMember

            embed.add_field(name='New Members', value=value, inline=False)

        elif key == 'formerMembers':
            formerMembers = json.loads(data[key][0][0])

            value = ''

            if int(formerMembers['count']) == 0:
                value = 'None'

            embed.add_field(name='Former Members', value=value, inline=False)

    report = embed.to_dict()

    return report

def sendReport(report):
    print('Sending report...')

    # Build header
    headers = {'Content-Type': 'application/json'}

    payload = {}
    payload['content'] = 'Iron Orange Membership Report'
    payload['embeds'] = [report]

    # Webhook URL
    url = discordConfig['webhooks'][0]['io']

    return requests.post(url, data=json.dumps(payload), headers=headers)

def handler (event, context):
    # Open the database connection
    pg = pg8000.connect(host=dbConfig['host'], port=dbConfig['port'], database=dbConfig['database'], user=dbConfig['user'], password=dbConfig['password'])

    reportData = {}
    # Get the overall counts
    counts = runQuery(pg, sqlConfig['memberCount'])
    reportData['counts'] = counts

    # Get the new members
    newMembers = runQuery(pg, sqlConfig['newMembers'])
    reportData['newMembers'] = newMembers

    # Get the former members
    formerMembers = runQuery(pg, sqlConfig['formerMembers'])
    reportData['formerMembers'] = formerMembers

    # Build the report
    report = buildReport(reportData)

    # Send the report
    result = sendReport(report)

    print(result.status_code)

    # Close the database connection
    pg.close()
