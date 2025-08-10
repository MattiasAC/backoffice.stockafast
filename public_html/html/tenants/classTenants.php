<?php
use Altahr\Database;

class Tenants {
    private $conn;

    public function __construct() {
        $this->conn = new Database();
    }

    public function getAreas() {
        $sql = "SELECT DISTINCT area FROM stockafast_se.hyreslista";
        $result = $this->conn->query($sql);
        $areas = [];
        while ($row = $result->fetch_assoc()) {
            $areas[] = $row['area'];
        }
        return $areas;
    }

    public function getActiveStatuses() {
        return [
            0 => 'Avflyttad',
            1 => 'Aktiv',
            2 => 'Vakant',
            4 => 'Extra',
            5 => 'Gratis'
        ];
    }

    public function getFields() {
        return [
            'clientid', 'name', 'size', 'monthly_exvat', 'yearly_fee_exvat', 'monthly_exvat_orig',
            'contract_from', 'email', 'cancellation', 'index', 'next_index', 'active', 'area',
            'vat', 'contract_to', 'invoicefrequency', 'information', 'fortnox', 'elavtal', 'rooms',
            'telephone', 'total_yearly_exvat', 'price_per_sqm'
        ];
    }

    public function getFieldLabels() {
        return [
            'clientid' => 'Klient-ID',
            'name' => 'Namn',
            'size' => 'Storlek',
            'monthly_exvat' => 'Hyra ex. moms',
            'yearly_fee_exvat' => 'Årlig avgift ex. moms',
            'monthly_exvat_orig' => 'Ursprunglig hyra ex. moms',
            'contract_from' => 'Kontrakt från',
            'email' => 'E-post',
            'cancellation' => 'Uppsägning',
            'index' => 'Index',
            'next_index' => 'Nästa index',
            'active' => 'Status',
            'area' => 'Område',
            'vat' => 'Moms',
            'contract_to' => 'Kontrakt till',
            'invoicefrequency' => 'Fakturafrekvens',
            'information' => 'Information',
            'fortnox' => 'Fortnox',
            'elavtal' => 'Elavtal',
            'rooms' => 'Rum',
            'telephone' => 'Telefon',
            'total_yearly_exvat' => 'Total årskostnad ex. moms',
            'price_per_sqm' => 'Pris per kvm'
        ];
    }

    public function getEditFields() {
        return [
            'active' => ['label' => 'Aktiv', 'type' => 'select', 'options' => $this->getActiveStatuses()],
            'area' => ['label' => 'Område', 'type' => 'text'],
            'cancellation' => ['label' => 'Uppsägning', 'type' => 'textarea'],
            'contract_from' => ['label' => 'Kontrakt från', 'type' => 'date'],
            'contract_to' => ['label' => 'Kontrakt till', 'type' => 'date'],
            'elavtal' => ['label' => 'Elavtal', 'type' => 'text'],
            'email' => ['label' => 'E-post', 'type' => 'email'],
            'fortnox' => ['label' => 'Fortnox', 'type' => 'number'],
            'index' => ['label' => 'Index', 'type' => 'number', 'step' => '0.01'],
            'information' => ['label' => 'Information', 'type' => 'text'],
            'invoicefrequency' => ['label' => 'Fakturafrekvens', 'type' => 'number'],
            'monthly_exvat' => ['label' => 'Hyra ex. moms', 'type' => 'number', 'step' => '0.01'],
            'monthly_exvat_orig' => ['label' => 'Ursprunglig hyra ex. moms', 'type' => 'number'],
            'name' => ['label' => 'Namn', 'type' => 'text'],
            'next_index' => ['label' => 'Nästa index', 'type' => 'date'],
            'rooms' => ['label' => 'Rum', 'type' => 'text'],
            'size' => ['label' => 'Storlek', 'type' => 'number'],
            'telephone' => ['label' => 'Telefon', 'type' => 'text'],
            'vat' => ['label' => 'Moms', 'type' => 'number'],
            'yearly_fee_exvat' => ['label' => 'Årlig avgift ex. moms', 'type' => 'number']
        ];
    }

    public function getData($selectedAreas, $selectedFields, $selectedActive) {
        $baseFields = array_diff($selectedFields, ['total_yearly_exvat', 'price_per_sqm']);
        $fields = empty($baseFields) ? ['clientid'] : $baseFields;
        $fieldsSql = implode(", ", $fields);
        $areas = implode("','", $selectedAreas);
        $active = implode(",", $selectedActive);
        $sql = "SELECT $fieldsSql";
        if (in_array('total_yearly_exvat', $selectedFields)) {
            $sql .= ", (monthly_exvat * 12 + yearly_fee_exvat) AS total_yearly_exvat";
        }
        if (in_array('price_per_sqm', $selectedFields)) {
            $sql .= ", CASE WHEN size = 0 THEN 0 ELSE ROUND((monthly_exvat * 12 + yearly_fee_exvat) / size) END AS price_per_sqm";
        }
        $sql .= " FROM stockafast_se.hyreslista WHERE area IN ('$areas') AND active IN ($active) ORDER BY name";
        $result = $this->conn->query($sql);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    public function getSummary($selectedAreas, $selectedActive) {
        $areas = implode("','", $selectedAreas);
        $active = implode(",", $selectedActive);
        $sql = "SELECT SUM(size) AS total_size, SUM(monthly_exvat * 12 + yearly_fee_exvat) AS total_yearly, AVG(CASE WHEN size = 0 THEN 0 ELSE (monthly_exvat * 12 + yearly_fee_exvat) / size END) AS avg_price_per_sqm
                FROM stockafast_se.hyreslista 
                WHERE area IN ('$areas') AND active IN ($active)";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    public function getTenant($clientid) {
        $sql = "SELECT * FROM stockafast_se.hyreslista WHERE clientid = " . intval($clientid);
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    public function updateTenant($data) {
        $clientid = $data['clientid'];
        unset($data['clientid']);
        return $this->conn->update("hyreslista", $data, "clientid = " . intval($clientid));
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>
