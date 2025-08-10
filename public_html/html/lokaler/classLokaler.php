<?php
use Altahr\Database;

class Lokaler {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getBuildings() {
        $sql = "SELECT DISTINCT building FROM stockafast_se.lokaler ORDER BY building";
        $result = $this->db->query($sql);
        $buildings = [];
        while ($row = $result->fetch_assoc()) {
            $buildings[] = $row['building'];
        }
        return $buildings;
    }

    public function getRoomToTenantMap() {
        $sql = "SELECT clientid, name, rooms, active FROM stockafast_se.hyreslista WHERE active IN (0,1,2,4,5)";
        $result = $this->db->query($sql);
        $map = [];
        while ($row = $result->fetch_assoc()) {
            $rooms = explode(',', $row['rooms']);
            foreach ($rooms as $room) {
                $room = trim($room);
                if ($room) {
                    $map[$room] = ($row['active'] == 5 || $row['active'] == 0) ? 'Vakant' : $row['name'];
                }
            }
        }
        return $map;
    }

    public function getRoomToMetersMap() {
        $sql = "SELECT meterid, name, rooms FROM stockafast_se.el_meters";
        $result = $this->db->query($sql);
        $map = [];
        while ($row = $result->fetch_assoc()) {
            $rooms = explode(',', $row['rooms']);
            foreach ($rooms as $room) {
                $room = trim($room);
                if ($room) {
                    $map[$room][] = $row['name'];
                }
            }
        }
        return $map;
    }

    public function getData($selectedBuildings) {
        $buildings = implode("','", $selectedBuildings);
        $sql = "SELECT * FROM stockafast_se.lokaler WHERE building IN ('$buildings') ORDER BY building, room";
        $result = $this->db->query($sql);
        $data = [];
        $tenantMap = $this->getRoomToTenantMap();
        $meterMap = $this->getRoomToMetersMap();
        while ($row = $result->fetch_assoc()) {
            $building = $row['building'];
            if (!isset($data[$building])) {
                $data[$building] = [];
            }
            $row['tenant'] = isset($tenantMap[$row['room']]) ? $tenantMap[$row['room']] : '';
            $row['meters'] = isset($meterMap[$row['room']]) ? $meterMap[$row['room']] : [];
            $data[$building][] = $row;
        }
        return $data;
    }

    public function getRoom($room) {
        $sql = "SELECT * FROM stockafast_se.lokaler WHERE room = '" . $this->db->escape($room) . "'";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    public function updateRoom($data) {
        $room = $data['room'];
        unset($data['room']);
        return $this->db->update("lokaler", $data, "room = '" . $this->db->escape($room) . "'");
    }
}
?>