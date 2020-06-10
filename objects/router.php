<?php

// 'router' object
class Router{

    // database connection and table name
    private $conn;
    private $table_name = "routers";

    // object properties
    public $id;
    public $sap_id;
    public $internet_host_name;
    public $client_ip_address;
    public $mac_address;
    public $is_deleted;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    function create(){

        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    sap_id = :sap_id,
                    internet_host_name = :internet_host_name,
                    client_ip_address = :client_ip_address,
                    mac_address = :mac_address";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->sap_id=htmlspecialchars(strip_tags($this->sap_id));
        $this->internet_host_name=htmlspecialchars(strip_tags($this->internet_host_name));
        $this->client_ip_address=htmlspecialchars(strip_tags($this->client_ip_address));
        $this->mac_address=htmlspecialchars(strip_tags($this->mac_address));

        // bind the values
        $stmt->bindParam(':sap_id', $this->sap_id);
        $stmt->bindParam(':internet_host_name', $this->internet_host_name);
        $stmt->bindParam(':client_ip_address', $this->client_ip_address);
        $stmt->bindParam(':mac_address', $this->mac_address);
        if($stmt->execute()){
            return true;
        }
        return false;
    }

        // check if given ip exist in the database
    function ipExists(){

        // query to check if email exists
        $query = "SELECT sap_id, internet_host_name, client_ip_address, mac_address FROM " . $this->table_name . "
                  WHERE client_ip_address = ?
                  LIMIT 0,1";

        // prepare the query
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $this->client_ip_address=htmlspecialchars(strip_tags($this->client_ip_address));

        // bind given email value
        $stmt->bindParam(1, $this->client_ip_address);

        // execute the query
        $stmt->execute();

        // get number of rows
        $num = $stmt->rowCount();

        // if ip exists, assign values to object properties for easy access and use for php sessions
        if($num>0){

            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // assign values to object properties
            $this->id = $row['id'];
            $this->internet_host_name = $row['internet_host_name'];
            $this->client_ip_address = $row['client_ip_address'];
            $this->mac_address = $row['mac_address'];

            // return true because ip exists in the database
            return true;
        }

        // return false if email does not exist in the database
        return false;
      }

      function update(){

          // insert query
          $query = "UPDATE  " . $this->table_name . "
                    SET
                      sap_id = :sap_id,
                      internet_host_name = :internet_host_name,
                      mac_address = :mac_address
                    WHERE client_ip_address=?";

          // prepare the query
          $stmt = $this->conn->prepare($query);

          // sanitize
          $this->sap_id=htmlspecialchars(strip_tags($this->sap_id));
          $this->internet_host_name=htmlspecialchars(strip_tags($this->internet_host_name));
          $this->client_ip_address=htmlspecialchars(strip_tags($this->client_ip_address));
          $this->mac_address=htmlspecialchars(strip_tags($this->mac_address));

          // bind the values
          $stmt->bindParam(':sap_id', $this->sap_id);
          $stmt->bindParam(':internet_host_name', $this->internet_host_name);
          //$stmt->bindParam(':client_ip_address', $this->client_ip_address);
          $stmt->bindParam(':mac_address', $this->mac_address);

          $stmt->bindParam(1, $this->client_ip_address);
          if($stmt->execute()){
              return true;
          }
          return false;
      }
}
?>
