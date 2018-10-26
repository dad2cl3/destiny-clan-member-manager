# destiny-clan-member-manager

Python scripts to perform a daily pull of Destiny clan membership for a specified clan or group of clans. The scripts identify changes that may or may not have occurred since the previous execution and apply the changes to the base data.

#### Database setup

Documentation on setting up PostgreSQL can be found on the PostgreSQL [home page](https://www.postgresql.org).

The clan member data is managed in two schemas: staging, and groups.

The staging schema is where the raw data from the bungie.net API is bulk loaded daily.

The groups schema is where the normalized data is stored after all daily updates are applied.

All database scripts are located in the [sql](https://github.com/dad2cl3/destiny-clan-member-manager/tree/master/sql) folder. (Maybe...:-)

#### Python

Two Python scripts are utilized to handle the data with assistance from a single PostgreSQL stored function. The Python script [stage_clan_members.py](https://github.com/dad2cl3/destiny-clan-member-manager/blob/master/py/lambda/stage_clan_members.py) retrieves the clans for which membership updates are collected. The Python script [load_clan_members.py](https://github.com/dad2cl3/destiny-clan-member-manager/blob/master/py/lambda/load_clan_members.py) calls the stored PostgreSQL function [prc_update_membership](https://github.com/dad2cl3/destiny-clan-member-manager/blob/master/sql/staging/create_function-prc_update_membership.sql) in order to identify and apply changes to the base database tables. 
#### Discord

The integration with Discord utilizes a webhook for the Discord server that our clan uses.

#### Bungie.net

The API calls to bungie.net require a developer API key. The key can be acquired by visiting [bungie.net](https://www.bungie.net/en/Application) and bungie.net API documentation can be found [here](https://github.com/Bungie-net/api).

###Amazon Web Services

AWS is leveraged to manage the processing and storage of the data. **Amazon RDS** is utilized to host the PostgreSQL database. **AWS Lambda** is utilized to execute the backend processing that stages and loads the database. Lastly, **AWS Step Functions** is used to orchestrate the daily retrieval and processing of clan data.

AWS is **NOT** required to host the processing. A simple VPS from a provider such as [Vultr](https://www.vultr.com) could be utilized. The VPS would require the installation and build out of a PostgreSQL database server and instance to store the data. CRON jobs would work well to replace AWS Step Functions for orchestration and execution of the Python scripts.

Some very small changes to the Python scripts would be necessary to allow them to function properly on their own. Within each Python script, there is a function defined named *handler*. The *handler* function would need to be removed and the code within would need to be executed directly.
