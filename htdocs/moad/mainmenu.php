<html>

<!-- Button to launch query_sessions.php page to show aggregate info for all sessions in DB. -->
<b>Session Info</b>
<p></p>

   
<input type="button" style="height:30px;width:200px;font-size:20px" value="Aggregate Sessions" class="homebutton" id="btnSessions" 
onClick="document.location.href='query_sessions.php'" />
<p></p>
 <!--------------------------------------------------------->


<hr>
<b>Op Info</b>
<p></p>

<!-- Button to show Ops for all subjects in the DB, redirects to query_ops.php page with no input args-->
All Subjects:
<input type="button" style="height:30px;width:200px;font-size:20px" value="Show Ops" class="homebutton" id="btnOps" 
onClick="document.location.href='query_ops.php'" />

<!--------------------------------------------------------->

<p></p>


<!-- List select to show Ops for one subjects in the DB, redirects to query_ops.php page subject name in string (e.g. query_ops/?SUBJECTNAME)-->
Select individual subject:
<form action= ""  method= 'post'>

    <input type="hidden" name="action" value="submit" />
    <select name='formSubject'>
        <option value=''>Select...</option>  


<?php
	// Query the db to populate list select with existing subjects
	include 'database.class.php';

	$db = new Database();
	$str = "select subjectCode from Subject";
	$db->query($str);
	$result = $db->resultset();

	echo "<p>";
	echo "Select a Subject:";

	// Create the list select box.
	foreach($result as $row)
	{
		$s = $row['subjectCode'];
    	echo '<option value="'.$s.'">'.$s.'</option>'; 
	}

	echo "</select>";
    echo "<input type= 'submit' name= 'test' value= 'View Op Details'/> ";
	echo "</form>";

	// Assign the redirect.
	if (isset($_POST['formSubject'])) {
    	$subj = $_POST['formSubject'];
    	header("Location: query_ops.php/?$subj");

	}
?>
<!--------------------------------------------------------->

<p></p>  
</html>