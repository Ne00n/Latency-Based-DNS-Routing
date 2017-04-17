<?php

    if (php_sapi_name() == 'cli') {

      include '../config.php';

      function dat_loader($class) {
          include '../class/' . $class . '.php';
      }

      spl_autoload_register('dat_loader');

      $DB = new Database;
      $DB->InitDB();

      $query = "SELECT ID,IP FROM destiny_requests ORDER by id LIMIT 40";
      if ($result = $DB->GetConnection()->query($query)) {
          /* fetch object array */
          while ($row = $result->fetch_row()) {
            BackgroundProcess::startProcess("/usr/bin/php Runner.php -I ".$row[0]." -P ".$row[1]);
            sleep(2);
          }
          /* free result set */
          $result->close();
      }

    }

?>
