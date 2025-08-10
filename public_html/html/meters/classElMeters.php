<?php
use Altahr\Database;

class ElMeters {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllMeters() {
        $sql = "SELECT * FROM stockafast_se.el_meters ORDER BY meterid";
        $result = $this->db->query($sql);
        $meters = [];
        while ($row = $result->fetch_assoc()) {
            $meters[$row['meterid']] = $row;
        }
        return $meters;
    }

    public function buildHierarchy($meters) {
        $tree = [];
        $lookup = [];
        foreach ($meters as $meter) {
            $lookup[$meter['meterid']] = $meter;
            $lookup[$meter['meterid']]['children'] = [];
        }
        foreach ($meters as $meter) {
            $parent = $meter['parent'];
            if (isset($lookup[$parent])) {
                $lookup[$parent]['children'][] = &$lookup[$meter['meterid']];
            } else {
                $tree[] = &$lookup[$meter['meterid']];
            }
        }
        return $tree;
    }

    public function getData() {
        $allMeters = $this->getAllMeters();
        return $this->buildHierarchy($allMeters);
    }

    public function getMeter($meterid) {
        $sql = "SELECT * FROM stockafast_se.el_meters WHERE meterid = " . intval($meterid);
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    public function updateMeter($data) {
        $meterid = $data['meterid'];
        unset($data['meterid']);
        return $this->db->update("el_meters", $data, "meterid = " . intval($meterid));
    }
}
?>