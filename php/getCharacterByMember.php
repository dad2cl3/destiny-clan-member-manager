<?php

include 'YOUR-SERVER-PATH/inc/db.inc';
include 'YOUR-SERVER-PATH/inc/api.inc';
include 'functions.php';

	$currentDate = date_create(date('Y-m-d'));

	$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);

	pg_query($dbconn, 'SELECT stg.prc_truncate_table(\'t_member_characters\')');

	$memberQuery = 'select distinct clan_id, destiny_id, destiny_name from ' .$dbstg. '.t_clan_members order by destiny_name, clan_id';
	
	$members = pg_fetch_all(pg_query($dbconn, $memberQuery));

	$inserts = 0;
	$counter = 0;
	$memberCount = count($members);
	
	//echo count($members). '</br>'; //Troubleshooting
	
	foreach ($members as $member) {
		$clanId = $member['clan_id'];
		$memberId = $member['destiny_id'];
		
		$url = $root. 'Destiny/2/Account/' .$memberId. '/Summary/';
		//echo $url. '</br>';
		$json = execute_curl($url, $apiKey);

		$characters = $json['Response']['data']['characters'];
		
		foreach ($characters as $character) {
			//print_r($character); //Troubleshooting
			$characterId = $character['characterBase']['characterId'];
			$classType = $character['characterBase']['classType'];
			$lastPlayed = $character['characterBase']['dateLastPlayed'];
			$minutesPlayed = $character['characterBase']['minutesPlayedTotal'];

			//echo $characterId. ' - ' .$classType. ' - ' .$lastPlayed. ' - ' .$minutesPlayed. '</br>';
			
			$values = '(';
			$values .= 'to_date(\'' .$currentDate->format('Y-m-d'). '\', \'YYYY-mm-dd\')';
			$values .= ',' .$clanId;
			$values .= ',' .$memberId;
			$values .= ',' .$characterId;
			$values .= ',' .$classType;
			$values .= ',to_date(\'' .substr($lastPlayed,0,10). '\', \'YYYY-mm-dd\')';
			$values .= ',' .$minutesPlayed;
			$values .= ')';
			
			//echo $values. '</br>';
			
			$insertSQL = 'INSERT INTO stg.t_member_characters VALUES ' .$values;
			//echo $insertSQL. '</br>';
			
			$result = pg_query($dbconn,$insertSQL);
			$inserts += pg_affected_rows($result);	
		}
		$counter++;
	}
	
	$result = '{"expected":"' .$memberCount. '","processed":"' .$counter. '"}';
	echo $result;
	
	close_db_connect($dbconn);
?>