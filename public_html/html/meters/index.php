<?php
include("classElMeters.php");
$elMeters = new ElMeters();
$data = $elMeters->getData();

function formatField($field, $value) {
    if ($field === 'showfrom' && $value) {
        return htmlspecialchars($value);
    }
    return htmlspecialchars($value);
}

function displayMeters($meters, $level = 0) {
    foreach ($meters as $meter) {
        $indent = $level > 0 ? '<span style=color:#CCC>└─ </span>' : '';
        echo '<tr>';
        echo '<td style="text-align: left;">' . $indent . formatField('meterid', $meter['meterid']) . '</td>';
        echo '<td style="text-align: left;">' . formatField('name', $meter['name']) . '</td>';
        echo '<td style="text-align: left;">' . formatField('showfrom', $meter['showfrom']) . '</td>';
        echo '<td style="text-align: left;">' . formatField('deviceid', $meter['deviceid']) . '</td>';
        echo '<td style="text-align: left;">' . formatField('branch', $meter['branch']) . '</td>';
        echo '<td style="text-align: left;">' . formatField('brand', $meter['brand']) . '</td>';
        echo '<td style="text-align: left;">' . formatField('parent', $meter['parent']) . '</td>';
        echo '<td style="text-align: left;">' . formatField('reading', $meter['reading']) . '</td>';
        echo '<td style="text-align: left;">' . implode('<br>', array_map('htmlspecialchars', explode(',', $meter['rooms']))) . '</td>';
        echo '<td style="text-align: left;"><a href="/meters/edit/' . $meter['meterid'] . '/" class="btn btn-primary">Redigera</a></td>';
        echo '</tr>';
        if (!empty($meter['children'])) {
            displayMeters($meter['children'], $level + 1);
        }
    }
}
?>

<div class="container mt-4 ml-0">
    <?php if (!empty($data)): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th style="text-align: left;">Meter ID</th>
                <th style="text-align: left;">Namn</th>
                <th style="text-align: left;">Show From</th>
                <th style="text-align: left;">Device ID</th>
                <th style="text-align: left;">Gren</th>
                <th style="text-align: left;">Brand</th>
                <th style="text-align: left;">Parent</th>
                <th style="text-align: left;">Reading</th>
                <th style="text-align: left;">Rum</th>
                <th style="text-align: left;">Åtgärd</th>
            </tr>
            </thead>
            <tbody>
            <?php displayMeters($data); ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Inga elmätare att visa.</p>
    <?php endif; ?>
</div>