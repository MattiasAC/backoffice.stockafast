<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $template = $db->real_escape_string($_POST['template'] ?? '');
    if ($action == 'add') {
        $name = $db->real_escape_string($_POST['name']);
        $text = $db->real_escape_string($_POST['text']);
        $sortorder = intval($_POST['sortorder']);
        $res = $db->query("SELECT COUNT(*) as cnt FROM kontrakt_std WHERE template = '$template' AND sortorder = $sortorder");
        $row = mysqli_fetch_assoc($res);
        if ($row['cnt'] > 0) {
            $db->query("UPDATE kontrakt_std SET sortorder = sortorder + 1 WHERE template = '$template' AND sortorder >= $sortorder");
        }
        $db->query("INSERT INTO kontrakt_std (template, name, text, sortorder) VALUES ('$template', '$name', '$text', $sortorder)");
    } elseif ($action == 'edit') {
        $id = intval($_POST['id']);
        $name = $db->real_escape_string($_POST['name']);
        $text = $db->real_escape_string($_POST['text']);
        $sortorder = intval($_POST['sortorder']);
        $db->query("UPDATE kontrakt_std SET template = '$template', name = '$name', text = '$text', sortorder = $sortorder WHERE id = $id");
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        $db->query("DELETE FROM kontrakt_std WHERE id = $id");
    } elseif ($action == 'move_up') {
        $id = intval($_POST['id']);
        $row = $db->query("SELECT template, sortorder FROM kontrakt_std WHERE id = $id")->fetch_assoc();
        if ($row) {
            $tmpl = $db->real_escape_string($row['template']);
            $sort = intval($row['sortorder']);
            $prev = $db->query("SELECT id, sortorder FROM kontrakt_std WHERE template = '$tmpl' AND sortorder < $sort ORDER BY sortorder DESC LIMIT 1")->fetch_assoc();
            if ($prev) {
                $db->query("UPDATE kontrakt_std SET sortorder = {$prev['sortorder']} WHERE id = $id");
                $db->query("UPDATE kontrakt_std SET sortorder = $sort WHERE id = {$prev['id']}");
            }
        }
    } elseif ($action == 'move_down') {
        $id = intval($_POST['id']);
        $row = $db->query("SELECT template, sortorder FROM kontrakt_std WHERE id = $id")->fetch_assoc();
        if ($row) {
            $tmpl = $db->real_escape_string($row['template']);
            $sort = intval($row['sortorder']);
            $next = $db->query("SELECT id, sortorder FROM kontrakt_std WHERE template = '$tmpl' AND sortorder > $sort ORDER BY sortorder ASC LIMIT 1")->fetch_assoc();
            if ($next) {
                $db->query("UPDATE kontrakt_std SET sortorder = {$next['sortorder']} WHERE id = $id");
                $db->query("UPDATE kontrakt_std SET sortorder = $sort WHERE id = {$next['id']}");
            }
        }
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
            <th>Template</th>
            <th>Namn</th>
            <th>Text</th>
            <th>Sortering</th>
            <th>Åtgärder</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $db->query("SELECT * FROM kontrakt_std ORDER BY template ASC, CAST(sortorder AS UNSIGNED) ASC");
        while ($row = mysqli_fetch_assoc($res)) {
            $short_text = substr($row['text'], 0, 100) . (strlen($row['text']) > 100 ? '...' : '');
            echo "<tr>
                    <td>{$row['template']}</td>
                    <td>{$row['name']}</td>
                    <td>" . htmlspecialchars($short_text) . "</td>
                    <td>{$row['sortorder']}</td>
                    <td>
                        <form method='post' style='display:inline;'><input type='hidden' name='action' value='move_up'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='btn btn-sm btn-secondary'>↑</button></form>
                        <form method='post' style='display:inline;'><input type='hidden' name='action' value='move_down'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='btn btn-sm btn-secondary'>↓</button></form>
                        <button class='btn btn-sm btn-warning edit-btn' data-id='{$row['id']}' data-template='{$row['template']}' data-name='{$row['name']}' data-text='" . htmlspecialchars($row['text'], ENT_QUOTES) . "' data-sortorder='{$row['sortorder']}' data-bs-toggle='modal' data-bs-target='#editModal'>Editera</button>
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
                            <label>Template</label>
                            <input type="text" name="template" class="form-control" required>
                        </div>
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
                            <label>Template</label>
                            <input type="text" name="template" id="edit-template" class="form-control" required>
                        </div>
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
            document.getElementById('edit-template').value = btn.dataset.template;
            document.getElementById('edit-name').value = btn.dataset.name;
            document.getElementById('edit-text').value = btn.dataset.text;
            document.getElementById('edit-sortorder').value = btn.dataset.sortorder;
        });
    });
</script>