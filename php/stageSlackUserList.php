<?php
include 'YOUR-SERVER-PATH/inc/db.inc';
include 'YOUR-SERVER-PATH/inc/slack.inc';
include 'functions.php';

	//Set the default timezome
	date_default_timezone_set('UTC');
	//Capture effective date for data pull
	$currentDate = date_create(date('Y-m-d'));
	//echo 'Current date = ' .$currentDate->format('Y-m-d'). '</br>'; //Troubleshooting

	//Open database connection
	//echo $dbi. '</br>'; //Troubleshooting
	$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);

	//Truncate the account table
	//mysqli_query($dbconn, 'truncate table `' .$dbstg. '`.`slack_accounts`');
	pg_query($dbconn, 'select ' .$dbstg. '.prc_truncate_table(\'t_slack_accounts\')');

	$url = 'https://slack.com/api/users.list';

	//$ch = curl_init($url. '?token=' .$betaOAuthToken);
	$ch = curl_init($url. '?token=' .$OAuthToken);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	//echo curl_exec($ch); //Troubleshooting
	
	$json = json_decode(curl_exec($ch), true);
	curl_close($ch);
	
	$members = $json['members'];
	
	//echo 'Member count = ' .count($members). '<br/>'; //Troubleshooting
	
	$counter = 0;
	$inserts = 0;

	foreach ($members as $member)
 		if (!($member['deleted']) && !($member['name'] == 'slackbot')) {
 			//echo $member['name']. '</br>'; //Troubleshooting

 		$counter++;

 		$memberId = $member['id'];
 		$memberName = $member['name'];
 		//var_dump($member); //Troubleshooting

 		$insertSQL = 'insert into ' .$dbstg. '.t_slack_accounts values (';
 		$insertSQL .= 'to_date(\'' .$currentDate->format('Y-m-d'). '\', \'YYYY-mm-dd\')';
 		$insertSQL .= ',\'' .$memberId. '\',\'' .$memberName. '\')';

 		//echo $insertSQL. '<br/>'; //Troubleshooting

 		//mysqli_query($dbconn, $insertSQL);
 		$result = pg_query($dbconn, $insertSQL);
 		//$inserts += mysqli_affected_rows($dbconn);
 		$inserts += pg_affected_rows($result);
 		
 	//echo $member['id']. ' - ' .$member['name']. '<br/>'; //Troubleshooting

 	//$ch = curl_init('https://slack.com/api/users.getPresence?token=' .$OAuthToken. '&user=' .$member['id']);
 	//echo 'https://slack.com/api.users.getPresence?token=' .$OAuthToken. '&user=' .$member['id']. '<br/>';
 	//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 		
 	//$json = json_decode(curl_exec($ch), true);
 		
 	//echo $json['presence']. ' - ' .$json['lastActivity']. '<br/>';
 		
 	//var_dump($json);
 	//echo curl_error($ch). '<br/>';
 		
 	//curl_close($ch);
 	}

	//Close database connection
	close_db_connect($dbconn);

	$result = '{"expected":"' .$counter. '","processed":"' .$inserts. '"}';
	echo $result; //Troubleshooting
	//echo 'Member count = ' .$counter. '<br/>';
	//echo 'Insert count = ' .$inserts. '<br/>';
 ?>