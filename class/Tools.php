<?php

class Tools {

  public static function getSubnetFromIP($ip) {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://api.bgpview.io/ip/".$ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 200) {
        $resp = json_decode($response);

        if ($resp->status == "ok") {
            return $resp->data->prefixes[0]->prefix;
        } else {
          return false;
        }

    } else {
      return false;
    }

  }

  public static function getSubnetID($ip, $ip_blocks) {

    foreach ($ip_blocks as $element) {
      $result = Tools::CheckIfIPInRange($ip,$element['Subnet']);
      if ($result === true) { return $element['ID']; }
    }

  }

  public static function getDNSbyLocation($location) {

    $servers['FR'] = '{"result":[{"qtype":"A", "qname":"yourdomain.science", "content":"REPLACE_IP", "ttl": 1}]}';
    $servers['ATL'] = '{"result":[{"qtype":"A", "qname":"yourdomain.science", "content":"REPLACE_IP", "ttl": 1}]}';
    $servers['AU'] = '{"result":[{"qtype":"A", "qname":"yourdomain.science", "content":"REPLACE_IP", "ttl": 1}]}';
    $servers['SA'] = '{"result":[{"qtype":"A", "qname":"yourdomain.science", "content":"REPLACE_IP", "ttl": 1}]}';

    return $servers[$location];

  }

  public static function CheckIfIPInRange( $ip, $range ) {

    //Stolen from: https://gist.github.com/tott/7684443

  	if ( strpos( $range, '/' ) == false ) {
  		$range .= '/32';
  	}
  	// $range is in IP/CIDR format eg 127.0.0.1/24
  	list( $range, $netmask ) = explode( '/', $range, 2 );
  	$range_decimal = ip2long( $range );
  	$ip_decimal = ip2long( $ip );
  	$wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
  	$netmask_decimal = ~ $wildcard_decimal;
  	return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );

  }

  public static function getRTT($host) {

    $ping = shell_exec("ping $host -c 2");
    $lines = explode("\n", $ping);
    if(!$lines[1] || !$lines[2]) {
      return false;
    } else {
      $first = str_replace(" ms", "", explode("time=", $lines[1])[1]);
      $second = str_replace(" ms", "", explode("time=", $lines[2])[1]);
      return (($first + $second) / 2);
    }

  }

  public static function tracerouteHops($host) {

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

  public static function getLatency($ip) {

    $hops = Tools::tracerouteHops($ip);
    for($i = sizeof($hops)-1; $i >= 0; $i--) {
      $rtt = Tools::getRTT($hops[$i]);
      if(sizeof($hops) - $i > 4) {
        echo "Hop difference too big.";
        break;
      }
      if($rtt) {
        return array('rtt' => $rtt, 'ip' => $hops[$i]);
      }
    }

  }

  public static function getRemoteLatency($remote,$ip) {

    $URL = "https://".$remote."/ping.php?host=". $ip;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); //Time for connection in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); //Time for execution in seconds
    $content = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totaltime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    curl_close($ch);

    $result = array();

    if ($httpcode == 200) {
      return $content;
    } else {
      return 'Did not return Response Code 200';
    }

  }


}

?>
