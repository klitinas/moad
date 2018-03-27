<?php

#if ($_GET['run']) {
#  # This code will run if ?run=true is set.
# exec("move_visit.sh");

$subject = $_POST['subject'];

// Get original CandID, SessionID from URL
$url=$_SERVER[HTTP_REFERER];
$str=parse_url($url,PHP_URL_QUERY);
parse_str($str,$output);
$candID_orig = $output['candID'];
$sessionID_orig = $output['sessionID'];


$db = new PDO('mysql:host=141.214.246.201;dbname=loris_prod;charset=utf8mb4', 'lorisuser', 'lorisuser');
$sql="select Value from parameter_candidate order by Value";

$stmt = $db->query($sql);
$stmt->setFetchMode(PDO::FETCH_ASSOC);

$allCodes = array();
foreach ($stmt as $row) {
  $bigstr = $row['Value'];
  $arr = explode(" ", $bigstr);
  $allCodes = array_merge($arr,$allCodes);
};
$allCodes = array_unique($allCodes);
asort($allCodes);

?>

<script>

function valueselect(str) {
  if (str == "") {
    document.getElementById("txtHint").innerHTML = "";
    return;
  } else {
    if (window.XMLHttpRequest) {
      // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
    } else {
      // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
      }
    };
    xmlhttp.open("GET","getvisit.php?q="+str,true);
    xmlhttp.send();
  }
}
</script>


<br></br>
<form action="<?php $_PHP_SELF ?>" method="post" enctype="multipart/form-data">

<select name='subject' id='subjectbox' onchange='javascript:valueselect(this.value)'>
  <option value=''>--Select the proper subject code--</option>
  <?php foreach ($allCodes as $subj) {
    echo '<option value="'.$subj.'">'.$subj.'</option>';
  }?>
</select>
