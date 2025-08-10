<?php
include("classPdf.php");
include("classInvoicing.php");
include("classFortnox.php");
$clientId = isset($_GET["c"]) ? $_GET["c"] : '';
if (isset($_POST['show_to'])) {
    $_SESSION['show_to'] = $_POST['show_to'];
}
$showTo = isset($_SESSION['show_to']) ? $_SESSION['show_to'] : date('Y-m-01');
$inv = new Invoicing($clientId, $showTo);
$filename = $inv->fileName;
$pdf = new PDF($inv);
$pdf->generate($filename);
?>

<div class="container-fluid mt-4">
    <form method="post">
        <label for="show_to">Visa endast mätningar t.o.m.:</label>
        <input type="text" id="show_to" name="show_to" value="<?php echo $inv->showTo; ?>" class="datepicker">
        <button type="submit">Uppdatera</button>
    </form>
    <div class="row">
        <div class="col-md-4">
            <h2><?php echo htmlspecialchars($inv->clientName); ?> (ID: <?php echo htmlspecialchars($inv->clientId); ?>)</h2>
            <p>Fortnox: <?php echo htmlspecialchars($inv->fortnox); ?></p>
            <p>Elavtal: <?php echo htmlspecialchars($inv->elavtal); ?></p>
            <?php include("fortnox.php");?>
            <div class="dropdown mb-3">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="invoicingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Faktureringar
                </button>
                <ul class="dropdown-menu" aria-labelledby="invoicingsDropdown">
                    <?php if (empty($inv->invoicings)): ?>
                        <li><a class="dropdown-item">Inga faktureringar</a></li>
                    <?php else: ?>
                        <?php foreach (array_reverse($inv->invoicings) as $i): ?>
                            <li>
                                <a class="dropdown-item">
                                    <?php echo htmlspecialchars($i['date'] . ' - ' . $i['invoiceid'] . ' - ' . $i['kwh'] . ' kWh'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <?php if (empty($inv->meters)): ?>
                <p>Inga mätare</p>
            <?php else: ?>
                <?php foreach ($inv->meters as $meter): ?>
                    <div class="dropdown mb-3">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="measurementsDropdown_<?php echo $meter['meterid']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                            [<?php echo htmlspecialchars($meter['meterid']); ?>] <?php echo htmlspecialchars($meter['name']); ?> (Visas från <?php echo $meter['showfrom']; ?>)
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="measurementsDropdown_<?php echo $meter['meterid']; ?>">
                            <?php $meterMeasurements = $inv->measurementsByMeter[$meter['meterid']] ?? []; ?>
                            <?php if (empty($meterMeasurements)): ?>
                                <li><a class="dropdown-item">Inga mätningar</a></li>
                            <?php else: ?>
                                <?php foreach (array_reverse($meterMeasurements) as $m): ?>
                                    <li>
                                        <a class="dropdown-item">
                                            <?php echo htmlspecialchars($m['datetime'] . ' - ' . $m['value'] . ' kWh'); ?>
                                            <?php if ($m['image']): ?>
                                                <img src="<?php echo htmlspecialchars($m['image']); ?>" alt="Mätarbild" style="max-width: 100px; height: auto;">
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <iframe src="/html/el/pdf/<?php echo $filename; ?>?t=<?=time()?>" width="100%" height="800px"></iframe>
        </div>
    </div>
</div>