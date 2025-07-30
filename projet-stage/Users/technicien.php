<?php
$servername = "10.10.10.55";
$dbname = "indicateur";
$db_username = "cq_projet";
$db_password = "Z9#k*E)dl*o(0I";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = '';
$filterActif = '';
$techniciens = [];

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
}

if (!empty($_GET['actif'])) {
    $filterActif = $_GET['actif'];
}

$fields_res = $conn->query("SHOW COLUMNS FROM liste_techniciens");
$conditions = [];

if ($search !== '') {
    while ($field = $fields_res->fetch_assoc()) {
        $col = $field['Field'];
        if ($col !== 'Actif') {
            $conditions[] = "`$col` LIKE '%" . $conn->real_escape_string($search) . "%'";
        }
    }
}

if ($filterActif === 'vrai') {
    $conditions[] = "`Actif` = 'vrai'";
} elseif ($filterActif === 'faux') {
    $conditions[] = "`Actif` = 'faux'";
}

$sql = "SELECT * FROM liste_techniciens";
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" OR ", $conditions);
}

$result = $conn->query($sql);
if (!$result) {
    die("Erreur SQL: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $techniciens[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des techniciens</title>
<link rel="icon" type="image/png" href="image/logo.png">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1000px;
    margin: 50px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
}
h2 {
    margin: 0 0 20px 0;
    background: linear-gradient(135deg, #667eea, #764ba2);
    font-weight: 700;
    font-size: 1.5rem;
    text-align: center;
}
form {
    text-align: center;
    margin-bottom: 20px;
}
input[type="text"] {
    padding: 8px 12px;
    width: 40%;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    vertical-align: middle;
}

/* Filtre select avec icône et style */
.form-filter-wrapper {
    display: inline-block;
    position: relative;
    margin-left: 10px;
    vertical-align: middle;
}
.form-filter-wrapper select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-left: 35px;
    padding-right: 15px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    height: 30px;
    min-width: 110px;
    box-sizing: border-box;
    vertical-align: middle;
}
.form-filter-wrapper::before {
    content: "\f0b0"; /* icône filtre */
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: white;
    pointer-events: none;
    font-size: 16px;
    user-select: none;
}

button.btn, .btn {
    all: unset;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 10px;
    margin-left: 5px;
    border-radius: 5px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    cursor: pointer;
    text-decoration: none;
    gap: 5px;
    font-family: inherit;
    font-size: inherit;
    height: 30px; /* même hauteur que select */
    vertical-align: middle;
    line-height: 30px;
}
button.btn:hover, .btn:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
}
table {
    width: 100%;
    table-layout: auto;
    border-collapse: collapse;
    border-spacing: 0;
    /* border-radius: 20px; */
    word-wrap: break-word;
    overflow-wrap: break-word;
    border: 4px solid transparent;
    background-clip: padding-box;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    font-size: 0.85rem;
    letter-spacing: 0.03em;
    margin-bottom: 20px;
    min-width: 700px; /* ou une largeur adaptée */
}
thead {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    text-transform: uppercase;
    font-weight: 700;
    font-size: 0.9rem;
}
thead th {
    padding: 10px 20px;
    border-right: 1px solid rgba(255,255,255,0.3);
    white-space: nowrap;
}
thead th:last-child {
    border-right: none;
}
tbody tr {
    background: #ffffff;
    transition: background-color 0.25s ease;
}
tbody tr:nth-child(even) {
    background: #f9f9fb;
}
tbody tr:hover {
    background: #dde6f7;
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(102,126,234,0.2);
}
tbody td {
    padding: 10px 15px;
    border-bottom: 1px solid #e1e6f9;
    color: #34495e;
    text-align: center;
}
tbody td:first-child {
    font-weight: 600;
    color: #5a5a5a;
    text-align: left;
}
.no-result {
    color: #888;
    font-style: italic;
    text-align: center;
}

/* Bouton retour fixé en haut à gauche en dehors du container */
.btn-retour-fixed {
    all: unset;
    position: fixed;
    top: 15px;
    left: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 5px 12px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
    gap: 6px;
    text-decoration: none;
    height: 30px;
    line-height: 30px;
    z-index: 9999;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.btn-retour-fixed:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
}
.table-wrapper {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* pour un scroll fluide sur mobile */
}
</style>
</head>
<body>

<!-- Bouton retour fixé hors container -->
<a href="home.php" class="btn-retour-fixed" title="Retour">
    <i class="fas fa-arrow-left"></i> Retour
</a>

<div class="container">
    <h2>Liste des techniciens</h2>

    <form method="get">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Rechercher...">

        <div class="form-filter-wrapper">
            <select name="actif" onchange="this.form.submit()">
                <option value="" <?= $filterActif === '' ? 'selected' : '' ?>>Filtre</option>
                <option value="vrai" <?= $filterActif === 'vrai' ? 'selected' : '' ?>>Vrai</option>
                <option value="faux" <?= $filterActif === 'faux' ? 'selected' : '' ?>>Faux</option>
            </select>
        </div>

        <button type="submit" class="btn"><i class="fas fa-search"></i></button>
        <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn" title="Rafraîchir"><i class="fas fa-sync"></i></a>
        
    </form>
    <div class="table-wrapper">
        <table>
            <thead>
            <tr>
                <?php if (!empty($techniciens)): ?>
                    <?php foreach (array_keys($techniciens[0]) as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                <?php else: ?>
                    <th>Aucune donnée</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($techniciens)): ?>
                <?php foreach ($techniciens as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?= htmlspecialchars($value) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="100%" class="no-result">Aucun utilisateur trouvé</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>