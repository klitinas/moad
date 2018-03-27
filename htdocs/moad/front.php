<html>

  <form action="checkbox-form.php" method="post">
    Assessment Names <br />
    <?php
    	// Query the db to populate list select with existing subjects
    	include 'database.class.php';

    	$db = new Database();
    	$str = "select distinct(Test_name) from flag;";
    	$db->query($str);
    	$result = $db->resultset();

      foreach ($result as $row) {
      $test = $row['Test_name'];
      echo "<input type='checkbox' name='formDoor[]' value='$test' />$test<br />";

      }

    ?>
    <input type="submit" name="formSubmit" value="Submit" />


</form>

</html>
