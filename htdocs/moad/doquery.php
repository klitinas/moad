<?php
// Runs the actual query specified in front end

  if (isset($_GET["items"])) {
    include 'database.class.php';

    $db = new Database();

    // Parse query
    


    $str = sprintf("select COLUMN_NAME as item from information_schema.columns where table_schema = 'loris_prod' and table_name='%s'",$_GET["id"]);
    $db->query($str);
    $result = $db->resultset();

    //$result = array('name'=>array('zero','one','two','three'),'foodid'=>array(0,1,2,3));
    //$result = array('name'=>'zero','foodid'=>1);
    echo json_encode($result);
  }


?>
