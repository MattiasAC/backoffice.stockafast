<?php
include("classLokaler.php");
$lok = new Lokaler();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = $lok->updateRoom($_POST);
    if ($success) {
        echo "<script>window.location.href = '/lokaler/'</script>";
        exit;
    } else {
        echo 'Uppdatering misslyckades';
    }
} else {
    $room = isset($_GET['c']) ? $_GET['c'] : '';
    $local = $lok->getRoom($room);
    if (!$local) {
        echo 'Lokal inte hittad';
        exit;
    }
}

$fields = [
    'name' => ['label' => 'Namn', 'type' => 'text'],
    'description' => ['label' => 'Beskrivning', 'type' => 'textarea'],
    'building' => ['label' => 'Byggnad', 'type' => 'text'],
    'size' => ['label' => 'Storlek', 'type' => 'number'],
];

?>

<div class="container-lg mt-5">
    <form method="POST" action="/lokaler/edit/">
        <input type="hidden" name="room" value="<?php echo htmlspecialchars($local['room']); ?>">
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
                            <?php if ($fields[$field]['type'] === 'textarea'): ?>
                                <textarea class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>"><?php echo htmlspecialchars($local[$field]); ?></textarea>
                            <?php else: ?>
                                <input type="<?php echo $fields[$field]['type']; ?>" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($local[$field]); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-6">
                <?php foreach ($rightColumn as $field): ?>
                    <div class="row mb-2 align-items-center">
                        <label for="<?php echo $field; ?>" class="col-sm-4 col-form-label"><?php echo $fields[$field]['label']; ?></label>
                        <div class="col-sm-8">
                            <?php if ($fields[$field]['type'] === 'textarea'): ?>
                                <textarea class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>"><?php echo htmlspecialchars($local[$field]); ?></textarea>
                            <?php else: ?>
                                <input type="<?php echo $fields[$field]['type']; ?>" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($local[$field]); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Spara</button>
            <a href="/lokaler/" class="btn btn-secondary">Avbryt</a>
        </div>
    </form>
</div>