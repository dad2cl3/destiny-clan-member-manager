from concurrent import futures
import asyncio
import aiohttp

import pg8000, requests, time, json

with open('config.json', 'r') as configFile:
    config = json.load(configFile)

api_config = config['API']
db_config = config['Database']
sql_config = config['SQL']


def execute_ddl(pg_cursor, ddl):
    print('Executing DDL: {0}'.format(ddl))

    pg_cursor.execute(ddl)


def execute_select(pg_cursor, sql):
    print('Executing SQL SELECT...')

    pg_cursor.execute(sql)
    results = pg_cursor.fetchall()
    print('Records: {0}'.format(pg_cursor.rowcount))
    print(pg_cursor.description)

    return results


def pre_processing(pg_cursor):
    print('Executing preprocessing...')

    if 'preProcessing' in sql_config:
        pre_proc = sql_config['preProcessing']
        for pre_proc_stmt in pre_proc:
            execute_ddl(pg_cursor, pre_proc_stmt)


def build_array_requests():
    print('Building requests...')

    requests = []

    return requests


def build_requests(clans):
    print('Building requests...')
    requests = []

    for clan in clans:
        url = api_config['clanUrl'].format(clan[0]['clan_id'])

        requests.append(url)

    #print(requests) # debugging
    return requests


def build_character_requests(members):
    print('Building character requests...')

    requests = []

    for member in members:
        request = api_config['memberUrl'].format(member['destinyUserInfo']['membershipType'], member['destinyUserInfo']['membershipId'])
        requests.append(request)

    return requests


async def process_request_chunk(request_chunk):
    api_key = api_config['xApiKey']

    headers = {
        'X-API-Key': api_key
    }

    tasks = []

    cookie_jar = aiohttp.DummyCookieJar()
    async with aiohttp.ClientSession(headers=headers,cookie_jar=cookie_jar) as session:
        for request in request_chunk:
            #print('Request URL: {0}'.format(request))
            task = asyncio.ensure_future(get_api_data(request, session))
            tasks.append(task)
            await asyncio.gather(*tasks)


#async def get_api_data(request_url, session):
def get_api_data(request_url):
    print('Getting API data...')
    #print('Request URL: {0}'.format(request_url))

    api_key = api_config['xApiKey']

    response = requests.get(request_url, headers={'X-API-Key': api_key})
    if response.status_code == 200:
        response_data = response.json()
        return response_data
    else:
        return {}

    '''async with session.get(request_url) as response:
        data = await response.json()
        #print(data)
        return data'''


def process_requests(api_requests):
    print('Processing requests...')

    # Initialize variable to hold responses
    responses = []

    # Capture starting time
    api_start = time.time()

    request_count = len(api_requests)
    print('Requests: {0}'.format(request_count))

    chunk_size = 25
    start = 0

    for start in range(0, request_count, chunk_size):
        end = start + chunk_size - 1
        if end > request_count:
            end = request_count - 1

        print('Processing chunk {0} - {1}'.format(str(start), str(end)))

        chunk = end - start + 1

        '''loop = asyncio.get_event_loop()
        future = asyncio.ensure_future(process_request_chunk(api_requests[start:(end+1)]))
        loop.run_until_complete(future)
        print(future)'''

        with futures.ThreadPoolExecutor(chunk) as executor:
            future_requests = {executor.submit(get_api_data, request): request for request in api_requests[start:(end + 1)]}

        for request in futures.as_completed(future_requests):
            response = request.result()
            #print(response)
            #print('\n')

            responses.append(request.result())

    # Capture finish time
    api_end = time.time()
    # Calculation API execution time
    api_duration = api_end - api_start
    print('API execution time: {0:.2f}s'.format(api_duration))

    return responses


