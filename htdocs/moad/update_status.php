<?php

  include 'database.class.php';
  $db = new Database();

  # Loop through all tests (in test_names)
  $db->query("select Test_name from test_names where Test_name not in('cdr','rhino');");
  #$db->query("select Test_name from test_names where Test_name in ('epworth','nback');");

  $arrTests = $db->resultset();

  foreach ($arrTests as $r ) {
    $test = $r['Test_name'];

    $str = sprintf("select * from %s where CommentID not like 'DDE_%%'",$test);
    $db->query($str);
    $records = $db->resultset();

    print_r("\n");
    print_r($test);
    foreach ($records as $rec) {

      if (in_array($rec['Examiner'],array(NULL,1))) {
        $recTrim = $rec;
        unset($recTrim['CommentID']);
        unset($recTrim['UserID']);
        unset($recTrim['Examiner']);
        unset($recTrim['Testdate']);
        unset($recTrim['Data_entry_completion_status']);
        unset($recTrim['Date_taken']);
        unset($recTrim['Candidate_Age']);
        unset($recTrim['Window_Difference']);

        # Check for non-empty values
        $isEmpty = 1;
        foreach ($recTrim as $item) {
          if (!empty($item)) {
            $isEmpty = 0;
            break;
          }
        }

        # if not empty, update status to Imported (flag)
        if ($isEmpty == 0) {
          $cid = $rec['CommentID'];
          $cmd = sprintf("update flag set Data_entry = 'Imported' where CommentID=:cid");
          $db->query($cmd);
          $db->bind(':cid',$cid);
          $db->execute($cmd);

        }

      }
    }
    sleep(3);
  }

?>
