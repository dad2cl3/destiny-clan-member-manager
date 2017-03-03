<?php

include 'YOUR-PATH-HERE/inc/api.inc';
include 'YOUR-PATH-HERE/inc/db.inc';
include 'functions.php';

function insert_ib_rank($dbconn, $db, $memberId, $characterId, $hash, $level) {

	if ($hash == '2161005788') {
		$insertSQL = 'insert into ' .$db. '.t_iron_banner_rank values (' .$memberId. ',' .$characterId. ',' .$level. ', CURRENT_DATE)';
		//echo $insertSQL. '</br>'; //Troubleshooting

		pg_query($dbconn, $insertSQL);
	}
}

	$startTime = TIME();
	
	$queryLimit = 25;
	$loopCounter = 0;

	//Open database connection
	$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);

	//Truncate the staging table that stores Iron Banner rank
	$truncateQuery = 'SELECT ' .$dbstg. '.prc_truncate_table(\'t_iron_banner_rank\')';
	
	pg_query($dbconn, $truncateQuery);
	
	//Execute query to retrieve characters
	$characterQuery = 'SELECT tm.destiny_id, tc.character_id
		FROM ' .$dbprod. '.t_members tm, ' .$dbprod. '.t_characters tc, ' .$dbprod. '.t_member_characters tmc
		WHERE tm.destiny_id = tmc.destiny_id
		AND tmc.character_id = tc.character_id
		AND tm.deleted IS NULL AND tc.deleted IS NULL
		AND NOT EXISTS (
				SELECT \'x\'
				FROM io.t_iron_banner_rank tibr
				WHERE tm.destiny_id = tibr.destiny_id)
		ORDER BY tm.destiny_id, tc.character_id';

	//echo $characterQuery. '</br>'; //Troubleshooting
	
	//Retrieve query results
	$characterResults = pg_query($dbconn, $characterQuery);
	$characters = pg_fetch_all($characterResults);
	//echo count($characters). '</br>'; //Troubleshooting
	
	foreach ($characters as $character) {
		$loopCounter++;

		$memberId = $character['destiny_id'];
		//echo $memberId; //Troubleshooting
		$characterId = $character['character_id'];

		$ch = curl_init($root. 'Destiny/2/Account/' .$memberId. '/Character/' .$characterId .'/Progression/?definitions=false');

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-Key:' . $apiKey));

		$json = json_decode(curl_exec($ch), true);

		if (array_key_exists('Response', $json)) {
			$progArray = $json['Response']['data']['progressions'];

			foreach($progArray as $value)
				//echo $characterId. ' - ' .$value['level']. '</br>'; //Troubleshooting
				insert_ib_rank($dbconn, $dbstg, $memberId, $characterId, $value['progressionHash'], $value['level']);
		} else {
			echo $memberId. ' - ' .$characterId. ' - ' .$json['ErrorStatus']. '<br/>';
		}
		set_time_limit(120);
	}

	echo $loopCounter. '</br>';

	curl_close($ch);
	
	close_db_connect($dbconn);
?>