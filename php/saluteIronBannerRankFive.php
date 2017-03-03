<?php

include 'YOUR-PATH-HERE/inc/api.inc';
include 'YOUR-PATH-HERE/inc/db.inc';
include 'YOUR-PATH-HERE/inc/slack.inc';
include 'functions.php';

	$betaFlag = false;
	
	if ($betaFlag) {
		$slackUrl = $dodioBetaUrl;
	} else {
		$slackUrl = $dodioUrl;
	}
	//Open database connection
	$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);
	
	$notifyQuery = 'SELECT DISTINCT CURRENT_DATE effective_date, tm.destiny_id, vsdx.slack_name
		FROM ' .$dbprod. '.t_members tm, ' .$dbprod. '.vw_slack_destiny_xref vsdx, ' .$dbstg. '.t_iron_banner_rank tibr
		WHERE UPPER(tm.destiny_name) = UPPER(vsdx.destiny_name)
		AND tm.destiny_id = tibr.destiny_id
		AND tm.deleted IS NULL
		AND tibr.current_rank = 5
		AND NOT EXISTS (
				SELECT \'x\'
				FROM ' .$dbprod. '.t_iron_banner_rank
				WHERE tm.destiny_id = t_iron_banner_rank.destiny_id)
		ORDER BY vsdx.slack_name';
	
	echo $notifyQuery. '</br>';

	$queryResults = pg_query($dbconn, $notifyQuery);
	$members = pg_fetch_all($queryResults);
	
	$memberString = NULL;
	$sqlArray = NULL;
	$i = 0;
	
	foreach ($members as $member) {
		if ($memberString == NULL) {
			$memberString = '<@' .$member['slack_name']. '>';
		} else {
			$memberString .= '\n<@' .$member['slack_name']. '>';
		}
		$sqlArray[$i] = 'INSERT INTO ' .$dbprod.'.t_iron_banner_rank VALUES (CURRENT_DATE, ' .$member['destiny_id']. ')';
		$i++;
	}
	
	//echo 'Member string ' .$memberString. '</br>'; //Troubleshooting
	
	$preText = '{"channel":"#announcements","text":"_Congratulations guardian(s) for achieving rank 5 in Iron Banner_","attachments":[{"text":"';
	$postText = '","mrkdwn_in":["pretext"]}]}';
	
	$message = $preText.$memberString.$postText;
	echo $message. '</br>';
	
	if ($memberString != NULL) {
		$result = push_slack_message($message,$slackUrl);

			if ($result == 'ok') {
				$sqlCount = count($sqlArray);
				
				for ($i=0;$i<$sqlCount;$i++) {
					echo $sqlArray[$i]. '</br>';
					pg_query($dbconn, $sqlArray[$i]);
				}
			}
			
			if($result === false) {
			    echo 'Curl error: ' . curl_error($ch);
			}
	 
			curl_close($ch);
	}
	
	close_db_connect($dbconn);
?>