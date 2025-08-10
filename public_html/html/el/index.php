<?php
use Altahr\Database;

class El {
    public $all;
    private $db;

    function __construct() {
        $this->db = new Database();
        $this->init();

        $sort = isset($_GET['b']) ? $_GET['b'] : 'invoice';
        $order = isset($_GET['c']) ? $_GET['c'] : 'desc';

        uasort($this->all, function($a, $b) use ($sort, $order) {
            $valA = $a[$sort] === ' - ' ? '0000-00-00' : $a[$sort];
            $valB = $b[$sort] === ' - ' ? '0000-00-00' : $b[$sort];
            return $order === 'asc' ? strcmp($valA, $valB) : strcmp($valB, $valA);
        });
    }

    private function init() {
        $this->all = [];

        $invoiced = [];
        $res = $this->db->query("SELECT clientid, MAX(date) AS date FROM el_invoiced GROUP BY clientid");
        while ($row = mysqli_fetch_assoc($res)) {
            $invoiced[$row['clientid']] = $row['date'];
        }

        $clients = $this->db->selectArray('hyreslista', 'clientid', 'active IN (1,4,5)');
        $allrooms = $this->db->selectArray('lokaler', 'room', '1=1');
        $meters = $this->db->selectArray('el_meters', 'meterid', '1=1');

        foreach ($meters as $meter) {
            $rooms = explode(',', $meter['rooms']);
            foreach ($rooms as $room) {
                if (isset($allrooms[$room])) {
                    $allrooms[$room]['meters'][] = [
                        'meterid' => $meter['meterid'],
                        'name' => $meter['name']
                    ];
                }
            }
        }

        foreach ($clients as $client) {
            $clientId = $client['clientid'];
            $this->all[$clientId] = [
                'name' => $client['name'],
                'invoice' => isset($invoiced[$clientId]) ? $invoiced[$clientId] : ' - ',
                'rooms' => []
            ];

            $rooms = explode(',', $client['rooms']);
            foreach ($rooms as $room) {
                if (isset($allrooms[$room])) {
                    $this->all[$clientId]['rooms'][] = [
                        'name' => $allrooms[$room]['room'],
                        'description' => $allrooms[$room]['description'],
                        'meters' => $allrooms[$room]['meters'] ?? []
                    ];
                }
            }

            usort($this->all[$clientId]['rooms'], function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }
    }
}

$el = new El();
$sort = isset($_GET['b']) ? $_GET['b'] : 'name';
$dir = isset($_GET['c']) ? $_GET['c'] : 'asc';

$nameOrder = ($sort === 'name' && $dir === 'asc') ? 'desc' : 'asc';
$invoiceOrder = ($sort === 'invoice' && $dir === 'asc') ? 'desc' : 'asc';
?>

<div class="container mt-4 ml-0">
    <table class="table table-striped" style="margin-left: 0; margin-right: auto;">
        <thead>
        <tr>
            <th style="text-align: left;"><a href="/el/name/<?php echo $nameOrder; ?>/">Hyresgäst</a></th>
            <th style="text-align: left;">Lokal</th>
            <th style="text-align: left;">Elmätare</th>
            <th style="text-align: left;"><a href="/el/invoice/<?php echo $invoiceOrder; ?>/">Fakturat</a></th>
            <th style="text-align: left;">Åtgärd</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($el->all as $clientId => $client): ?>
            <tr>
                <td style="text-align: left;max-width:150px"><?php echo htmlspecialchars($client['name']); ?></td>
                <td style="text-align: left;">
                    <select class="form-select" style="width:150px">
                        <?php foreach ($client['rooms'] as $room): ?>
                            <option><?php echo htmlspecialchars($room['name'] . ' - ' . $room['description']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="text-align: left;">
                    <select class="form-select" style="width:150px">
                        <?php foreach ($client['rooms'] as $room): ?>
                            <?php foreach ($room['meters'] ?? [] as $meter): ?>
                                <option><?php echo htmlspecialchars($meter['meterid'] . ' - ' . $meter['name'] . ' (' . $room['name'] . ')'); ?></option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td style="text-align: left;"><?php echo htmlspecialchars($client['invoice']); ?></td>
                <td style="text-align: left;"><a href="/el/invoicing/<?php echo $clientId; ?>/" class="btn btn-primary">Redigera</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>