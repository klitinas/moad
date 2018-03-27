<!-- Test drop down-->
Select Assessment Items
<br></br>
<form method="post" >

<select id="assessments">
  <option value="">Select an Assessment</option>

  <?php
    include "database.class.php";
    $query = "select Test_name,Full_name from test_names";
    $db = new Database();
    $db->query($query);
    $result = $db->resultset();

    foreach ($result as $row) {
      $fullname = $row['Full_name'];
      $testname = $row['Test_name'];
      $out = sprintf("<option value='%s'>%s</option>",$testname,$fullname);
      echo $out;
    }
    ?>
</select>


<script type="text/javascript">
function updateItemList() {

  var testName = document.getElementById("assessments").value;
  var select1 = document.getElementById("testitems");
  var selected1 = [];
  for (var i = 0; i < select1.length; i++) {
      //if (select1.options[i].selected) selected1.push(select1.options[i].value);
      if (select1.options[i].selected)
        $('#selectedtestitems').append($('<option>', { value: testName+'.'+select1.options[i].value }).text(testName+'.'+select1.options[i].value));
  }
}
</script>


<!-- Assement item(s) selection box dynamically populated (empty on default) -->
<br></br>
<select id="testitems" multiple="multiple" style="width:140px;height:140px;"></select>

<input type="button" id="submittestitems" value="Add items" onclick="javascript:updateItemList()"/>

<br></br>

</form>

<select id="selectedtestitems" multiple="multiple" style="width:140px;height:140px;"></select>

<br></br>
<br></br>
Choose Filters
<br></br>
Subject <input id="filtSubj"></input>



<br></br>
<input type="button" id="doQuery" value="Run Query"/>



<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/script.js"></script>

<script type="text/javascript">
$(function() {
  $("#assessments").change(function() {
    var groupID = $(this).val();

    $.ajax({
        type: "GET",
        url: "http://localhost/moad/gettestitems.php",
        data: { id: groupID },
        dataType: "json",
      /*  success:function(result)//we got the response
       {
      alert('Successfully called');
       },
       error:function(exception){alert('Exception:'+exception);} */
    }
    ).done(function(reply) {
        // Clear options
        $("#testitems").find("option").remove();

        // Loop through JSON response
        $.each(reply, function(key, value) {
            //$('#testitems').append($('<option>', { value: value.foodid }).text(value.name));
            $('#testitems').append($('<option>', { value: value.item }).text(value.item));
        });
    });
  });
});
</script>

<!-- query cb -->
<script>
$(function() {
  $("#doQuery").on('click',function() {

    // get obj properties to pass to php?
    var itemNames = document.getElementById("selectedtestitems");
    var selected1 = [];

    for (var i = 0; i < itemNames.length; i++) {
      //if (select1.options[i].selected) selected1.push(select1.options[i].value);
      //if (itemNames.options[i].selected) selected1.push(itemNames.options[i].text);
      selected1.push(itemNames.options[i].text);

    }

    $.ajax({
      type: "GET",
      url: "http://localhost/moad/doquery.php",

      // pass stuff to php code in JSON format
      data: { items: selected1 },
      dataType: "json",
    }
  ).done(function(reply) {
    // pass stuff back to be displayed


  });
});
});
</script>
