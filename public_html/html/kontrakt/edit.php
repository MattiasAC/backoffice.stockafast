<?php
// edit.php - Place in /kontrakt/edit.php or handle routing
$id = intval(basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db->query("DELETE FROM kontrakt_paragraphs WHERE contractid = $id");
    $sortorder = 1;
    foreach ($_POST['paragraphs'] as $idx => $para) {
        $name = $db->real_escape_string($para['name']);
        $content = $db->real_escape_string($para['content']);
        if (!empty($name) || !empty($content)) {
            $db->query("INSERT INTO kontrakt_paragraphs (contractid, name, content, sortorder) VALUES ($id, '$name', '$content', $sortorder)");
            $sortorder++;
        }
    }
    header('Location: /kontrakt/');
    exit;
}
$contract = $db->query("SELECT * FROM kontrakt WHERE id = $id")->fetch_assoc();
$res = $db->query("SELECT * FROM kontrakt_paragraphs WHERE contractid = $id ORDER BY sortorder ASC");
$paragraphs = [];
while ($row = $res->fetch_assoc()) {
    $paragraphs[] = $row;
}
if (empty($paragraphs)) {
    $res_std = $db->query("SELECT * FROM kontrakt_std ORDER BY sortorder ASC");
    while ($row_std = $res_std->fetch_assoc()) {
        $paragraphs[] = ['name' => $row_std['name'], 'content' => $row_std['text'], 'sortorder' => $row_std['sortorder']];
    }
}
?>

<div class="container mt-4">
    <h2>Editera paragrafer för <?= $contract['name'] ?></h2>
    <form method="post">
        <div id="paragraphs">
            <?php foreach ($paragraphs as $idx => $p): ?>
                <div class="mb-3 paragraph">
                    <label>Namn</label>
                    <input type="text" name="paragraphs[<?= $idx ?>][name]" value="<?= htmlspecialchars($p['name']) ?>" class="form-control">
                    <label>Innehåll</label>
                    <textarea name="paragraphs[<?= $idx ?>][content]" class="form-control"><?= htmlspecialchars($p['content']) ?></textarea>
                    <button type="button" class="btn btn-danger mt-2" onclick="this.parentElement.remove()">Ta bort</button>
                </div>
            <?php endforeach; ?>
            <div class="mb-3 paragraph">
                <label>Namn</label>
                <input type="text" name="paragraphs[new1][name]" class="form-control">
                <label>Innehåll</label>
                <textarea name="paragraphs[new1][content]" class="form-control"></textarea>
                <button type="button" class="btn btn-danger mt-2" onclick="this.parentElement.remove()">Ta bort</button>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addParagraph()">Lägg till paragraf</button>
        <button type="submit" class="btn btn-primary">Spara</button>
    </form>
</div>

<script>
    let counter = 2;
    function addParagraph() {
        const div = document.createElement('div');
        div.className = 'mb-3 paragraph';
        div.innerHTML = `
        <label>Namn</label>
        <input type="text" name="paragraphs[new${counter}][name]" class="form-control">
        <label>Innehåll</label>
        <textarea name="paragraphs[new${counter}][content]" class="form-control"></textarea>
        <button type="button" class="btn btn-danger mt-2" onclick="this.parentElement.remove()">Ta bort</button>
    `;
        document.getElementById('paragraphs').appendChild(div);
        counter++;
    }
</script>