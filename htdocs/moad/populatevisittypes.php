<?php

$cid = $argv[1];

include 'database.class.php';

$db = new Database();
$str = sprintf("select s.Date_visit,s.Visit_label,v.id_one,v.id_two,v.id_three,v.visit_id_one,v.visit_id_two,v.visit_id_three from visit v
join session s on substring(v.CommentID,11,length(v.CommentID)-23) =s.id
where v.CommentID like '%s%%' order by s.Visit_label asc;",$cid);


$db->query($str);
$result = $db->resultset();

print_r($result);

// Get all unique IDs across visits
$id_one = array_column($result, 'id_one');
$id_two = array_column($result, 'id_two');
$id_three = array_column($result, 'id_three');
$arrIds = array_filter(array_unique(array_merge($id_one,$id_two,$id_three)));

$arrVids = array();
$ind = 0;
foreach ($result as $r) {
  $t = array_filter(array($r['id_one'],$r['id_two'],$r['id_three']));
  $arrVids[$ind] = $t;
  $ind += 1;
}

// now get corresponding visits for each id
foreach ($arrIds as $id) {
  $tmp = array_search($id,$arrVids);
  print $tmp;

}

exit;
$arrTypes = array('Baseline','1st Year','2nd Year','3rd Year');

$indRow = 0;
foreach ($result as $row) {

  $id_one = $row['id_one'];
  $vid_one = $row['visit_id_one'];

  $id_two = $row['id_two'];
  $vid_two = $row['visit_id_two'];

  $id_three = $row['id_three'];
  $vid_three = $row['visit_id_three'];

  // first visit, always Baseline
  $t1='';
  $t2='';
  $t3='';
  if ($indRow==0) {
    if ($id_one !='') {
      $t1 = $arrTypes[0];
    }

    if ($id_two !='') {
      $t2 = $arrTypes[0];
    }

    if ($id_three !='') {
      $t3 = $arrTypes[0];
    }
  } else {



  /*if ($id_one !='' && $vid_one=='') {
      $t1 = $arrTypes[0];
      echo $t1;

    }
  }*/
  }
  $indRow+=1;
}
?>
