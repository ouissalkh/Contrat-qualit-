
<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_SESSION['id']);

$query_role = mysqli_query($con, "SELECT role FROM users WHERE id = $id");
$current_user = mysqli_fetch_assoc($query_role);

if (!$current_user || !in_array($current_user['role'], ['admin', 'semi-admin'])) {
    die("Accès refusé.");
}

$query = mysqli_query($con, "SELECT id, username, email, role FROM users ORDER BY username");

$message = $_GET['msg'] ?? null;
$success = isset($_GET['success']) && $_GET['success'] == 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Supprimer un utilisateur</title>
<link rel="icon" type="image/png" href="image/logo.png">
<link rel="stylesheet" href="style/style.css">
<style>
    body, html {
        height: 100%;
        background: #e4e9f7;
    }
    .container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 90vh;
        padding-top: 100px;
        flex-direction: column;
    }
    .box {
        background: #fdfdfd;
        display: flex;
        flex-direction: column;
        padding: 25px 30px;
        border-radius: 20px;
        box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
                    0 32px 64px -48px rgba(0,0,0,0.5);
        width: 450px;
        margin-bottom: 20px;
    }
    .box header {
        font-size: 25px;
        font-weight: 600;
        padding-bottom: 10px;
        border-bottom: 1px solid #e6e6e6;
        margin-bottom: 15px;
        text-align: center;
    }
    form label {
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
    }
    select {
        width: 100%;
        height: 40px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 0 10px;
        outline: none;
        text-align: center;
    }
    button.btn {
        height: 40px;
        background: rgba(1, 137, 248, 0.808);
        border: none;
        border-radius: 5px;
        color: #fff;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
        transition: all 0.3s ease;
        width: 100%;
    }
    button.btn:hover {
        opacity: 0.82;
    }
    a.back-link {
        margin-top: 10px;
        display: block;
        text-align: center;
        color: #1687f5;
        text-decoration: none;
        font-weight: 500;
    }
    a.back-link:hover {
        text-decoration: underline;
    }
    .message-box {
        max-width: 450px;
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .message-success {
        border: 2px solid #699053;
        color: #2a6a1a;
        background: #e1f3d8;
    }
    .message-error {
        border: 2px solid #d9534f;
        color: #842029;
        background: #f8d7da;
    }
</style>
</head>
<body>
<div class="container">

<?php if ($message): ?>
    <div class="message-box <?php echo $success ? 'message-success' : 'message-error'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="box form-box">
    <header>Supprimer un utilisateur</header>
    <form method="post" action="delete.php">
        <label for="user_id">Choisissez un utilisateur à supprimer :</label>
        <select name="user_id" id="user_id" required>
            <option value="" disabled selected>-- Sélectionnez un utilisateur --</option>
            <?php
            while ($user = mysqli_fetch_assoc($query)) {
                // Optionnel: empêcher de supprimer soi-même ici si besoin
                // if ($user['id'] == $id) continue;

                echo "<option value=\"" . $user['id'] . "\">" 
                    . htmlspecialchars($user['username']) . " (" 
                    . htmlspecialchars($user['email']) . ") - " 
                    . htmlspecialchars($user['role']) 
                    . "</option>";
            }
            ?>
        </select>
        <button type="submit" name="delete_user" class="btn"
            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
            Supprimer
        </button>
    </form>
    <a href="home.php" class="back-link">Retour à l'accueil</a>
</div>
</div>
</body>
</html>
