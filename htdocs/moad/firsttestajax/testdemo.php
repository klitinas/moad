<!-- Our food group drop down-->
<select id="foodgroups">
    <option value="">Select a Food Group</option>
    <option value="1">Fruit</option>
    <option value="2">Vegetables</option>
    <option value="3">Meat</option>
    <option value="4">Dairy</option>
    <option value="5">Grains</option>
</select>



<!-- Our foods for a group selection (empty on default) -->
<select id="foods"></select>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/script.js"></script>

<script>
$(function() {
  $("#foodgroups").change(function() {
    var groupID = $(this).val();

    $.ajax({
        type: "GET",
        url: "http://localhost/moad/testgetfoodgroups.php",
        data: { id: groupID },
        dataType: "json",
        success:function(result)//we got the response
       {
        alert('Successfully called');
       },
       error:function(exception){alert('Exception:'+exception);}
    }


  ).done(function(reply) {
        // Clear options
        $("#foods").find("option").remove();

        // Loop through JSON response
        $.each(reply, function(key, value) {
            $('#foods').append($('<option>', { value: value.foodid }).text(value.name));
        });
    });
  });
});
</script>
