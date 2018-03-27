<?php

include 'database.class.php';
$db = new Database();

# Loop through all tests (in test_names)
$db->query("select Test_name from test_names where Test_name not in('cdr','rhino');");

$arrTests = $db->resultset();

foreach ($arrTests as $r ) {
  $test = $r['Test_name'];

  # look for cids in flag table
  $str = sprintf("select CommentID from flag where Test_name = '%s' and CommentID like 'DDE_%%'",$test);
  $db->query($str);
  $records = $db->resultset();

  # for each DDE in flag, check that it's in the test table
  print_r($test);
  foreach ($records as $rec) {
    $q = sprintf("select count(*) as cnt from %s where CommentID='%s'",$test,$rec['CommentID']);
    $db->query($q);
    $cnt = $db->single();
    $cnt = $cnt['cnt'];

    # If no record, create a row in test table
    if ($cnt == 0) {
      $cmd = sprintf("insert into %s (`CommentID`) values ('%s')",$test,$rec['CommentID']);

      //$cmd = sprintf("update flag set Data_entry = 'Imported' where CommentID=:cid");
      //$db->query($cmd);
      //$db->execute($cmd);

      echo($cmd);
    }
  }

}

?>
