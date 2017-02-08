<?php
include 'functions.php';
include 'YOUR-SERVER-PATH/inc/db.inc';
include 'YOUR-SERVER-PATH/inc/server.inc';

	$listLimit = 50;
	$i = 0;

	date_default_timezone_set('America/New_York');
	$expected = 0;
	$processed = 0;

	//Open database connection
	//echo $dbhost. '</br>'; //Troubleshooting
	$dbconn = open_db_connect($dbhost, $dbport, $dbi, $dbuser, $dbpwd);
	
	//echo $dataDir. '</br>';
	
	//Open file
	$filename = 'guardian_list.html';
	
	/*$filename1 = 'guardian_list1.html';
	$filename2 = 'guardian_list2.html';
	$filename3 = 'guardian_list3.html';
	$filename4 = 'guardian_list4.html';
	
	$handle1 = fopen($dataDir.$filename1, "w+");
	$handle2 = fopen($dataDir.$filename2, "w+");
	$handle3 = fopen($dataDir.$filename3, "w+");
	$handle4 = fopen($dataDir.$filename4, "w+");
	*/
	$handle = fopen($dataDir.$filename, 'w+');
	
	//Add the HTML header
	//echo '<html><table>';
	//fwrite($handle1, "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"http://ironorange.org/wp-content/themes/Divi-Child/iostyle.css\"></head>");
/*
	fwrite($handle1, "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"http://ironorange.org/wp-content/themes/Divi-Child/iostyle.css\"></head>");
	fwrite($handle2, "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"http://ironorange.org/wp-content/themes/Divi-Child/iostyle.css\"></head>");
	fwrite($handle3, "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"http://ironorange.org/wp-content/themes/Divi-Child/iostyle.css\"></head>");
	fwrite($handle4, "<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"http://ironorange.org/wp-content/themes/Divi-Child/iostyle.css\"></head>");
*/
	//fwrite($handle1, "<html><head>");
	fwrite($handle, '<html><link rel="stylesheet" href="http://ironorange.org/wp-includes/ironcrew.css" type="text/css" media="all"><head>');
	
	//Old style tag
	/*
	$styleTag = "a {color: #da8327;text-decoration: none}";
	//$styleTag .= "table {background-color:#000000};";
	$styleTag .= " span.listheader {color: black;font-family:\"Open Sans\",Arial,sans-serif;font-size:14px;font-weight:lighter}";
	$styleTag .= " span.listentry {font-family:\"Open Sans\",Arial,sans-serif;font-size:14px;font-weight:lighter}";
	
	fwrite($handle, '<style>' .$styleTag. '</style></head>');
	*/
	/*fwrite($handle1,"<style>" .$styleTag. "</style>");
	fwrite($handle1,"</head>");
	
	fwrite($handle2, "<html><head><style>" .$styleTag. "</style></head>");
	fwrite($handle3, "<html><head><style>" .$styleTag. "</style></head>");
	fwrite($handle4, "<html><head><style>" .$styleTag. "</style></head>");
	
	fwrite($handle1,'<table>');
	fwrite($handle2,'<table>');
	fwrite($handle3,'<table>');
	fwrite($handle4,'<table>');*/
	fwrite($handle, '<table border="1"');

	$listSQL = 'select * from ' .$dbprod. '.vw_guardian_list';
	//echo $listSQL. '</br>';

	$listQuery = pg_query($dbconn, $listSQL);
	$guardians = pg_fetch_all($listQuery);
	$expected = count($guardians);
	//var_dump($guardians);
	
	//Labels
	$col1aLabel = 'Founders';
	$col1bLabel = 'Admins';
	$col1cLabel = 'In Memory';
	$col2Label = 'A-F';
	$col3Label = 'G-N';
	$col4Label = 'O-Z';
	
	//Initialize holding strings
	$group = '';
	$col1a = '';
	$col1b = '';
	$col1c = '';
	$col2 = '';
	$col3 = '';
	$col4 = '';
	
	//echo '<html><table>';
	//while ($guardian = mysqli_fetch_assoc($query)) {
	foreach ($guardians as $guardian) {
		$processed++;

		//echo $guardian['grouping']. ' - ' .$guardian['profile']. ' - ' .$guardian['psndisplayname']. '</br>'; //Troubleshooting
		//echo substr($guardian['Grouping'], 3). '</br>';
		if (substr($guardian['grouping'], 3) == 'Founders') {
			$col1a .= '<a href="' .$guardian['profile']. '" target="_blank">' .$guardian['psndisplayname']. '</a></br>';
		} elseif (substr($guardian['grouping'], 3) == 'Admins') {
			$col1b .= '<a href="' .$guardian['profile']. '" target="_blank">' .$guardian['psndisplayname']. '</a></br>';
		} elseif (substr($guardian['grouping'], 3) == 'In Memory') {
			$col1c.= '<a href="' .$guardian['profile']. '" target="_blank">' .$guardian['psndisplayname']. '</a></br>';
		} elseif (substr($guardian['grouping'], 3) == 'A-F') {
			$col2 .= '<a href="' .$guardian['profile']. '" target="_blank">' .$guardian['psndisplayname']. '</a></br>';
		} elseif (substr($guardian['grouping'], 3) == 'G-N') {
			$col3 .= '<a href="' .$guardian['profile']. '" target="_blank">' .$guardian['psndisplayname']. '</a></br>';
		} elseif (substr($guardian['grouping'], 3) == 'O-Z') {
			$col4 .= '<a href="' .$guardian['profile']. '" target="_blank">' .$guardian['psndisplayname']. '</a></br>';
		}
	}

	//fwrite($handle, '<tr>');
	//echo '<tr>';
	//echo '<td width="25%" valign="top">' .$col1aLabel. '</br>' .$col1a.$col1bLabel. '</br>' .$col1b.$col1cLabel. '</br>' .$col1c. '</td>';
	//echo '<td width="25%" valign="top">' .$col2Label. '</br>' .$col2. '</td>';
	//echo '<td width="25%" valign="top">' .$col3Label. '</br>' .$col3. '</td>';
	//echo '<td width="25%" valign="top">' .$col4Label. '</br>' .$col4. '</td>';
