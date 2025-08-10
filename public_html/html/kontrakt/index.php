<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'add') {
        $name = $db->real_escape_string($_POST['name']);
        $db->query("INSERT INTO kontrakt (name) VALUES ('$name')");
    } elseif ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $name = $db->real_escape_string($_POST['name']);
        $db->query("UPDATE kontrakt SET name = '$name' WHERE id = $id");
    } elseif ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        $db->query("DELETE FROM kontrakt WHERE id = $id");
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<div class="container mt-4">
    <h2>Kontrakt</h2>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">Lägg till ny</button>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Namn</th>
            <th>Åtgärder</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $db->query("SELECT * FROM kontrakt ORDER BY name ASC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <td>{$row['name']}</td>
                    <td>
                        <button class='btn btn-sm btn-warning edit-btn' data-id='{$row['id']}' data-name='{$row['name']}' data-bs-toggle='modal' data-bs-target='#editModal'>Byt namn</button>
                        <a href='/kontrakt/edit/{$row['id']}/' class='btn btn-sm btn-primary'>Editera</a>
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
                    <h5 class="modal-title">Lägg till kontrakt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label>Namn</label>
                            <input type="text" name="name" class="form-control" required>
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
                    <h5 class="modal-title">Byt namn på kontrakt</h5>
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
        });
    });
</script>