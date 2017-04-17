<?php

  function getRTT($host) {

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

  $Whitelist = array('');

  if (isset($_GET['host'])) {
  	$remote = $_SERVER['REMOTE_ADDR'];
    $forward = '';
  	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  		$forward = $_SERVER['HTTP_X_FORWARDED_FOR'];
  	}
  	if (in_array($remote, $Whitelist) AND $forward == "") {
  		$host = $_GET['host'];
  		echo getRTT($host);

  	}
  }

 ?>