/*
	fwrite($handle, '<td width="25%" valign="top">' .$col1aLabel. '</br>' .$col1a.$col1bLabel. '</br>' .$col1b.$col1cLabel. '</br>' .$col1c. '</td>');
	fwrite($handle, '<td width="25%" valign="top">' .$col2Label. '</br>' .$col2. '</td>');
	fwrite($handle, '<td width="25%" valign="top">' .$col3Label. '</br>' .$col3. '</td>');
	fwrite($handle, '<td width="25%" valign="top">' .$col4Label. '</br>' .$col4. '</td>');
*/
/*
	fwrite($handle1, '<div><p>' .$col1aLabel. '</br>' .$col1a. '</p><p>' .$col1bLabel. '</br>' .$col1b. '</p><p>' .$col1cLabel. '</br>' .$col1c. '</p></div>');
	fwrite($handle2, '<div><p>' .$col2Label. '</br>' .$col2. '</p></div>');
	fwrite($handle3, '<div><p>' .$col3Label. '</br>' .$col3. '</p></div>');
	fwrite($handle4, '<div><p>' .$col4Label. '</br>' .$col4. '</p></div>');
*/
	/*fwrite($handle1, '<td><span class="listheader">' .$col1aLabel. '</span></br><span class="listentry">' .$col1a. '</span></br><span class="listheader">' .$col1bLabel. '</span></br><span class="listentry">' .$col1b. '</span></br><span class="listheader">' .$col1cLabel. '</span></br><span class="listentry">' .$col1c. '</span></td>');
	fwrite($handle2, '<td><span class="listheader">' .$col2Label. '</span></br><span class="listentry">' .$col2. '</span></td>');
	fwrite($handle3, '<td><span class="listheader">' .$col3Label. '</span></br><span class="listentry">' .$col3. '</span></td>');
	fwrite($handle4, '<td><span class="listheader">' .$col4Label. '</span></br><span class="listentry">' .$col4. '</span></td>');
	fwrite($handle, '</tr>');*/
	
	//Option 1
	/*
	$header = '<tr><td>';
	$header .= '<span class="listheader">' .$col1aLabel. '</span></br><span class="listentry">' .$col1a. '</span></br>';
	$header .= '<span class="listheader">' .$col1bLabel. '</span></br><span class="listentry">' .$col1b. '</span></br>';
	$header .= '<span class="listheader">' .$col1cLabel. '</span></br><span class="listentry">' .$col1c. '</span></td>';
	$header .= '<td><span class="listheader">' .$col2Label. '</span></td>';
	$header .= '<td><span class="listheader">' .$col3Label. '</span></td>';
	$header .= '<td><span class="listheader">' .$col4Label. '</span></td>';
	$header .= '</tr>';
	$header .= '<tr><td></td>';
	$header .= '<td><span class="listentry">' .$col2. '</span></td>';
	$header .= '<td><span class="listentry">' .$col3. '</span></td>';
	$header .= '<td><span class="listentry">' .$col4. '</span></td>';
	*/
	
	//Option 2
	//Header row
	/*
	$header = '<tr><td></td>';
	$header .= '<td><span class="listheader">' .$col2Label. '</span></td>';
	$header .= '<td><span class="listheader">' .$col3Label. '</span></td>';
	$header .= '<td><span class="listheader">' .$col4Label. '</span></td>';
	$header .= '</tr>';
	//Data row
	$header .= '<tr><td>';
	$header .= '<span class="listheader">' .$col1aLabel. '</span></br><span class="listentry">' .$col1a. '</span></br>';
	$header .= '<span class="listheader">' .$col1bLabel. '</span></br><span class="listentry">' .$col1b. '</span></br>';
	$header .= '<span class="listheader">' .$col1cLabel. '</span></br><span class="listentry">' .$col1c. '</span></td>';
	$header .= '<td><span class="listentry">' .$col2. '</span></td>';
	$header .= '<td><span class="listentry">' .$col3. '</span></td>';
	$header .= '<td><span class="listentry">' .$col4. '</span></td>';
	*/
	//Option 3
	//Header row
	$header = '<tr>';
	$header .= '<td align="center"><span class="listheader">' .$col1aLabel. '</span></td>';
	$header .= '<td align="center"><span class="listheader">' .$col2Label. '</span></td>';
	$header .= '<td align="center"><span class="listheader">' .$col3Label. '</span></td>';
	$header .= '<td align="center"><span class="listheader">' .$col4Label. '</span></td>';
	$header .= '</tr>';	
	//Data row
	$header .= '<tr>';
	$header .= '<td><span class="listentry">' .$col1a. '</span></br>';
	$header .= '<span class="listheader">' .$col1bLabel. '</span></br><span class="listentry">' .$col1b. '</span></br>';
	$header .= '<span class="listheader">' .$col1cLabel. '</span></br><span class="listentry">' .$col1c. '</span></td>';
	$header .= '<td><span class="listentry">' .$col2. '</span></td>';
	$header .= '<td><span class="listentry">' .$col3. '</span></td>';
	$header .= '<td><span class="listentry">' .$col4. '</span></td>';
	
	fwrite($handle, $header);
	fwrite($handle, '</tr>');
	
	
	
	//echo '</tr>';
	
	//echo '<tr><td>' .$col2. '</td></tr>';
	
		//Create a new row
		/*
		fwrite($handle, '<tr>');
		fwrite($handle,'<td><a href="' .$guardian['Profile']. '" target="_blank">' .$guardian['psnDisplayName']. '</a></td>');
		fwrite($handle,'<td>' .$guardian['Grouping']. '</td></tr>');
		*/
