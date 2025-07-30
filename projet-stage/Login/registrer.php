<?php
session_start();
include("php/config.php");

// Vérification de la session et du rôle admin
if (!isset($_SESSION['valid'])) {
    exit();
}

$id = intval($_SESSION['id']);
$query = mysqli_query($con, "SELECT role FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($query);

if (!$user || $user['role'] !== 'admin') {
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
            <p><a href="delete.php">Retour à l\'accueil</a></p>
        </div>
    </body>
    </html>';
    exit();
}

// Traitement du formulaire d'inscription
if(isset($_POST['submit'])){
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $role = mysqli_real_escape_string($con, $_POST['role']);

    // Vérification de l’unicité de l’e-mail
    $verify_query = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");

    if(mysqli_num_rows($verify_query) != 0 ){
        $message = "Cet e-mail est déjà utilisé, veuillez en essayer un autre !";
    } else {
        // Stocker le mot de passe en clair (non recommandé)
        $insert = mysqli_query($con, "INSERT INTO users(username, email, password, role) VALUES('$username', '$email', '$password', '$role')");

        if($insert){
            $message = "Inscription réussie !";
        } else {
            $message = "Une erreur est survenue lors de l'inscription.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/png" href="image/logo.png">
<link rel="stylesheet" href="style/style.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<!-- Font Awesome -->
<link
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  rel="stylesheet"
/>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<title>Inscription</title>
</head>
<body>
<div class="container">
    <div class="box form-box">
        <?php 
        if(isset($message)){
            echo "<div class='message'><p>" . htmlspecialchars($message) . "</p></div><br>";
            if($message === "Inscription réussie !"){
                echo "<a href='home.php'><button class='btn'>Accueil</button></a>";
            } else {
                echo "<a href='register.php'><button class='btn'>Retour</button></a>";
            }
        } else {
        ?>
        <header>Créer un compte</header>
        <form action="" method="post">
            <div class="field input">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" name="username" id="username" autocomplete="off" required>
            </div>

            <div class="field input">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" autocomplete="off" required>
            </div>

            <div class="field input">
                <label for="password">Mot de passe</label>
                <input type="password" name="password" id="password" autocomplete="off" required>
            </div>

            <div class="field input">
                <label for="role">Rôle</label>
                <select name="role" id="role" required style="
                    height: 40px;
                    width: 100%;
                    font-size: 16px;
                    padding: 0 10px;
                    border-radius: 5px;
                    border: 1px solid #ccc;
                    outline: none;">
                    <option value="" disabled selected style="text-align:center">-- Sélectionner un rôle --</option>
                    <option value="admin">Admin</option>
                    <option value="semi-admin">Semi-Admin</option>
                    <option value="manager">Manager</option>
                    <option value="user">Utilisateur</option>
                </select>
            </div>

            <div class="field">
                <input type="submit" class="btn" name="submit" value="Ajouter" required>
            </div>
        </form>
        <?php } ?>
    </div>
</div>
</body>
</html>