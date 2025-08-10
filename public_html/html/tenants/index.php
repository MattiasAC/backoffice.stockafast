
<?php
include("classTenants.php");
$db = new Tenants();

$areas = $db->getAreas();
$activeStatuses = $db->getActiveStatuses();
$fields = $db->getFields();
$fieldLabels = $db->getFieldLabels();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['selectedAreas'] = $_POST['areas'] ?? $areas;
    $_SESSION['selectedFields'] = $_POST['fields'] ?? ['clientid', 'name', 'area'];
    $_SESSION['selectedActive'] = $_POST['active'] ?? [1, 2];
}

$selectedAreas = $_SESSION['selectedAreas'] ?? $areas;
$selectedFields = $_SESSION['selectedFields'] ?? ['clientid', 'name', 'area'];
$selectedActive = $_SESSION['selectedActive'] ?? [1, 2];
$data = (!empty($selectedAreas) && !empty($selectedActive)) ? $db->getData($selectedAreas, $selectedFields, $selectedActive) : [];
$summary = (!empty($selectedAreas) && !empty($selectedActive)) ? $db->getSummary($selectedAreas, $selectedActive) : [];

function formatField($field, $value, $activeStatuses) {
    switch ($field) {
        case 'size':
            return htmlspecialchars($value) . ' kvm';
        case 'monthly_exvat':
        case 'total_yearly_exvat':
            return number_format($value, 0, ',', ' ') . ' kr';
        case 'price_per_sqm':
            return number_format($value, 0, ',', ' ') . ' kr/kvm';
        case 'active':
            return htmlspecialchars($activeStatuses[$value] ?? $value);
        default:
            return htmlspecialchars($value);
    }
}
?>

<style>
    .larger-checkbox {
        transform: scale(1.5);
        margin-right: 8px;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <div class="navbar-nav">
            <form method="POST" class="d-flex">
                <div class="dropdown me-2">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Välj områden
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($areas as $area): ?>
                            <li>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="larger-checkbox" name="areas[]" value="<?php echo htmlspecialchars($area); ?>"
                                        <?php echo in_array($area, $selectedAreas) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($area); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Välj fält
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($fields as $field): ?>
                            <li>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="larger-checkbox" name="fields[]" value="<?php echo htmlspecialchars($field); ?>"
                                        <?php echo in_array($field, $selectedFields) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($fieldLabels[$field] ?? $field); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Välj aktiv status
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($activeStatuses as $value => $label): ?>
                            <li>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="larger-checkbox" name="active[]" value="<?php echo htmlspecialchars($value); ?>"
                                        <?php echo in_array($value, $selectedActive) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="submit" class="btn btn-primary">Visa</button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-4 ml-0">
    <?php if (!empty($data)): ?>
        <table class="table table-striped" style="margin-left: 0; margin-right: auto;">
            <thead>
            <tr>
                <?php foreach ($selectedFields as $field): ?>
                    <th style="text-align: left;"><?php echo htmlspecialchars($fieldLabels[$field] ?? $field); ?></th>
                <?php endforeach; ?>
                <th style="text-align: left;">Åtgärd</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($selectedFields as $field): ?>
                        <td style="text-align: left;"><?php echo formatField($field, $row[$field], $activeStatuses); ?></td>
                    <?php endforeach; ?>
                    <td style="text-align: left;">
                        <a href="/tenants/edit/<?php echo $row['clientid']; ?>/" class="btn btn-primary">Redigera</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($summary)): ?>
            <div class="card mt-4">
                <div class="card-header">Sammanfattning</div>
                <div class="card-body">
                    <p><strong>Uthyrda kvadratmeter:</strong> <?php echo number_format($summary['total_size'], 0, ',', ' ') . ' kvm'; ?></p>
                    <p><strong>Total månadshyra:</strong> <?php echo number_format($summary['total_yearly'] / 12, 0, ',', ' ') . ' kr'; ?></p>
                    <p><strong>Total årshyra:</strong> <?php echo number_format($summary['total_yearly'], 0, ',', ' ') . ' kr'; ?></p>
                    <p><strong>Genomsnittligt pris per kvadratmeter:</strong> <?php echo number_format($summary['avg_price_per_sqm'], 0, ',', ' ') . ' kr/kvm'; ?></p>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p>Välj minst ett område, fält och aktiv status för att visa data.</p>
    <?php endif; ?>
</div>