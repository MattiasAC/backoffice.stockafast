<?php
include("classLokaler.php");
$lokaler = new Lokaler();
$buildings = $lokaler->getBuildings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['selectedBuildings'] = $_POST['buildings'] ?? $buildings;
}

$selectedBuildings = $_SESSION['selectedBuildings'] ?? $buildings;
$data = $lokaler->getData($selectedBuildings);

function formatField($field, $value) {
    switch ($field) {
        case 'size':
            return number_format($value, 0, ',', ' ') . ' kvm';
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
    .thumbnail {
        max-width: 200px;
        cursor: pointer;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <div class="navbar-nav">
            <form method="POST" class="d-flex">
                <div class="dropdown me-2">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Välj byggnader
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($buildings as $building): ?>
                            <li>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="larger-checkbox" name="buildings[]" value="<?php echo htmlspecialchars($building); ?>"
                                        <?php echo in_array($building, $selectedBuildings) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($building); ?>
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
        <?php foreach ($data as $building => $rows): ?>
            <div class="mb-4">
                <h3><?php echo htmlspecialchars($building); ?></h3>
                <?php
                $image = "html/lokaler/images/{$building}.png";
                if(file_exists($image)){
                    echo "<img src=\"/{$image}\" class=\"img-fluid thumbnail mb-3\" onclick=\"this.classList.toggle('thumbnail');\">";
                }
                ?>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th style="text-align: left;">Rum</th>
                        <th style="text-align: left;">Namn</th>
                        <th style="text-align: left;">Beskrivning</th>
                        <th style="text-align: left;">Storlek</th>
                        <th style="text-align: left;">Elmätare</th>
                        <th style="text-align: left;">Hyresgäst</th>
                        <th style="text-align: left;">Åtgärd</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td style="text-align: left;"><?php echo formatField('room', $row['room']); ?></td>
                            <td style="text-align: left;"><?php echo formatField('name', $row['name']); ?></td>
                            <td style="text-align: left;"><?php echo formatField('description', $row['description']); ?></td>
                            <td style="text-align: left;"><?php echo formatField('size', $row['size']); ?></td>
                            <td class="text-muted" style="text-align: left;"><?php echo implode('<br>', array_map('htmlspecialchars', $row['meters'])); ?></td>
                            <td class="text-muted" style="text-align: left;"><?php echo formatField('tenant', $row['tenant']); ?></td>
                            <td style="text-align: left;">
                                <a href="/lokaler/edit/<?php echo htmlspecialchars($row['room']); ?>/" class="btn btn-primary">Redigera</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Inga lokaler att visa.</p>
    <?php endif; ?>
</div>