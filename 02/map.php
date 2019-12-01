<?php

class Database {

    private static $db;
    private $connection;

    private static $servername = "localhost";
    private static $username = "tcundic";
    private static $password = "tcundic";
    private static $dbname = "zadatak_02";

    private function __construct() {
        $this->connection = new MySQLi(self::$servername, self::$username, self::$password, self::$dbname);
    }

    function __destruct() {
        $this->connection->close();
    }

    public static function getConnection() {
        if (self::$db == null) {
            self::$db = new Database();
        }
        return self::$db->connection;
    }
}

function getMap() { 
    $db = Database::getConnection();

    $sql = "SELECT * FROM county";
    $result = $db->query($sql);

    return $result;
}

function getCountyColor($date, $countyId) {
    $db = Database::getConnection();

    $date = date("Y-m-d", strtotime($date));

    $sql = "SELECT * FROM county_stats WHERE stats_date='" . $date . "' AND county_id='" . $countyId . "' LIMIT 1";
    $result = $db->query($sql);

    $row = $result->fetch_array();

    if (is_null($row)) {
        $color = "#CCCCCC";
    } else {
        $red = ($row['red'] * 204) / 100;
        $blue = ($row['blue'] * 204) / 100;
        $color = sprintf("#%02x%02x%02x", $red, 0, $blue);
    }

    return $color; 
}

?>