from concurrent import futures

import pg8000, requests, time, json

with open('config.json', 'r') as configFile:
    config = json.load(configFile)

apiConfig = config['API']
dbConfig = config['Database']
sqlConfig = config['SQL']

def truncateStaging(db):
    print('Truncating stage table...')

    pgCursor = db.cursor()
    pgCursor.execute(sqlConfig['memberTruncate'])
    pgCursor.execute(sqlConfig['characterTruncate'])

def getClans(db):
    print('Getting clans...')

    pgCursor = db.cursor()
    pgCursor.execute(sqlConfig['clanSelect'])
    clans = pgCursor.fetchall()
    print('Clans found: {0}'.format(pgCursor.rowcount))

    return clans

def buildRequests(clans):
    print('Building requests...')

    requests = []

    for clan in clans:
        url = apiConfig['clanUrl'].format(clan[0])

        requests.append(url)

    return requests

def getMembers(requestURL):

    xApiKey = apiConfig['xApiKey']

    members = requests.get(requestURL, headers={'X-API-Key': xApiKey}).text

    return members

def processRequests(memberRequests):
    print('Processing requests...')

    # Initialize variable to hold members
    members = []

    # Capture starting time
    apiStart = time.time()

    requestCount = len(memberRequests)
    print('Requests: {0}'.format(requestCount))

    maxChunkSize = 25
    start = 0

    if requestCount >= maxChunkSize:
        chunkSize = maxChunkSize
    else:
        chunkSize = requestCount

    for i in range(requestCount):
        if (i % chunkSize == 0 and i > 0) or i == (requestCount - 1):
            print('Processing chunk {0} - {1}'.format(str(start), str(i)))

            chunk = i - start + 1

            with futures.ThreadPoolExecutor(chunk) as executor:
                futureRequests = {executor.submit(getMembers, request): request for request in memberRequests[start:(i + 1)]}

            for request in futures.as_completed(futureRequests):
                members.append(request.result())

            start = i + 1

    # Capture finish time
    apiEnd = time.time()
    # Calculation API execution time
    apiDuration = apiEnd - apiStart
    print('API execution time: {0:.2f}'.format(apiDuration))
    return members

def prepareMembers(members):
    print('Preparing member data...')
    memberCount = 0
    memberList = []

    for clan in members:

        members = json.loads(clan)['Response']['results']

        for member in members:
            clanId = [member['groupId']]

            destinyUserInfo = member['destinyUserInfo']
            del destinyUserInfo['iconPath']

            bungieNetUserInfo = []

            if 'bungieNetUserInfo' in member.keys():
                bungieNetUserInfo = member['bungieNetUserInfo']
                del bungieNetUserInfo['iconPath']
                del bungieNetUserInfo['supplementalDisplayName']

            if len(bungieNetUserInfo) > 0:
                record = clanId + list(destinyUserInfo.values()) + list(bungieNetUserInfo.values())
            else:
                record = clanId + list(destinyUserInfo.values())

            memberList.append(record)
            memberCount += 1

    return memberList

def stageMembers(db, members):
    print('Staging members...')
    inserts = 0

    for member in members:
        if len(member) == 7:
            sql = sqlConfig['longMemberInsert']

        elif len(member) == 4:
            sql = sqlConfig['shortMemberInsert']

        pgCursor = db.cursor()
        pgCursor.execute(sql, member)
        inserts += pgCursor.rowcount

    # Perform all inserts before issuing the commit
    db.commit()

    return inserts

def handler(event, context):

    start = time.time()

    # Open database connection
    pg = pg8000.connect(host=dbConfig['host'], port=dbConfig['port'], database=dbConfig['database'], user=dbConfig['user'], password=dbConfig['password'])

    # Truncate staging table
    truncateStaging(pg)

    # Get the clans from database
    clans = getClans(pg)

    # Build the clan API requests
    memberRequests = buildRequests(clans)

    # Get the clan members from API
    members = processRequests(memberRequests)

    # Prepare the data
    memberList = prepareMembers(members)

    # Stage members
    inserts = stageMembers(pg, memberList)
    print('Inserts: {0}'.format(inserts))

    # Close database connection
    pg.close()

    end = time.time()

    duration = end - start
    print('Duration: {0:.2f}s'.format(duration))

