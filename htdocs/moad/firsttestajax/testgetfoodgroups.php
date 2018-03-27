<?php
  if (isset($_GET["id"])) {
    include 'database.class.php';

    $db = new Database();
    $str = "select ID as foodid,Test_name as name from test_names;";
    $db->query($str);
    $result = $db->resultset();

    //$result = array('name'=>array('zero','one','two','three'),'foodid'=>array(0,1,2,3));
    //$result = array('name'=>'zero','foodid'=>1);
    echo json_encode($result);
  }
?>
