<?php

$NS = '{"qtype":"NS", "qname":"ns1.yourdomain.science", "content":"IP_REPLACE", "ttl": 1},{"qtype":"NS", "qname":"ns2.yourdomain.science", "content":"IP_REPLACE", "ttl": 1}';

$responses['/dns/lookup/ns1.destinycdn.science./ANY'] = '{"result":[{"qtype":"A", "qname":"ns1.yourdomain.science", "content":"IP_REPLACE", "ttl": 3600},'.$NS.']}';
$responses['/dns/lookup/ns2.destinycdn.science./ANY'] = '{"result":[{"qtype":"A", "qname":"ns2.yourdomain.science", "content":"IP_REPLACE", "ttl": 3600},'.$NS.']}';

$responses['/dns/lookup/destinycdn.science./ANY'] = '';
$responses['/dns/lookup/destinycdn.science./SOA'] = '{"result":[{"qtype":"SOA", "qname":"yourdomain.science", "content":"IP_REPLACE", "ttl": 1}]}';

if (isset($responses[$_SERVER['REQUEST_URI']])) {
  if ($_SERVER['REQUEST_URI'] == '/dns/lookup/destinycdn.science./ANY') {
    include 'target.php';
  } else {
    echo $responses[$_SERVER['REQUEST_URI']];
  }
} else {
  header("HTTP/1.0 404 Not Found");
}

//if (startsWith($_SERVER['REQUEST_URI'], "/dns/lookup/")) { echo '{"result":[{"qtype":"ANY", "qname":"google.com", "content":"7.7.7.7", "ttl": 60}]}';}

//if (startsWith($_SERVER['REQUEST_URI'], "/dns/calculateSOASerial/")) { echo '{"result":2013060501}';}

?>