/*
		if ($group == '') {
			//echo '<th>' .substr($guardian['Grouping'], 2). '</th>';
			fwrite ($handle, '<th>' .substr($guardian['Grouping'], 2). '</th>');
			$group = $guardian['Grouping'];
		} elseif ($group != $guardian['Grouping']) {
			//echo '<th>' .substr($guardian['Grouping'], 2). '</th>';
			fwrite ($handle, '<th>' .substr($guardian['Grouping'], 2). '</th>');
			$group = $guardian['Grouping'];
		}
		
		//echo '<tr><td><a href="' .$guardian['Profile']. '">' .$guardian['psnDisplayName']. '</a></td></tr>';
		//fwrite ($handle, '<tr><td><a href="' .$guardian['Profile']. '">' .$guardian['psnDisplayName']. '</a></td></tr>');
	}
*/
	//echo '</table></html>';
	//Close out table and HTML tags
	fwrite($handle, "</table></html>");
	
	/*fwrite($handle1, '</table>');
	fwrite($handle2, '</table>');
	fwrite($handle3, '</table>');
	fwrite($handle4, '</table>');
	
	fwrite($handle1, '</html>');
	fwrite($handle2, '</html>');
	fwrite($handle3, '</html>');
	fwrite($handle4, '</html>');

	//Close file
	fclose($handle1);
	fclose($handle2);
	fclose($handle3);
	fclose($handle4);*/
	fclose($handle);

	close_db_connect($dbconn);
	
	$output = '{"expected":"' .$expected. '","processed":"' .$processed. '"}';
	echo $output;
?>