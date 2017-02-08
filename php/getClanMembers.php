<?php
include 'YOUR-SERVER-PATH/inc/api.inc';
include 'YOUR-SERVER-PATH/inc/db.inc';
include 'functions.php';

	//Capture current date to log counts
	$currentDate = date_create(date('Y-m-d'));
	//echo 'Current date = ' .$currentDate->format('Y-m-d'). '</br>';

	//Open database connection
	$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);
	//Remove clan member counts for current day if script has run previously
	//$deleteSQL = 'delete from ' .$dbstg. '.daily_clan_counts where date = str_to_date("' .$currentDate->format('Y-m-d'). '", "%Y-%m-%d")';
	//$deleteSQL = 'delete from ' .$dbstg. '.t_daily_clan_counts where effective_date = to_date(\'' .$currentDate->format('Y-m-d'). '\', \'YYYY-mm-dd\')';
	//echo $deleteSQL. '</br>'; //Troubleshooting
	//mysqli_query($dbconn, $deleteSQL);
	//pg_query($dbconn, $deleteSQL);
	
	//echo 'Database error ' .pg_last_error($dbconn). '</br>';
	
	pg_query($dbconn, 'SELECT stg.prc_truncate_table(\'t_clan_members\')');

	//Retrieve clans
	$clans = get_clans($dbconn, $dbprod);
	$clanCount = 0;
	
	//echo 'Clan count = ' .count($clans). '</br>';
	$memberCount = 0;
	$clanArray = '[{';
	foreach ($clans as $clan) {
		//Retrieve the current clan member count
		$url = $root. 'Group/' .$clan. '/';
		//echo $url. '</br>'; //Troubleshooting
		
		$json = execute_curl($url, $apiKey);
		//var_dump($json); //Troubleshooting
		//var_dump($json['Response']['clanMembershipTypes']); //Troubleshooting
		$clanKey = '"' .$json['Response']['clanMembershipTypes'][0]['clanName']. '"';
		$clanValue = '"' .$json['Response']['clanMembershipTypes'][0]['memberCount']. '"';
		
		if (!($clanArray == '[{')) {
			$clanArray .= ',';
		}
		
		$clanArray .= $clanKey. ':' .$clanValue;
		
		$clanCount += $json['Response']['clanMembershipTypes'][0]['memberCount'];
		//echo $json['Response']['clanMembershipTypes'][0]['memberCount']. '</br>'; //Troubleshooting
		
		//Insert the date, clan, count
		//$countSQL = 'insert into ' .$dbstg. '.daily_clan_counts (date, clan, members) values (str_to_date("' .$currentDate->format('Y-m-d'). '", "%Y-%m-%d"), ' .$clan. ', ' .$clanCount. ')';
		//$countSQL = 'insert into ' .$dbstg. '.t_daily_clan_counts (effective_date, clan, members) values (to_date(\'' .$currentDate->format('Y-m-d'). '\', \'YYYY-mm-dd\'), ' .$clan. ', ' .$clanCount. ')';
		//echo $countSQL. '</br>'; //Troubleshooting
		//mysqli_query($dbconn, $countSQL);
		//pg_query($dbconn, $countSQL);

		//Retrieve the clan members
		$i = 1;
		$moreResults = true;
		$insertCount = 0;
		
		do {
			$url = $root. 'Group/' .$clan. '/ClanMembers/?platformType=2&currentPage=' .$i;
			//echo $url. '</br>';
			$json = execute_curl($url, $apiKey);
		
			$results = $json['Response']['results'];
		
			foreach ($results as $result) {
				//var_dump($result); //Troubleshooting
				$destinyMemberId = $result['destinyUserInfo']['membershipId'];
				$destinyMemberName = $result['destinyUserInfo']['displayName'];
				$bungieMemberId = $result['bungieNetUserInfo']['membershipId'];
				$bungieMemberName = $result['bungieNetUserInfo']['displayName'];
				$approvalDate = $result['approvalDate'];
				//echo $approvalDate. '</br>'; //Troubleshooting
				
				$insertSQL = 'INSERT INTO ' .$dbstg. '.t_clan_members VALUES (';
				$insertSQL .= 'to_date(\'' .$currentDate->format('Y-m-d'). '\', \'YYYY-mm-dd\')';
				$insertSQL .= ',' .$clan;
				
				if ($bungieMemberId == NULL) {
					$insertSQL .= ', NULL';
				} else {
					$insertSQL .= ',' .$bungieMemberId;
				}
				
				if ($bungieMemberName == NULL) {
					$insertSQL .= ', NULL';
				} else {
					$insertSQL .= ',\'' .$bungieMemberName. '\'';
				}
				
				$insertSQL .= ',' .$destinyMemberId;
				$insertSQL .= ',\'' .$destinyMemberName;
				$insertSQL .= '\',to_date(\'' .substr($approvalDate, 0, 10). '\', \'YYYY-mm-dd\')';
				$insertSQL .= ')';
				
				//echo $insertSQL. '</br>'; //Troubleshooting
				$result = pg_query($dbconn, $insertSQL);
				
				$insertCount += pg_affected_rows($result);
			}
			
			$moreResults = $json['Response']['hasMore'];
			$i++;
		} while ($moreResults == true);
		$memberCount += $insertCount;
	}
	pg_close($dbconn);
	
	$clanArray .= '}]';
	echo '{"clans":';
	echo $clanArray;
	echo ',"expected":"' .$clanCount. '","processed":"' .$memberCount. '"}';
?>
