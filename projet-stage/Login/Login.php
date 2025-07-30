<!-- index.php -->
<?php
session_start();
include("php/config.php");

if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $result = mysqli_query($con, "SELECT * FROM users WHERE username='$username' AND password='$password'");

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['valid'] = $row['username'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['age'] = $row['age'] ?? '';
        $_SESSION['id'] = $row['id'];
        header("Location: ../index.php");
        exit();
    } else {
        // Optionnel : tu peux afficher une erreur ici si tu veux
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="icon" type="image/png" href="image/logo.png">
    <link rel="stylesheet" href="Login.css">
</head>
<body>
    <div class="container">
        <div class="box form-box">
            <header>Connexion</header>
            <form action="" method="post">
                <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
                <div class="field input">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="field input">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="field">
                    <input type="submit" name="submit" class="btn" value="Se connecter">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