def prepare_members(members):
    print('Preparing member data...')
    member_count = 0
    member_list = []

    for clan in members:

        members = clan['Response']['results']

        for member in members:
            record = {}
            record['clan_id'] = member['groupId']

            clanId = [member['groupId']]

            destiny_user_info = member['destinyUserInfo']
            destiny_user_info['iconPath'] = 'https://www.bungie.net{0}'.format(destiny_user_info['iconPath'])
            #del destiny_user_info['iconPath']

            record['destinyUserInfo'] = destiny_user_info
            #print(record)
            bungie_net_user_info = []

            if 'bungieNetUserInfo' in member.keys():
                bungie_net_user_info = member['bungieNetUserInfo']
                #del bungie_net_user_info['iconPath']
                bungie_net_user_info['iconPath'] = 'https://www.bungie.net{0}'.format(bungie_net_user_info['iconPath'])
                del bungie_net_user_info['supplementalDisplayName']

                record['bungieNetUserInfo'] = bungie_net_user_info

            member_list.append(record)
            member_count += 1

    return member_list


def prepare_characters(characters):
    print('Preparing character data...')

    character_list = []

    for character_response in characters:
        character_keys = character_response.keys()
        #character_keys = json.loads(character_response).keys()

        if 'Response' in character_keys:
            #profile = json.loads(character_response)['Response']['profile']
            profile = character_response['Response']['profile']
            versions_owned = profile['data']['versionsOwned']
            #print(versions_owned)
            #character_data = json.loads(character_response)['Response']['characters']['data']
            character_data = character_response['Response']['characters']['data']
            for character_id in character_data:
                #print(characterData[characterId])
                character = character_data[character_id]

                record = {}
                record['membershipId'] = character['membershipId']
                record['membershipType'] = character['membershipType']
                record['characterId'] = character['characterId']
                record['dateLastPlayed'] = character['dateLastPlayed']
                record['minutesPlayedTotal'] = character['minutesPlayedTotal']
                record['classHash'] = character['classHash']
                record['versionsOwned'] = versions_owned

                character_list.append(record)

    return character_list


def stage_members(db, members):
    print('Staging members...')
    inserts = 0

    for member in members:
        if len(member) == 3:
            sql = sql_config['longMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values()) + list(member['bungieNetUserInfo'].values())

        elif len(member) == 2:
            sql = sql_config['shortMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values())

        #print(record) # debugging
        pg_cursor = db.cursor()
        pg_cursor.execute(sql, record)
        inserts += pg_cursor.rowcount

    # Perform all inserts before issuing the commit
    db.commit()

    return inserts


def stage_characters(db, characters):
    print('Staging characters...')
    inserts = 0

    for character in characters:
        '''if len(member) == 3:
            sql = sqlConfig['longMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values()) + list(member['bungieNetUserInfo'].values())

        elif len(member) == 2:
            sql = sqlConfig['shortMemberInsert']
            record = [member['clan_id']] + list(member['destinyUserInfo'].values())'''

        pg_cursor = db.cursor()
        pg_cursor.execute(sql_config['characterInsert'], list(character.values()))
        inserts += pg_cursor.rowcount

    # Perform all inserts before issuing the commit
    db.commit()

    return inserts


def handler(event, context):

    start = time.time()

    # Open database connection
    pg = pg8000.connect(host=db_config['host'], port=db_config['port'], database=db_config['database'], user=db_config['user'], password=db_config['password'])
    # Provision a cursor that will used throughout script to perform database operations
    pg_cursor = pg.cursor()

    # Preprocessing
    pre_processing(pg_cursor)


    # Get the clans from database
    clans = execute_select(pg_cursor, sql_config['clanSelect'])

    # Build the clan API requests
    member_requests = build_requests(clans)

    # Get the clan members from API
    members = process_requests(member_requests)
    #print(members) # debugging

    # Prepare the data
    member_list = prepare_members(members)
    #print(json.dumps(member_list)) # debugging

    # Stage members
    member_inserts = stage_members(pg, member_list)
    print('Inserts: {0}'.format(member_inserts))

    # Build character requests
    character_requests = build_character_requests(member_list)

    # Get the characters from API
    characters = process_requests(character_requests)
    
    # Prepare the character data
    character_list = prepare_characters(characters)
    
    # Stage characters
    character_inserts = stage_characters(pg, character_list)
    print('Inserts: {0}'.format(character_inserts))

    # Close database connection
    pg.close()

    end = time.time()

    duration = end - start
    print('Duration: {0:.2f}s'.format(duration))

    # Prepare to return data
    counts = {}
    counts['counts'] = {'members': member_inserts, 'characters': character_inserts}
    return counts

