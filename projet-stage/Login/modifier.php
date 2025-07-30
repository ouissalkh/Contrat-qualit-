
<?php
session_start();
include("php/config.php");

// Vérification de connexion
if (!isset($_SESSION['valid']) || !isset($_SESSION['id'])) {
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Accès refusé</title>
        <style>
            body { font-family: Arial, sans-serif; background: #f8d7da; color: #721c24; display: flex; height: 100vh; justify-content: center; align-items: center; margin: 0; }
            .box { background: white; padding: 30px 40px; border: 1px solid #f5c6cb; border-radius: 8px; text-align: center; }
            a { color: #721c24; font-weight: bold; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>Accès refusé</h1>
            <p>Vous devez être connecté pour accéder à cette page.</p>
            <p><a href="index.php">Retour à la page de connexion</a></p>
        </div>
    </body>
    </html>';
    exit();
}

$current_admin_id = intval($_SESSION['id']);

// Vérification rôle
$resRole = mysqli_query($con, "SELECT role FROM users WHERE id = $current_admin_id");
if (!$resRole || mysqli_num_rows($resRole) === 0) {
    die("Impossible de récupérer votre rôle.");
}
$rowRole = mysqli_fetch_assoc($resRole);
if ($rowRole['role'] !== 'admin') {
    echo '<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>Accès réservé</title>
        <style>
            body { font-family: Arial, sans-serif; background: #e4e9f7; color: #000000ff; display: flex; height: 100vh; justify-content: center; align-items: center; margin: 0; }
            .box { background: white; padding: 30px 40px; border: 1px solid #ffffffff; border-radius: 8px; text-align: center; }
            a { color: #000000ff; font-weight: bold; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>Accès réservé aux administrateurs</h1>
            <p>Vous n\'avez pas la permission d\'accéder à cette page.</p>
            <p><a href="Home.php">Retour à l\'accueil</a></p>
        </div>
    </body>
    </html>';
    exit();
}

$message = "";
$show_error = false;

// Liste des utilisateurs sauf admin courant
$users = [];
$res = mysqli_query($con, "SELECT id, username FROM users WHERE id != $current_admin_id ORDER BY username");
while ($row = mysqli_fetch_assoc($res)) {
    $users[] = $row;
}

// Utilisateur à éditer par défaut
$idToEdit = null;

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['choose'])) {
        $idToEdit = intval($_POST['selected_user']);
    } elseif (isset($_POST['submit'])) {
        $idToEdit = intval($_POST['selected_user']);
        $username = mysqli_real_escape_string($con, trim($_POST['username']));
        $email = mysqli_real_escape_string($con, trim($_POST['email']));
        $role = mysqli_real_escape_string($con, $_POST['role']);
        $password = trim($_POST['password']);

        if (empty($username) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Nom d'utilisateur et email valides requis.";
            $show_error = true;
        } else {
            if ($password !== "") {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username='$username', email='$email', role='$role', password='$hashed_password' WHERE id=$idToEdit";
            } else {
                $sql = "UPDATE users SET username='$username', email='$email', role='$role' WHERE id=$idToEdit";
            }

            if (mysqli_query($con, $sql)) {
                $message = "Utilisateur mis à jour avec succès.";
                $show_error = false;
            } else {
                $message = "Erreur lors de la mise à jour : " . mysqli_error($con);
                $show_error = true;
            }
        }
    }
}

if ($idToEdit === null) {
    if (!empty($users)) {
        $idToEdit = $users[0]['id'];
    } else {
        die("Aucun utilisateur à modifier.");
    }
}

// Charger données utilisateur
$resUser = mysqli_query($con, "SELECT * FROM users WHERE id=$idToEdit");
if (!$resUser || mysqli_num_rows($resUser) == 0) {
    die("Utilisateur introuvable.");
}
$userData = mysqli_fetch_assoc($resUser);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Modifier utilisateur</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    margin: 0;
    padding: 0;
}
.back-button {
    position: fixed;
    top: 20px;
    left: 20px;
    background: #dc3545;
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    z-index: 1000;
}
.back-button:hover {
    background: #c82333;
}
.container {
    max-width: 500px;
    margin: 80px auto 40px;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
}
.field {
    margin-bottom: 15px;
}
label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}
input, select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
button {
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
}
button:hover {
    background: #0056b3;
}
.message {
    padding: 15px 20px;
    margin-bottom: 20px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    animation: fadeIn 0.4s ease-in-out;
}
.success {
    background-color: #d4edda;
    color: #155724;
    border: 1.5px solid #c3e6cb;
}
.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1.5px solid #f5c6cb;
}
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-5px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>
</head>
<body>

<a href="Home.php" class="back-button">Retour</a>

<div class="container">
    <h2>Modifier un utilisateur</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo $show_error ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="modifier.php">
        <div class="field">
            <label for="selected_user">Choisir un utilisateur</label>
            <select name="selected_user" id="selected_user">
                <?php foreach ($users as $u): ?>
                    <option value="<?php echo $u['id']; ?>" <?php if ($u['id'] == $idToEdit) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($u['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="choose">Charger</button>
    </form>

    <hr>

    <form method="post" action="modifier.php">
        <input type="hidden" name="selected_user" value="<?php echo $idToEdit; ?>">

        <div class="field">
            <label>Nom d'utilisateur</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>" required>
        </div>

        <div class="field">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
        </div>

        <div class="field">
            <label>Rôle</label>
            <select name="role" required>
                <option value="user" <?php if ($userData['role']=='user') echo 'selected'; ?>>Utilisateur</option>
                <option value="semi-admin" <?php if ($userData['role']=='semi-admin') echo 'selected'; ?>>Semi-admin</option>
                <option value="admin" <?php if ($userData['role']=='admin') echo 'selected'; ?>>Admin</option>
            </select>
        </div>

        <div class="field">
            <label>Mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" name="password" placeholder="Nouveau mot de passe">
        </div>

        <button type="submit" name="submit">Mettre à jour</button>
    </form>
</div>

</body>
</html>
