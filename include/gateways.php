<?php
?>
  <div class="panel panel-default">
  <!-- Standard-Panel-Inhalt -->
  <div class="panel-heading">Connected P25Gateways</div>
  <!-- Tabelle -->
  <div class="table-responsive"> 
  <table id="gateways" class="table table-condensed">
  	<thead>
    <tr>
      <th>Reporting Time (<?php echo TIMEZONE;?>)</th>
      <th>Callsign</th>
    </tr>
    </thead>
    <tbody>
<?php
	//$gateways = getConnectedGateways($logLines);
	$gateways = getLinkedGateways($logLines);
	foreach ($gateways as $gateway) {
		
		echo "<tr>";
		echo "<td>".convertTimezone($gateway['timestamp'])."</td>";
		
		if (constant("GDPR"))
			echo"<td nowrap>".str_replace("0","&Oslash;",substr($gateway['callsign'],0,3)."***")."</td>";
		else
			echo"<td nowrap>".str_replace("0","&Oslash;",$gateway['callsign'])."</td>";
		echo "</tr>";
	}
?>
  </tbody>
  </table>
  </div>
  <script>
    $(document).ready(function(){
      $('#gateways').dataTable( {
        "aaSorting": [[1,'asc']]
      } );
    });
   </script>
</div>
