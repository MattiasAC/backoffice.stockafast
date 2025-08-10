
<?php
include("classTenants.php");
$db = new Tenants();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = $db->updateTenant($_POST);
    if ($success) {
        echo "<script>window.location.href = '/tenants/'</script>";
        exit;
    } else {
        echo 'Uppdatering misslyckades';
    }
} else {
    $clientid = isset($_GET['c']) ? intval($_GET['c']) : 0;
    $tenant = $db->getTenant($clientid);
    if (!$tenant) {
        echo 'Hyresgäst inte hittad';
        exit;
    }
}

$fields = $db->getEditFields();
?>

<div class="container-lg mt-5">
    <form method="POST" action="/tenants/edit/">
        <input type="hidden" name="clientid" value="<?php echo htmlspecialchars($tenant['clientid']); ?>">
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
                                <textarea class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>"><?php echo htmlspecialchars($tenant[$field]); ?></textarea>
                            <?php elseif ($fields[$field]['type'] === 'select'): ?>
                                <select class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>">
                                    <?php foreach ($fields[$field]['options'] as $value => $label): ?>
                                        <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $tenant[$field] == $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="<?php echo $fields[$field]['type']; ?>" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($tenant[$field]); ?>" <?php echo isset($fields[$field]['step']) ? 'step="' . $fields[$field]['step'] . '"' : ''; ?> <?php echo $fields[$field]['type'] === 'date' ? 'pattern="\d{4}-\d{2}-\d{2}" placeholder="YYYY-MM-DD"' : ''; ?>>
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
                                <textarea class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>"><?php echo htmlspecialchars($tenant[$field]); ?></textarea>
                            <?php elseif ($fields[$field]['type'] === 'select'): ?>
                                <select class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>">
                                    <?php foreach ($fields[$field]['options'] as $value => $label): ?>
                                        <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $tenant[$field] == $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="<?php echo $fields[$field]['type']; ?>" class="form-control" id="<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo htmlspecialchars($tenant[$field]); ?>" <?php echo isset($fields[$field]['step']) ? 'step="' . $fields[$field]['step'] . '"' : ''; ?> <?php echo $fields[$field]['type'] === 'date' ? 'pattern="\d{4}-\d{2}-\d{2}" placeholder="YYYY-MM-DD"' : ''; ?>>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Spara</button>
            <a href="/tenants/" class="btn btn-secondary">Avbryt</a>
        </div>
    </form>
</div>