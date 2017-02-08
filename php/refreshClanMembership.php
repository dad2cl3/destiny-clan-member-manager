<?php
include 'YOUR-SERVER-PATH/inc/api.inc';
include 'YOUR-SERVER-PATH/inc/db.inc';
include 'YOUR-SERVER-PATH/inc/slack.inc';
include 'functions.php';

	//$betaFlag = True;
	$betaFlag = false;
	
	if ($betaFlag) {
		$slackUrl = $dodioBetaUrl;
	} else {
		$slackUrl = $dodioAdminUrl;
	}

	//Open database connection
	$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);

	$url = 'YOUR-SERVER-PATH/getClanMembers.php';
	
	//Execute PHP script
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	//echo $url[$i]; //Troubleshooting
	$output = json_decode(curl_exec($ch), true);
	//var_dump($output); //Troubleshooting
	
	echo $output['expected']. '</br>';
	echo $output['processed']. '</br>';
	
	if ($output['expected'] == $output['processed']) {
		echo 'Processing...</br>';
	} else {
		echo 'Processing halted.</br>';
	}
	
	curl_close($ch);
	
	if ($output['expected'] == $output['processed']) {

		$url = 'YOUR-SERVER-PATH/getCharacterByMember.php';
		
		//Execute PHP script
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		//echo $url[$i]; //Troubleshooting
		$output = json_decode(curl_exec($ch), true);
		//var_dump($output); //Troubleshooting
		
		echo $output['expected']. '</br>';
		echo $output['processed']. '</br>';
		
		if ($output['expected'] == $output['processed']) {
			echo 'Processing...</br>';
		} else {
			echo 'Processing halted.</br>';
		}
		
		curl_close($ch);
		
		if ($output['expected'] == $output['processed']) {
		
			$url = 'YOUR-SERVER-PATH/stageSlackUserList.php';
			
		
			//Execute PHP script
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			//echo $url[$i]; //Troubleshooting
			$output = json_decode(curl_exec($ch), true);
			//var_dump($output); //Troubleshooting
			echo $output['expected']. '</br>';
			echo $output['processed']. '</br>';
			
			curl_close($ch);
			
			if ($output['expected'] == $output['processed']) {
				//Open database connection
				$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);
				//Process the Bungie data
				$clanResult = pg_query($dbconn, 'SELECT stg.prc_update_membership(CURRENT_DATE) output');
				$clanStaging = pg_fetch_all($clanResult);
				//var_dump($clanStaging); //Troubleshooting

				//Process the Slack data
				$slackResult = pg_query($dbconn, 'SELECT stg.prc_update_slack(CURRENT_DATE) output');
				$slackStaging = pg_fetch_all($slackResult);
				//var_dump($slackStaging); //Troubleshooting


				//Build the report
				//Post clan totals
				$clans = pg_query($dbconn, 'SELECT io.fn_get_clan_counts()');
				$clansResult = pg_fetch_all($clans);
				//var_dump($clansResult);
				
				$json = json_decode($clansResult[0]['fn_get_clan_counts'], true);
				
				//print_r($json); //Troubleshooting
				
				$total = $json['total'];
				$counts = $json['clans'][0];
				//print_r($json);
				//print_r($counts);
				$clanList = null;
				
				$message = '{"attachments":[{';
				
				foreach ($counts as $key => $count) {
						
						
					if (!(is_null($clanList))) {
						$clanList .= '_' .$key. ' - ' .$count. '_\n';
					} else {
						$clanList = '"text":"_' .$key. ' - ' .$count. '_\n';
					}
				
					//echo $key. ' - ' .$count. '</br>';
				}
				
				$message .= $clanList. '","pretext":"*_Iron Orange Membership - ' .$total. '_*","mrkdwn_in":["text","pretext"]}';
				//echo $message. '</br>'; //Troubleshooting
				//echo $apiKey. ' - ' .$slackUrl. '</br>';
				
				//$result = push_slack_message($message,$slackUrl);
				
				//echo 'Clan message - ' .$result. '</br>';
				
				//echo $message;
			
				//Push new members
				$new = pg_query($dbconn, 'SELECT io.fn_new_members(CURRENT_DATE)');
				$newResult = pg_fetch_all($new);
				
				//var_dump($newResult); //Troubleshooting
				
				$json = json_decode($newResult[0]['fn_new_members'], true);
				$new = null;
				
				if ($json['count'] > 0) {
					//print_r($json); //Troubleshooting
					foreach ($json['members'] as $member) {
						if (is_null($new)) {
							$new .= '"text":"' .$member;
						} else {
							$new .= '\n' .$member;
						}
					}
					//echo $new. '</br>'; //Troubleshooting
					
					//$message = '{"attachments":[{';
					$new .= '","pretext":"*_New Members_*","mrkdwn_in":["text","pretext"]}';
					//echo $message. '</br>'; //Troubleshooting
					
					//$result = push_slack_message($message, $slackUrl);
					
					//echo 'New members - ' .$result. '</br>';
				}

				//Push old members	
				$old = pg_query($dbconn, 'SELECT io.fn_deleted_members(CURRENT_DATE)');
				$oldResult = pg_fetch_all($old);
				//var_dump($oldResult); //Troubleshooting
				
				$json = json_decode($oldResult[0]['fn_deleted_members'], true);
				//print_r($json);
				$old = null;

				if ($json['count'] > 0) {
					//print_r($json); //Troubleshooting
					foreach ($json['members'] as $member) {
						if (is_null($old)) {
							$old .= '"text":"' .$member;
						} else {
							$old .= '\n' .$member;
						}
					}
					//echo $new. '</br>'; //Troubleshooting
					
					//$message = '{"attachments":[{';
					$old .= '","pretext":"*_Former Members_*","mrkdwn_in":["text","pretext"]}';
					//echo $message. '</br>'; //Troubleshooting
					
				}

				//Push slack differences
				$delta = pg_query($dbconn, 'SELECT io.fn_get_slack_deltas()');
				$deltaResult = pg_fetch_all($delta);
				//var_dump($deltaResult); //Troubleshooting
				
				$json = json_decode($deltaResult[0]['fn_get_slack_deltas'], true);
				
				$slack = null;
				$tag = null;
				
				//var_dump($json['results']);
				
				foreach ($json['results'] as $result) {
					//var_dump($result); //Troubleshooting
					
					if (array_key_exists('missing slack accounts', $result)) {
						if ($result['missing slack accounts'] > 0) {
							//Build members string
							foreach ($result['members'] as $member) {
								if (is_null($slack)) {
									$slack .= '"' .$member;
								} else {
									$slack .= '\n' .$member;
								}
							}
							$slack .= '"';
						}
					} elseif (array_key_exists('missing clan tags', $result)) {
						if ($result['missing clan tags'] > 0) {
							//Build members string
							foreach ($result['members'] as $member) {
								if (is_null($tag)) {
									$tag .= '"' .$member;
								} else {
									$tag .= '\n' .$member;
								}
							}
							$tag .= '"';
						}
					}
				}
				//echo $slack. '</br>'; //Troubleshooting
				//echo $tag. '</br>'; //Troubleshooting
				
				$attachments = '{"attachments":[';
				$pretext = '{"pretext":"';
				$posttext = ',"mrkdwn_in":["pretext"]}';
				$close = ']}';
				$separator = ',';
				
				if (!(is_null($tag)) && !(is_null($slack))) {
					$deltas = $pretext. '*_Missing Clan Tags_*","text":' .$tag.$posttext.$separator.$pretext. '*_Missing Slack Accounts_*","text":' .$slack.$posttext;
				} elseif (!(is_null($tag)) && is_null($slack)) {
					$deltas = $pretext. '*_Missing Slack Accounts_*","text":' .$slack.$posttext;
				} elseif (is_null($tag) && !(is_null($slack))) {
					$deltas = $pretext. '*_Missing Clan Tags_*","text":' .$tag.$posttext;
				}
				
				//echo $message. '</br>'; //Troubleshooting
				
				//$result = push_slack_message($message, $slackUrl);
				//echo 'Slack discrepancies - ' .$result. '</br>'; //Troubleshooting

				//Push inactive guardians
				$inactives = pg_query($dbconn, 'SELECT io.fn_get_inactive_data()');
				$inactivesResult = pg_fetch_all($inactives);
				//var_dump($inactivesResult); //Troubleshooting
				
				$json = json_decode($inactivesResult[0]['fn_get_inactive_data'], true);
				
				//var_dump($json);
				
				$inactive = null;
				
				foreach ($json['members'] as $member) {
					if (is_null($inactive)) {
						$inactive .= '"text":"' .$member;
					} else {
						$inactive .= '\n' .$member;
					}
				}
				
				//$message = '{"attachments":[{"pretext":"*_Inactive members_*","text":"' .$message. '","mrkdwn_in":["pretext"]}]}';
				$inactive .= '","pretext":"*_Inactive members_*", "mrkdwn_in":["pretext"]}';
				
				//$result = push_slack_message($message, $slackUrl);
				//echo 'Inactive guardians - ' .$result. '</br>'; //Troubleshooting
				
				//echo $message. '</br>';
				//echo $total. '</br>';
				//print_r($counts);

				//Close the database connection
				pg_close($dbconn);
				
				if (!(is_null($new))) {
					$message .= ',{' .$new;
				}
				
				if (!(is_null($old))) {
					$message .= ',{' .$old;
				}
				
				if (!(is_null($deltas))) {
					$message .= ',' .$deltas;
				}
				
				if (!(is_null($inactive))) {
					$message .= ',{' .$inactive;
				}
				
				$message = $message. ']}';
				echo $message. '</br>'; //Troubleshooting
				
				$result = push_slack_message($message, $slackUrl);
				echo $result. '</br>';
				

			} else {
				null;
			}
		}
	}

	//Build the guardian list for http://www.ironorange.org/guardians
	$url = 'YOUR-SERVER-PATH/buildGuardianList.php';
	//Execute PHP script
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$output = json_decode(curl_exec($ch), true);
	echo $output['expected']. '</br>';
	echo $output['processed']. '</br>';
	
	curl_close($ch);
?>