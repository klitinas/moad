<!DOCTYPE html>
<html>
<head>

</head>
<body>


	<?php

	// Get a list of visits (dates + labels) for subject
	$subjCode = $_GET['q'];
	//print_r(get_defined_vars());
	$db = new PDO('mysql:host=141.214.246.201;dbname=loris_prod;charset=utf8mb4', 'lorisuser', 'lorisuser');
	//$str = "select Visit_label,Date_visit from session where CandID=(select CandID from parameter_candidate where Value like '%%%s%%');";
	$str = "select c.CandID,c.PSCID,s.ID,s.Visit_label,s.Date_visit from session s
	JOIN candidate c on c.CandID=s.CandID where
	c.CandID=(select CandID from parameter_candidate where Value like '%%%s%%')";


	$sql = sprintf($str,$subjCode);

	$stmt = $db->query($sql);
	$stmt->setFetchMode(PDO::FETCH_ASSOC);

	$visits = array();
	foreach ($stmt as $row) {
		$date_visit = $row['Date_visit'];
		$visit_label = $row['Visit_label'];

		$visit_id = $row['ID'];
		$CandID = $row['CandID'];
		$PSCID = $row['PSCID'];

		$pat = $CandID . $PSCID . $visit_id + "'%s'";
		$cmd = sprintf("select codelist from visit where CommentID like '%s%%'",$pat);

		$stmt2 = $db->query($cmd);
		$stmt2->setFetchMode(PDO::FETCH_ASSOC);
		foreach ($stmt2 as $row) {
			$codes = $row['codelist'];

			// Include it in the list if it's for the study
			$pos = strpos($codes,$subjCode);

			if($pos !== false) {
				$visits = array_merge(array(sprintf('%s: %s (Associated Subject ID(s): %s)',$visit_label,$date_visit,$codes)),$visits);
			}


		}

	};
	sort($visits);





	?>

	<br></br>
	<select name="visit">
		<option value="">--Select a visit--</option>
		<?php foreach ($visits as $v) {
			echo '<option value="'.$v.'">'.$v.'</option>';
		}?>
	</select>





</body>
</html>
