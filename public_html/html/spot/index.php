<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'add') {
        $year = intval($_POST['year']);
        $month = intval($_POST['month']);
        $se4 = floatval($_POST['se4']);
        $db->query("INSERT INTO spotpriser (year, month, se4) VALUES ($year, $month, $se4)");
    } elseif ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $year = intval($_POST['year']);
        $month = intval($_POST['month']);
        $se4 = floatval($_POST['se4']);
        $db->query("UPDATE spotpriser SET year = $year, month = $month, se4 = $se4 WHERE id = $id");
    } elseif ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        $db->query("DELETE FROM spotpriser WHERE id = $id");
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<div class="container mt-4">
    <h2>Spotpriser</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Lägg till ny</button>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>År</th>
            <th>Månad</th>
            <th>SE4 (öre/kWh)</th>
            <th>Åtgärder</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $db->query("SELECT * FROM spotpriser ORDER BY year DESC, month DESC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <td>{$row['year']}</td>
                    <td>{$row['month']}</td>
                    <td>{$row['se4']}</td>
                    <td>
                        <button class='btn btn-sm btn-warning edit-btn' data-id='{$row['id']}' data-year='{$row['year']}' data-month='{$row['month']}' data-se4='{$row['se4']}' data-bs-toggle='modal' data-bs-target='#editModal'>Editera</button>
                    </td>
                </tr>";
        }
        ?>
        </tbody>
    </table>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lägg till spotpris</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>År</label>
                            <input type="number" name="year" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Månad</label>
                            <input type="number" name="month" class="form-control" min="1" max="12" required>
                        </div>
                        <div class="mb-3">
                            <label>SE4</label>
                            <input type="number" step="0.01" name="se4" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Spara</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editera spotpris</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label>År</label>
                            <input type="number" name="year" id="edit-year" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Månad</label>
                            <input type="number" name="month" id="edit-month" class="form-control" min="1" max="12" required>
                        </div>
                        <div class="mb-3">
                            <label>SE4</label>
                            <input type="number" step="0.01" name="se4" id="edit-se4" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Uppdatera</button>
                    </form>
                    <form method="post" action="" class="mt-3">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete-id">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Är du säker?');">Ta bort</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            document.getElementById('edit-id').value = id;
            document.getElementById('delete-id').value = id;
            document.getElementById('edit-year').value = btn.dataset.year;
            document.getElementById('edit-month').value = btn.dataset.month;
            document.getElementById('edit-se4').value = btn.dataset.se4;
        });
    });
</script>