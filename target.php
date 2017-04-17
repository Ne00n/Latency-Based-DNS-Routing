<?php

include 'config.php';

function dat_loader($class) {
    include 'class/' . $class . '.php';
}

spl_autoload_register('dat_loader');

$DB = new Database;
$DB->InitDB();

$ip = $_SERVER['HTTP_X_REMOTEBACKEND_REMOTE'];

$Blocks = new Blocks($DB);

$ip_blocks = $Blocks->checkIfIPExistsInDB($ip);

$subnet_id = Tools::getSubnetID($ip,$ip_blocks);

if (!empty($subnet_id)) {

    $location = $Blocks->getClosestLocationByID($subnet_id);

    if (!empty($location)) {
      echo Tools::getDNSbyLocation($location);
    } else {
      echo Tools::getDNSbyLocation('FR');
    }

} else {
  if ($Blocks->checkIfRequestExists($ip)) {
    echo Tools::getDNSbyLocation('FR');
  } else {
    $Blocks->addRequest($ip);
    echo Tools::getDNSbyLocation('FR');
  }
}

?>
