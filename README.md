# destiny-clan-member-manager
Database and web server scripts for managing Destiny clan membership within a Postgres database

Database setup

Documentation on setting up Postgres can be found at https://www.postgresql.org.

The clan member data is managed in three schemas: stg, io, and archive.

The stg schema is where the raw data from the bungie.net and Slack API calls is loaded.

The io (stands for Iron Orange which is the best Destiny clan :-)) schema is where the normalized data is stored after all daily updates are complete.

The archive schema is where the denormalized daily membership data is stored after all of the daily updates are complete.

All database scripts are located in the sql folder.

Slack setup

The integration with Slack utilizes an incoming webhook for the Slack team that our clan uses. Documentation on setting up a Slack team and creating and managing Slack integrations can be found at https://www.slack.com and https://api.slack.com

Bungie.net

The API calls to bungie.net require a developer API key. The key can be acquired by visiting https://www.bungie.net/en/Application and documentation of the APIs can be found at http://destinydevs.github.io/BungieNetPlatform/
