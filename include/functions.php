<?php
function getP25ReflectorVersion() {
	// returns creation-time of P25Reflector as version-number
	$filename = P25REFLECTORPATH."/P25Reflector";
	// Validate file exists and is executable before running command
	if (!file_exists($filename) || !is_executable($filename)) {
		return getP25ReflectorFileVersion();
	}
	$output = array();
	exec($filename." -v 2>&1", $output);
	if (!empty($output) && !startsWith(substr($output[0],21,8),"20")) {
		return getP25ReflectorFileVersion();
	} else if (!empty($output)) {
		return substr($output[0],21,8)." (compiled ".getP25ReflectorFileVersion().")";
	}
	return getP25ReflectorFileVersion();
}


function getP25ReflectorFileVersion() {
	// returns creation-time of P25Reflector as version-number
	$filename = P25REFLECTORPATH."/P25Reflector";
	if (file_exists($filename)) {
		return date("d M Y", filectime($filename));
	}
}

function getP25ReflectorConfig() {
	// loads NXDNReflector.ini into array for further use
	$conf = array();
	if ($configs = fopen(P25REFLECTORINIPATH."/".P25REFLECTORINIFILENAME, 'r')) {
		while ($config = fgets($configs)) {
			array_push($conf, trim ( $config, " \t\n\r\0\x0B"));
		}
		fclose($configs);
	}
	return $conf;
}

function getConfigItem($section, $key, $configs) {
	// retrieves the corresponding config-entry within a [section]
	$sectionpos = array_search("[" . $section . "]", $configs) + 1;
	$len = count($configs);
	while(startsWith($configs[$sectionpos],$key."=") === false && $sectionpos <= ($len) ) {
		if (startsWith($configs[$sectionpos],"[")) {
			return null;
		}
		$sectionpos++;
	}
	
	return substr($configs[$sectionpos], strlen($key) + 1);
}

function getP25ReflectorLog() {
	// Open Logfile and copy loglines into LogLines-Array()
	$logLines = array();
	if ($log = fopen(P25REFLECTORLOGPATH."/".P25REFLECTORLOGPREFIX."-".date("Y-m-d").".log", 'r')) {
		while ($logLine = fgets($log)) {
			if (startsWith($logLine, "M:"))
				array_push($logLines, $logLine);
		}
		fclose($log);
	}
	return $logLines;
}

function getShortP25ReflectorLog() {
	// Open Logfile and copy loglines into LogLines-Array()
	$logPath = P25REFLECTORLOGPATH."/".P25REFLECTORLOGPREFIX."-".date("Y-m-d").".log";
	$result = shell_exec("tail -n1 " . escapeshellarg($logPath));
	$logLines = $result ? explode("\n", $result) : array();
	//$logLines = explode("\n", shell_exec("egrep -h \"Received|watchdog\" " . escapeshellarg($logPath) . " | tail -1"));
	return $logLines;
}

function getConnectedGateways($logLines) {
	$gateways = Array();
	foreach ($logLines as $logLine) {
		if(strpos($logLine,"P25Reflector")){
			$gateways = Array();
		}
		if(strpos($logLine,"Adding")) {
			$lineParts = explode(" ", $logLine);
			if (!array_search($gateways, $lineParts[4])) {
				array_push($gateways, Array('callsign'=>$lineParts[4],'timestamp'=>$lineParts[1]." ".substr($lineParts[2],0,8)));
			}
		}
		if(strpos($logLine,"Removing")) {
			$lineParts = explode(" ", $logLine);
			$pos = array_search($lineParts[4],array_column($gateways, 'callsign'));
			array_splice($gateways, $pos, 1);
		}
	}
	return $gateways;
}

function getLinkedGateways($logLines) {
//0000000000111111111122222222223333333333444444444455555555556666666666	
//0123456789012345678901234567890123456789012345678901234567890123456789
//M: 2016-06-24 11:11:41.787 Currently linked repeaters/gateways:
//M: 2016-06-24 11:11:41.787     GATEWAY   : 217.82.212.214:42000 2/60
//M: 2016-06-24 11:11:41.787     DM0GER    : 217.251.59.165:42000 5/60

	$gateways = Array();
	for ($i = count($logLines); $i>0; $i--) {
		$logLine = $logLines[$i-1];
		
		if (strpos($logLine, "Starting P25Reflector")) {
			return $gateways;
		}
		if (strpos($logLine, "No repeaters linked")) {
			return $gateways;
		}
		if (strpos($logLine, "Currently linked repeaters")) {
			for ($j = $i+1; $j <= count($logLines); $j++) {
				$logLine = $logLines[$j-1];
				if (!startsWith(substr($logLine,27), "   ")) {
					return $gateways;
				} else {
					$timestamp = substr($logLine, 3, 19);
					$callsign = substr($logLine, 31, 10);
					//$ipport = substr($logLine,43);
					$ipport = substr($logLine,31);
					$key = searchForKey("ipport",$ipport, $gateways);
					if ($key === NULL) {
						array_push($gateways, Array('callsign'=>$callsign,'timestamp'=>$timestamp,'ipport'=>$ipport));
					}
				}	
			}
		}
	}
	return $gateways;
}

function getHeardList($logLines) {
	$heardList = array();
	$dttxend = "";
	foreach ($logLines as $logLine) {
		$duration = "transmitting";
		$timestamp = substr($logLine, 3, 19);
		$dttimestamp = new DateTime($timestamp);
		if ($dttxend !== "") {
			$duration = $dttimestamp->diff($dttxend)->format("%s");
		}
		$callsign2 = substr($logLine, strpos($logLine,"from") + 5, strpos($logLine,"at") - strpos($logLine,"from") - 6);
        $callsign = trim($callsign2);
        $target = substr($logLine, strpos($logLine,"at") + 16 , strpos($logLine,"to") - strpos($logLine,"at") + 6 ) ; 
        $gateway = substr($logLine, strpos($logLine,"at") + 3 , strpos($logLine,"to") - strpos($logLine,"at") - 3 ) ; 
		// Callsign or ID should be less than 11 chars long, otherwise it could be errorneous
		if ( strlen($callsign) < 11 ) {
			array_push($heardList, array(convertTimezone($timestamp), $callsign, $target, $gateway, $duration));
		}
		if(strpos($logLine,"end of") || strpos($logLine,"watchdog has expired") || strpos($logLine,"ended RF data") || strpos($logLine,"ended network")) {
			$txend = substr($logLine, 3, 19);
			$dttxend = new DateTime($txend);
		}
	}
	return $heardList;
}

function getLastHeard($logLines) {
	//returns last heard list from log
	$lastHeard = array();
	$heardCalls = array();
	$heardList = getHeardList($logLines);
	$counter = 0;
	foreach ($heardList as $listElem) {
		if(!(array_search($listElem[1], $heardCalls) > -1)) {
			array_push($heardCalls, $listElem[1]);
			array_push($lastHeard, $listElem);
			$counter++;
		}
	}
	return $lastHeard;
}

function getSize($filesize, $precision = 2) {
	$units = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');
	foreach ($units as $idUnit => $unit) {
		if ($filesize > 1024)
			$filesize /= 1024;
		else
			break;
	}
	return round($filesize, $precision).' '.$units[$idUnit].'B';
}
?>
