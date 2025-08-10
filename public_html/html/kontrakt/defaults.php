<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'add') {
        $name = $db->real_escape_string($_POST['name']);
        $text = $db->real_escape_string($_POST['text']);
        $sortorder = intval($_POST['sortorder']);
        $res = $db->query("SELECT COUNT(*) as cnt FROM kontrakt_std WHERE sortorder = $sortorder");
        $row = mysqli_fetch_assoc($res);
        if ($row['cnt'] > 0) {
            $db->query("UPDATE kontrakt_std SET sortorder = sortorder + 1 WHERE sortorder >= $sortorder");
        }
        $db->query("INSERT INTO kontrakt_std (name, text, sortorder) VALUES ('$name', '$text', $sortorder)");
    } elseif ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $name = $db->real_escape_string($_POST['name']);
        $text = $db->real_escape_string($_POST['text']);
        $sortorder = intval($_POST['sortorder']);
        $db->query("UPDATE kontrakt_std SET name = '$name', text = '$text', sortorder = $sortorder WHERE id = $id");
    } elseif ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        $db->query("DELETE FROM kontrakt_std WHERE id = $id");
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<div class="container mt-4">
    <h2>Kontrakt Standard</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Lägg till ny</button>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Namn</th>
            <th>Text</th>
            <th>Sortering</th>
            <th>Åtgärder</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $db->query("SELECT * FROM kontrakt_std ORDER BY CAST(sortorder AS UNSIGNED) ASC");
        while ($row = mysqli_fetch_assoc($res)) {
            $short_text = substr($row['text'], 0, 100) . (strlen($row['text']) > 100 ? '...' : '');
            echo "<tr>
                    <td>{$row['name']}</td>
                    <td>" . htmlspecialchars($short_text) . "</td>
                    <td>{$row['sortorder']}</td>
                    <td>
                        <button class='btn btn-sm btn-warning edit-btn' data-id='{$row['id']}' data-name='{$row['name']}' data-text='" . htmlspecialchars($row['text'], ENT_QUOTES) . "' data-sortorder='{$row['sortorder']}' data-bs-toggle='modal' data-bs-target='#editModal'>Editera</button>
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
                    <h5 class="modal-title">Lägg till standardkontrakt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>Namn</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Text</label>
                            <textarea name="text" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Sortering</label>
                            <input type="number" name="sortorder" class="form-control" required>
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
                    <h5 class="modal-title">Editera standardkontrakt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3">
                            <label>Namn</label>
                            <input type="text" name="name" id="edit-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Text</label>
                            <textarea name="text" id="edit-text" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Sortering</label>
                            <input type="number" name="sortorder" id="edit-sortorder" class="form-control" required>
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
            document.getElementById('edit-name').value = btn.dataset.name;
            document.getElementById('edit-text').value = btn.dataset.text;
            document.getElementById('edit-sortorder').value = btn.dataset.sortorder;
        });
    });
</script>