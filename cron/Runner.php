<?php

    if (php_sapi_name() == 'cli') {

      include '../config.php';

      function dat_loader($class) {
          include '../class/' . $class . '.php';
      }

      spl_autoload_register('dat_loader');

      $DB = new Database;
      $DB->InitDB();

      $servers_remote = array();
      $servers_remote['ATL'] = 'atl.yourdomain.science';
      $servers_remote['AU'] = 'au.yourdomain.science';
      $servers_remote['SA'] = 'sa.yourdomain.science';

      $options = getopt("I:P:");

      $id = $options['I'];
      $ip = $options['P'];

      $Blocks = new Blocks($DB);

      echo $ip."\n";
      $subnet = Tools::getSubnetFromIP($ip);
      echo $subnet."\n";

      if ($subnet != false) {
        $latency =  Tools::getLatency($ip);
        echo $latency['rtt']."\n";

        if ($latency != "Hop difference too big.") {

          if ($Blocks->checkIfSubnetExistsInBlocks($subnet) === false) {
            $subnet_id = $Blocks->addEntry($subnet);
            echo $subnet_id."\n";
            $Blocks->addLatency($subnet_id,'FR',$latency['rtt']);

            foreach ($servers_remote as $key => $element) {
              while ($i <= 5) {
                $remote_latency = Tools::getRemoteLatency($element,$latency['ip']);
                if ($remote_latency != 'Did not return Response Code 200') { break;}
                $i++;
              }
              $remote_latency = Tools::getRemoteLatency($element,$latency['ip']);
              echo $remote_latency."\n";
              if ($remote_latency != 'Did not return Response Code 200') {
                $Blocks->addLatency($subnet_id,$key,$remote_latency);
              }
            }

          }
          $Blocks->removeRequest($id);
        }

      }

    }

  ?>
