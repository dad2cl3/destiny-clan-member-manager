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

def buildCharacterRequests(members):
    print('Building character requests...')

    requests = []

    for member in members:
        request = apiConfig['memberUrl'].format(member['destinyUserInfo']['membershipType'], member['destinyUserInfo']['membershipId'])
        requests.append(request)

    return requests

def getMembers(requestURL):

    xApiKey = apiConfig['xApiKey']
    members = requests.get(requestURL, headers={'X-API-Key': xApiKey}).text
    return members

def getBungieData(requestURL):

    xApiKey = apiConfig['xApiKey']
    response = requests.get(requestURL, headers={'X-API-Key': xApiKey}).text
    return response

def processRequests(apiRequests):
    print('Processing requests...')

    # Initialize variable to hold responses
    responses = []

    # Capture starting time
    apiStart = time.time()

    requestCount = len(apiRequests)
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
                #futureRequests = {executor.submit(getMembers, request): request for request in memberRequests[start:(i + 1)]}
                futureRequests = {executor.submit(getBungieData, request): request for request in apiRequests[start:(i + 1)]}

            for request in futures.as_completed(futureRequests):
                responses.append(request.result())

            start = i + 1

    # Capture finish time
    apiEnd = time.time()
    # Calculation API execution time
    apiDuration = apiEnd - apiStart
    print('API execution time: {0:.2f}'.format(apiDuration))
    return responses

def prepareMembers(members):
    print('Preparing member data...')
    memberCount = 0
    memberList = []

    for clan in members:

        members = json.loads(clan)['Response']['results']

        for member in members:
            record = {}
            record['clan_id'] = member['groupId']

            clanId = [member['groupId']]

            destinyUserInfo = member['destinyUserInfo']
            del destinyUserInfo['iconPath']

            record['destinyUserInfo'] = destinyUserInfo
            #print(record)
            bungieNetUserInfo = []

            if 'bungieNetUserInfo' in member.keys():
                bungieNetUserInfo = member['bungieNetUserInfo']
                del bungieNetUserInfo['iconPath']
                del bungieNetUserInfo['supplementalDisplayName']

                record['bungieNetUserInfo'] = bungieNetUserInfo

            '''if len(bungieNetUserInfo) > 0:
                record = clanId + list(destinyUserInfo.values()) + list(bungieNetUserInfo.values())
            else:
                record = clanId + list(destinyUserInfo.values())'''

            memberList.append(record)
            memberCount += 1

    return memberList

def prepareCharacters(characters):
    print('Preparing character data...')

    characterList = []

    for characterResponse in characters:
        characterKeys = json.loads(characterResponse).keys()

        if 'Response' in characterKeys:
            profile = json.loads(characterResponse)['Response']['profile']
            #print(profile)
            characterData = json.loads(characterResponse)['Response']['characters']['data']
            for characterId in characterData:
                #print(characterData[characterId])
                character = characterData[characterId]

                record = {}
                record['membershipId'] = character['membershipId']
                record['membershipType'] = character['membershipType']
                record['characterId'] = character['characterId']
                record['dateLastPlayed'] = character['dateLastPlayed']
                record['minutesPlayedTotal'] = character['minutesPlayedTotal']
                record['classHash'] = character['classHash']

                characterList.append(record)

    return characterList

def stageMembers(db, members):
    print('Staging members...')
    inserts = 0

    for member in members:
        if len(member) == 3:
            sql = sqlConfig['longMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values()) + list(member['bungieNetUserInfo'].values())

        elif len(member) == 2:
            sql = sqlConfig['shortMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values())

        pgCursor = db.cursor()
        pgCursor.execute(sql, record)
        inserts += pgCursor.rowcount

    # Perform all inserts before issuing the commit
    db.commit()

    return inserts

def stageCharacters(db, characters):
    print('Staging characters...')
    inserts = 0

    for character in characters:
        '''if len(member) == 3:
            sql = sqlConfig['longMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values()) + list(member['bungieNetUserInfo'].values())

        elif len(member) == 2:
            sql = sqlConfig['shortMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values())'''

        pgCursor = db.cursor()
        pgCursor.execute(sqlConfig['characterInsert'], list(character.values()))
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
    memberInserts = stageMembers(pg, memberList)
    print('Inserts: {0}'.format(memberInserts))

    # Build character requests
    characterRequests = buildCharacterRequests(memberList)

    # Get the characters from API
    characters = processRequests(characterRequests)

    # Prepare the character data
    characterList = prepareCharacters(characters)

    # Stage characters
    characterInserts = stageCharacters(pg, characterList)
    print('Inserts: {0}'.format(characterInserts))

    # Close database connection
    pg.close()

    end = time.time()

    duration = end - start
    print('Duration: {0:.2f}s'.format(duration))

    # Prepare to return data
    counts = {}
    counts['counts'] = {'members': memberInserts, 'characters': characterInserts}
    return counts

