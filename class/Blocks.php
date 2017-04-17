<?php

class Blocks {

  private $DB;

  function __construct($DB) {
    $this->DB = $DB;
  }

  public function checkIfIPExistsInDB($ip) {

    list($ip_first_block) = explode(".", $ip);
    $blocks = array();

    $ip_first_block .= '.%.%.%';

    $query = "SELECT ID,Subnet FROM destiny_blocks WHERE Subnet LIKE ? ORDER BY ID";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->bind_param('s', $ip_first_block);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $blocks[] = array('ID' => $row['ID'],'Subnet' => $row['Subnet']);
    }

    return $blocks;

  }

  public function getClosestLocationByID($subnet_id) {

    $latency = array();
    $latency_location = array();

    $query = "SELECT Location,Latency FROM destiny_latency WHERE BlockID = ? ORDER BY ID";
    $stmt = $this->DB->GetConnection()->prepare($query);
    $stmt->bind_param('i', $subnet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $latency[] = $row['Latency'];
      $latency_location[$row['Latency']] = $row['Location'];
    }

    return $latency_location[min($latency)];

  }

  public function checkIfRequestExists($ip) {

      $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM destiny_requests WHERE IP = ? LIMIT 1");
      $stmt->bind_param('s', $ip);
      $stmt->execute();
      $stmt->bind_result($db_id);
      $stmt->fetch();
      $stmt->close();

      if (isset($db_id)) {
        return true;
      } else {
        return false;
      }

  }

  public function checkIfSubnetExistsInBlocks($subnet) {

      $stmt = $this->DB->GetConnection()->prepare("SELECT ID FROM destiny_blocks WHERE Subnet = ? LIMIT 1");
      $stmt->bind_param('s', $subnet);
      $stmt->execute();
      $stmt->bind_result($db_id);
      $stmt->fetch();
      $stmt->close();

      if (isset($db_id)) {
        return true;
      } else {
        return false;
      }

  }


  public function addLatency($blockID,$location,$latency) {

    $stmt = $this->DB->GetConnection()->prepare("INSERT INTO destiny_latency(BlockID,Location,Latency) VALUES (?,?,?)");
    $stmt->bind_param('iss',$blockID,$location,$latency);
    $stmt->execute();
    $stmt->close();

  }

  public function addRequest($ip) {

    $stmt = $this->DB->GetConnection()->prepare("INSERT INTO destiny_requests(IP) VALUES (?)");
    $stmt->bind_param('s',$ip);
    $stmt->execute();
    $stmt->close();

  }

  public function removeRequest($id) {

    $stmt = $this->DB->GetConnection()->prepare("DELETE FROM destiny_requests WHERE ID = ?");
    $stmt->bind_param('i', $id);
    $rc = $stmt->execute();
    $stmt->close();

  }

  public function addEntry($subnet) {

    $stmt = $this->DB->GetConnection()->prepare("INSERT INTO destiny_blocks(Subnet) VALUES (?)");
    $stmt->bind_param('s',$subnet);
    $stmt->execute();
    $subnet_id = $stmt->insert_id;
    $stmt->close();

    return $subnet_id;

  }

}

?>
