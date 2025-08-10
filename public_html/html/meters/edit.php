<?php
include("classElMeters.php");
$el = new ElMeters();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = $el->updateMeter($_POST);
    if ($success) {
        echo "<script>window.location.href = '/meters/'</script>";
        exit;
    } else {
        echo 'Uppdatering misslyckades';
    }
} else {
    $meterid = isset($_GET['c']) ? intval($_GET['c']) : 0;
    $meter = $el->getMeter($meterid);
    if (!$meter) {
        echo 'Elmätare inte hittad';
        exit;
    }
}

$fields = [
    'name' => ['label' => 'Namn', 'type' => 'text', 'class'=>'form-control'],
    'showfrom' => ['label' => 'Show From', 'type' => 'text', 'class'=>'datepicker'],
    'deviceid' => ['label' => 'Device ID', 'type' => 'text', 'class'=>'form-control'],
    'branch' => ['label' => 'Gren', 'type' => 'text', 'class'=>'form-control'],
    'brand' => ['label' => 'Brand', 'type' => 'text', 'class'=>'form-control'],
    'parent' => ['label' => 'Parent', 'type' => 'number', 'class'=>'form-control'],
    'reading' => ['label' => 'Reading', 'type' => 'text', 'class'=>'form-control'],
    'rooms' => ['label' => 'Rum', 'type' => 'text', 'class'=>'form-control'],
];

?>

<div class="container-lg mt-5">
    <form method="POST" action="/meters/edit/">
        <input type="hidden" name="meterid" value="<?php echo htmlspecialchars($meter['meterid']); ?>">
        <div class="row">
            <?php
            $fieldKeys = array_keys($fields);
            $half = ceil(count($fieldKeys) / 2);
            $leftColumn = array_slice($fieldKeys, 0, $half);
            $rightColumn = array_slice($fieldKeys, $half);
            ?>
            <div class="col-md-6">
                <?php foreach ($leftColumn as $field): ?>
                    <div class="row mb-2 align-items-center">
                        <label for="<?php echo $field; ?>" class="col-sm-4 col-form-label"><?php echo $fields[$field]['label']; ?></label>
                        <div class="col-sm-8">
                            <input type="<?php echo $fields[$field]['type']; ?>" class="<?php echo $fields[$field]['class']; ?>" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($meter[$field]); ?>" <?php echo $fields[$field]['type'] === 'date' ? 'pattern="\d{4}-\d{2}-\d{2}" placeholder="YYYY-MM-DD"' : ''; ?>>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-6">
                <?php foreach ($rightColumn as $field): ?>
                    <div class="row mb-2 align-items-center">
                        <label for="<?php echo $field; ?>" class="col-sm-4 col-form-label"><?php echo $fields[$field]['label']; ?></label>
                        <div class="col-sm-8">
                            <input type="<?php echo $fields[$field]['type']; ?>" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($meter[$field]); ?>" <?php echo $fields[$field]['type'] === 'date' ? 'pattern="\d{4}-\d{2}-\d{2}" placeholder="YYYY-MM-DD"' : ''; ?>>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Spara</button>
            <a href="/meters/" class="btn btn-secondary">Avbryt</a>
        </div>
    </form>
</div>