
<a href="./forms/" target="_blank">FORMS</a>
<br />
<br />
<a href="./workbooks/{$candID}/{$sessionID}" target="_blank">SUBJECT WORKBOOK</a>
<br />
<br />
<!--{if $isDataEntryPerson} -->
<!--button class="btn btn-primary" onclick="location.href='main.php?test_name=candidate_parameters&candID={$candID}&identifier={$candID}'">Move Visit</button-->
<!--{else}
Edit Candidate Info
{/if} -->

<script type="text/javascript">
function openwindow (url) {
   var win = window.open(url, "window1", "width=600,height=400,status=yes,scrollbars=yes,resizable=yes");
   win.focus();
}
</script>

<button class="btn btn-primary" onclick="openwindow('./move_visit.php')">Move Visit (Placeholder)</button>

<!--form action="./move_visit.php" method="get">

  <input type="submit" value="Move visit">
</form-->

<br />

<!-- table title -->
<br />
<table border="0" valign="bottom" width="100%"><td class="controlPanelSection"><strong>Behavioral Battery of Instruments</strong></td></table>

<!-- table with list of instruments and links to open them -->
<table class="table table-hover table-bordered dynamictable" cellpadding="2">
{section name=group loop=$instruments}
    <!-- print the sub group header row -->
    <thead>
    <tr class="info">
	    <th>{$instrument_groups[group].title}
	       <!-- show the instruction only one time -->
	       {if $smarty.section.group.iteration == 1}
	       <br />(Click To Open)
	       {/if}
	    </th>
	    <th>Data Entry</th>
	    <th>Administration</th>
	    <th>Feedback</th>
	    <th>Double Data Entry Form</th>
	    <th>Double Data Entry Status</th>
    </tr>
    </thead>
	{section name=instrument loop=$instruments[group]}
	<tbody>
	   	<tr{if $instruments[group][instrument].isDirectEntry} class="directentry"{/if}>
	    	<td>
		    	<a href="main.php?test_name={$instruments[group][instrument].testName}&candID={$candID}&sessionID={$sessionID}&commentID={$instruments[group][instrument].commentID}">
	            {$instruments[group][instrument].fullName}</a></td>
	    	<td>{$instruments[group][instrument].dataEntryStatus}</td>
	    	<td>{$instruments[group][instrument].administrationStatus}</td>
	    	<td bgcolor="{$instruments[group][instrument].feedbackColor}">
		    	{$instruments[group][instrument].feedbackStatus}
	        </td>
			<td>
				{if $instruments[group][instrument].isDdeEnabled }
				    	<a href="main.php?test_name={$instruments[group][instrument].testName}&candID={$candID}&sessionID={$sessionID}&commentID={$instruments[group][instrument].ddeCommentID}">Double Data Entry</a>
			   {/if}&nbsp;
			</td>
			<td>{if $instruments[group][instrument].isDdeEnabled }{$instruments[group][instrument].ddeDataEntryStatus}{/if}&nbsp;</td>
	   	</tr>
	   </tbody>
	{/section}
{sectionelse}
     <tr><td nowrap="nowrap">The battery has no registered instruments</td></tr>
{/section}
</table>




</div>
