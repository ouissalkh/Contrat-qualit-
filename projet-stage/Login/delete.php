
<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$current_user_id = intval($_SESSION['id']);
$res = mysqli_query($con, "SELECT role, email, username FROM users WHERE id = $current_user_id");
$current_user = mysqli_fetch_assoc($res);
if (!$current_user || !in_array($current_user['role'], ['admin', 'semi-admin'])) {
    die("Accès refusé.");
}

$current_user_email = $current_user['email'];
$current_user_name = $current_user['username'];

$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = intval($_POST['delete_user_id']);
    if ($delete_user_id === $current_user_id) {
        $message = ["Vous ne pouvez pas vous supprimer vous-même.", false];
    } else {
        $target_res = mysqli_query($con, "SELECT role FROM users WHERE id = $delete_user_id");
        $target = mysqli_fetch_assoc($target_res);
        if (!$target) {
            $message = ["Utilisateur introuvable.", false];
        } elseif ($target['role'] === 'admin' && $current_user_email !== 'otmane@gmail.com') {
            $message = ["Vous ne pouvez pas supprimer un admin.", false];
        } else {
            $delete = mysqli_query($con, "DELETE FROM users WHERE id = $delete_user_id");
            $message = $delete
                ? ["Utilisateur supprimé avec succès.", true]
                : ["Erreur lors de la suppression.", false];
        }
    }
}

$users = mysqli_query($con, "SELECT id, username, email, role FROM users ORDER BY username");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des utilisateurs</title>
<link rel="icon" type="image/png" href="image/logo.png">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f1f5f9;
    margin: 0;
}
.nav {
    background: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 40px;
    height: 70px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 100;
}
.logo img {
    height: 60px;
}
.container {
    max-width: 900px;
    margin: 100px auto 50px;
    background: white;
    padding: 20px;
    border-radius: 10px;
}
.profile-btn span {
    color: #333;
}
h1 {
    text-align: center;
    margin-bottom: 20px;
}
form {
    display: inline;
}
button {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: #dc2626;
    font-size: 16px;
}
button:hover {
    color: #b91c1c;
}
a.edit-icon {
    color: #2563eb;
    text-decoration: none;
    margin-right: 10px;
    font-size: 16px;
}
a.edit-icon:hover {
    color: #1e40af;
}
.message {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}
.success {
    background: #d1fae5;
    color: #065f46;
}
.error {
    background: #fee2e2;
    color: #991b1b;
}
.return-link {
    display: inline-block;
    margin: 10px;
    background-color: #2563eb;
    color: white;
    padding: 10px 18px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
}
.return-link:hover {
    background-color: #1e40af;
}
.dropdown {
    position: relative;
}
.dropdown-menu {
    position: absolute;
    right: 0;
    margin-top: 10px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    min-width: 150px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 200;
}
.dropdown-menu a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
}
.dropdown-menu a:hover {
    background: #f1f1f1;
}
.profile-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    border: none;
    background: none;
    font-weight: 600;
}
.profile-btn img {
    height: 35px;
    width: 35px;
    border-radius: 50%;
}
.profile-btn i {
    color: #000;
}

/* Table scoped styles */
.container table {
  width: 100%;
  table-layout: auto;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 20px;
  word-wrap: break-word;
  overflow-wrap: break-word;
  border: 4px solid transparent;
  background-clip: padding-box;
  overflow: hidden;
  box-shadow: 0 6px 20px rgba(0,0,0,0.05);
  font-size: 0.85rem;
  letter-spacing: 0.03em;
  margin-bottom: 20px;
}

.container thead {
  background: #2563eb; /* pure blue */
  color: white;
  text-transform: uppercase;
  font-weight: 700;
  font-size: 0.9rem;
}

.container thead th {
  padding: 10px 20px;
  border-right: 1px solid rgba(255,255,255,0.3);
  position: relative;
  white-space: nowrap;
}

.container thead th:last-child {
  border-right: none;
}

.container tbody tr {
  background: #ffffff;
  transition: background-color 0.25s ease;
  cursor: default;
}

.container tbody tr:nth-child(even) {
  background: #f9f9fb;
}

.container tbody tr:hover {
  background: #dde6f7;
  transform: translateY(-3px);
  box-shadow: 0 4px 10px rgba(102,126,234,0.2);
}

.container tbody td {
  padding: 10px 15px;
  border-bottom: 1px solid #e1e6f9;
  color: #34495e;
  transition: color 0.25s ease;
  text-align: center;
}

.container tbody td:first-child {
  font-weight: 600;
  color: #5a5a5a;
  text-align: left;
}

.container tfoot td {
  font-weight: 700;
  font-size: 1rem;
  background-color: #f0f0f0;
  color: #333;
  border-top: 2px solid #667eea;
  padding: 15px 20px;
  text-align: center;
}

.container tfoot td:first-child {
  text-align: left;
}
</style>
</head>
<body>

<div class="nav">
    <div class="logo">
        <img src="image/logo.png" alt="Logo">
    </div>
    <div class="dropdown" x-data="{ open: false }" style="color:black;">
        <button @click="open = !open" class="profile-btn">
            <span><?= htmlspecialchars($current_user_name) ?></span>
            <img src="image/profile.jpg" alt="Profile">
            <i class="fas fa-caret-down"></i>
        </button>
        <div x-show="open" @click.outside="open = false" x-transition class="dropdown-menu">
            <a href="#">Profile</a>
            <a href="#">Logs</a>
            <div style="border-top:1px solid #ddd; margin:5px 0;"></div>
            <a href="php/logout.php" style="color: red;">Logout</a>
        </div>
    </div>
</div>

<div class="container">

<h1>Gestion des utilisateurs</h1>

<?php if ($message): ?>
<div class="message <?= $message[1] ? 'success' : 'error' ?>">
    <?= htmlspecialchars($message[0]) ?>
</div>
<?php endif; ?>

<table>
<thead>
<tr>
    <th>Nom d'utilisateur</th>
    <th>Email</th>
    <th>Rôle</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php while ($user = mysqli_fetch_assoc($users)): ?>
<tr>
    <td><?= htmlspecialchars($user['username']) ?></td>
    <td><?= htmlspecialchars($user['email']) ?></td>
    <td><?= htmlspecialchars($user['role']) ?></td>
    <td>
        <?php if ($user['id'] != $current_user_id): ?>
            <a href="modifier.php?id=<?= $user['id'] ?>" class="edit-icon" title="Modifier">
                <i class="fas fa-pen-to-square"></i>
            </a>
            <form method="post" onsubmit="return confirm('Confirmer la suppression ?');" style="display:inline;">
                <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                <button type="submit" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        <?php else: ?>
            <em>Vous</em>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<div style="text-align:center">
    <a href="home.php" class="return-link">Retour à l'accueil</a>
    <a href="register.php" class="return-link">Ajouter un utilisateur</a>
</div>

</div>
</body>
</html>
