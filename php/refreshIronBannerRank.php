<?php

include 'YOUR-PATH-HERE/inc/db.inc';
include 'YOUR-PATH-HERE/inc/api.inc';
include 'functions.php';

	set_time_limit(240);

	$url = array(0=>'getIronBannerRankByCharacter.php',
			1=>'saluteIronBannerRankFive.php');
			
	for ($i=0;$i<=1;$i++) {
		echo basename($url[$i]). '</br>'; //Troubleshooting

		//Execute PHP script
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://YOUR-PATH_HERE/' .$url[$i]);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$output = curl_exec($ch);
		echo $output;
		curl_close($ch);
	}
?>