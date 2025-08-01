<?php
session_start();
include("php/config.php");

// Accès restreint à l'admin
if (!isset($_SESSION['valid']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé.");
}

// Récupération des comptes archivés
$sql = "SELECT * FROM compte_sup ORDER BY deleted_at DESC";
$result = mysqli_query($con, $sql);

$logs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $logs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Comptes supprimés archivés</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f9;
    padding: 50px;
}
.container {
    max-width: 900px;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
}
h2 {
    text-align: center;
    color: #2563eb;
}
.table-wrapper {
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #ddd;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-top: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
}
th {
    background-color: #2563eb;
    color: white;
    text-transform: uppercase;
}
tr:nth-child(even) {
    background-color: #f9f9f9;
}
.no-result {
    text-align: center;
    color: #777;
    margin-top: 20px;
}
.return-button {
    display: inline-block;
    padding: 10px 20px;
    margin-bottom: 20px;
    background-color: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
.return-button:hover {
    background-color: #1e40af;
}
</style>
</head>
<body>

<div class="container">
    <a href="home.php" class="return-button">← Retour</a>
    <h2>Archivage des comptes supprimés</h2>

    <?php if (!empty($logs)): ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom utilisateur</th>
                        <th>Email</th>
                        <th>Supprimé par</th>
                        <th>Date suppression</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['id']) ?></td>
                            <td><?= htmlspecialchars($log['deleted_username']) ?></td>
                            <td><?= htmlspecialchars($log['deleted_email']) ?></td>
                            <td><?= htmlspecialchars($log['deleted_by']) ?></td>
                            <td><?= htmlspecialchars($log['deleted_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="no-result">Aucun compte supprimé trouvé.</p>
    <?php endif; ?>
</div>

</body>
</html>
