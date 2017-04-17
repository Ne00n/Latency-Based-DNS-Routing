<?php
function getRTT($host) {
  $ping = shell_exec("ping $host -c 2");
  $lines = explode("\n", $ping);
  if(!$lines[1] || !$lines[2]) {
    return false;
  } else {
    $first = str_replace(" ms", "", explode("time=", $lines[1])[1]);
    $second = str_replace(" ms", "", explode("time=", $lines[2])[1]);
    var_dump($first);
    var_dump($second);
    return (($first + $second) / 2);
  }
}
function tracerouteHops($host) {
  $traceroute = shell_exec("traceroute $host -n -w 1");
  $traceroute = explode("\n", $traceroute);
  $hops = array();
  for($i = 1; $i < sizeof($traceroute)-1; $i++) {
    if(filter_var(explode(" ", $traceroute[$i])[3], FILTER_VALIDATE_IP)) {
        array_push($hops, explode(" ", $traceroute[$i])[3]);
    }
  }
  return ($hops);
}
$hops = tracerouteHops("8.8.8.8");
var_dump($hops);
for($i = sizeof($hops)-1; $i >= 0; $i--) {
  var_dump($hops[$i]);
  $rtt = getRTT($hops[$i]);
  var_dump($rtt);
  if(sizeof($hops) - $i > 4) {
    echo "Hop difference too big.";
    break;
  }
  if($rtt) {
    echo "Last pingable RTT: $rtt at hop " . ($i+1) . "";
    break;
  }
}
?>
