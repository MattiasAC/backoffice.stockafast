<?php
use Altahr\Database;

class Invoicing
{
    private $db;
    public $clientId;
    public $clientName;
    public $fortnox;
    public $elavtal;
    public $vat;
    public $rooms = [];
    public $meters = [];
    public $measurementsByMeter = [];
    public $invoicings = [];
    public $meterMap = [];
    public $spot = [];
    public $price;
    public $kwh_measured = 0;
    public $kwh_invoiced = 0;
    public $kwh_toinvoice = 0;
    public $fileName;
    public $showTo;

    public function __construct($clientId, $showTo = null)
    {
        $this->db = new Database();
        $this->clientId = $clientId;
        $this->showTo = $showTo ?? date('Y-m-01');
        $this->init();
    }

    private function init()
    {
        // Fetch client data
        $client = $this->db->selectOne('hyreslista', 'clientid = ' . $this->clientId);
        if ($client) {
            $this->clientName = $client['name'] ?? '';
            $this->fortnox = $client['fortnox'] ?? '';
            $this->elavtal = $client['elavtal'] ?? '';
            $this->vat = $client['vat'] ?? '';
            $this->rooms = explode(',', $client['rooms'] ?? '');
        }

        // Fetch all meters
        $allMeters = $this->db->selectArray('el_meters', 'meterid', '1=1');

        // Find client's meters
        foreach ($allMeters as $meter) {
            $meterRooms = explode(',', $meter['rooms']);
            foreach ($this->rooms as $room) {
                if (in_array($room, $meterRooms)) {
                    $this->meters[] = ['meterid' => $meter['meterid'], 'name' => $meter['name'], 'room' => $room, 'showfrom' => $meter['showfrom'] ?? null];
                }
            }
        }

        // Build meter map
        foreach ($this->meters as $m) {
            $this->meterMap[$m['meterid']] = $m['name'];
        }

        // Fetch measurements per meter with filters
        foreach ($this->meters as $meter) {
            $meterid = $meter['meterid'];
            $showFrom = $meter['showfrom'];
            $where = "meterid = " . intval($meterid) . " AND datetime <= '{$this->showTo} 23:59:59'";
            if ($showFrom) {
                $where .= " AND datetime >= '{$showFrom} 00:00:00'";
            }
            $res = $this->db->query("SELECT * FROM el_measured WHERE $where ORDER BY datetime ASC");
            while ($row = mysqli_fetch_assoc($res)) {
                $row['meter_name'] = $this->meterMap[$meterid] ?? '';
                $row['type'] = 1;
                $this->measurementsByMeter[$meterid][] = $row;
            }
        }

        // Fetch invoicings (sorted ASC)
        $res = $this->db->query("SELECT * FROM el_invoiced WHERE clientid = {$this->clientId} ORDER BY date ASC");
        while ($row = mysqli_fetch_assoc($res)) {
            $this->invoicings[] = $row;
            $this->kwh_invoiced += $row['kwh'];
        }

        // Set spot data if applicable
        if (strpos($this->elavtal, 'spot') !== false) {
            $res = $this->db->query("SELECT * FROM spotpriser ORDER BY year DESC, month DESC LIMIT 3");
            $spots = [];
            while ($row = mysqli_fetch_assoc($res)) {
                $spots[] = ['year' => $row['year'], 'month' => $row['month'], 'se4' => $row['se4']];
            }
            $this->spot = array_reverse($spots);
        }

        if ($this->elavtal == 1.50) {
            $this->price = 1.50;
        } elseif (strpos($this->elavtal, "spot") !== false) {
            foreach ($this->spot as $spot) {
                if ($spot["year"] == date("Y", strtotime($this->showTo) - 86400 * 15) && $spot["month"] == date("m", strtotime($this->showTo) - 86400 * 15)) {
                    $this->price = number_format(($spot["se4"] + str_replace("spot+", "", $this->elavtal)) / 100, 2, ".", "");
                }
            }
        }

        // Calculate kwh_measured
        foreach ($this->measurementsByMeter as $measurements) {
            if (!empty($measurements)) {
                $first = $measurements[0]['value'];
                $last = $measurements[count($measurements) - 1]['value'];
                $this->kwh_measured += $last - $first;
            }
        }

        // Calculate kwh_toinvoice
        $this->kwh_toinvoice = $this->kwh_measured - $this->kwh_invoiced;

        // Set fileName
        $this->fileName = "el_" . htmlspecialchars($this->clientId) . ".pdf";
    }
}
?>