<?php

//$cid = $argv[1];

$candID=$argv[1];

include 'database.class.php';

$db = new Database();

// Get list of session IDs for this subject
$str = sprintf("select ID from session where ID in (select SessionID from flag) and CandID='%s' order by Date_visit;",$candID);


//$str = select s.CandID, s.ID,s.Date_visit,f.CommentID from session s join flag f on f.SessionID=s.ID where s.ID in (select SessionID from flag) and s.CandID=867673 and f.CommentID not like 'DDE%' and f.Test_name='visit' order by Date_visit;
$str = sprintf("select f.CommentID from session s join flag f on f.SessionID=s.ID where s.ID in (select SessionID from flag) and s.CandID='%s' and f.CommentID not like 'DDE%%' and f.Test_name='visit' order by Date_visit;",$candID);
$db->query($str);
$arrCids = $db->resultset();

$arrTypes = array('Baseline','1st Year','2nd Year','3rd Year');

// Loop through each CommentID
$arrCodes = array();
foreach ($arrCids as $CID) {
  $CID = $CID['CommentID'];

  $qIDs = sprintf("select id_one,id_two,id_three,visit_id_one,visit_id_two,visit_id_three from visit where CommentID = '%s'",$CID);
  $db->query($qIDs);
  $idInfo = $db->resultset();
  $idInfo = $idInfo[0];


  // Id One
  $id_one = $idInfo['id_one'];
  $visit_id_one = $idInfo['visit_id_one'];

  if (empty($visit_id_one)) {
    $numExisting = array_count_values($arrCodes)[$id_one];
    $thisNum = $numExisting + 1;

    $strCmd = sprintf("update visit set visit_id_one = '%s' where CommentID='%s'",$arrTypes[$thisNum-1],$CID);
    print_r($strCmd);

    $db->query($strCmd);
    $db->execute($strCmd);
  }

  array_push($arrCodes,$id_one);

  // Id Two
  $id_two = $idInfo['id_two'];
  $visit_id_two = $idInfo['visit_id_two'];
  if (!empty($id_two)) {

    if (empty($visit_id_two) || $visit_id_two === NULL) {
      $numExisting = array_count_values($arrCodes)[$id_two];
      $thisNum = $numExisting + 1;

      $strCmd = sprintf("update visit set visit_id_two = '%s' where CommentID='%s'",$arrTypes[$thisNum-1],$CID);
      print_r($strCmd);

      $db->query($strCmd);
      $db->execute($strCmd);
    }

    array_push($arrCodes,$id_two);
  }

  $id_three = $idInfo['id_three'];
  $visit_id_three = $idInfo['visit_id_three'];

  // Id three
  if (! empty($id_three)) {
    if (empty($visit_id_three)) {
      $numExisting = array_count_values($arrCodes)[$id_three];
      $thisNum = $numExisting + 1;

      $strCmd = sprintf("update visit set visit_id_three = '%s' where CommentID='%s'",$arrTypes[$thisNum-1],$CID);
      print_r($strCmd);

      $db->query($strCmd);
      $db->execute($strCmd);
    }

    array_push($arrCodes,$id_three);
  }

}



?>
