<?php 
session_start();
include("php/config.php");

// Check login
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id'];
$role = $_SESSION['role']; // Make sure role is saved in session at login

if(isset($_POST['submit'])){
    $password = $_POST['password'];

    if($role === 'admin'){
        // Admin can change everything
        $username = $_POST['username'];
        $email = $_POST['email'];
        $edit_query = mysqli_query($con, "UPDATE users SET username='$username', email='$email', password='$password' WHERE id=$id") 
                      or die("Erreur survenue");
    } else {
        // Manager or user can change only password
        $edit_query = mysqli_query($con, "UPDATE users SET password='$password' WHERE id=$id") 
                      or die("Erreur survenue");
    }

    if($edit_query){
        echo "<div class='message'><p>Profil mis à jour !</p></div><br>";
        echo "<a href='home.php'><button class='btn'>Retour à l'accueil</button></a>";
        exit();
    }
} else {
    // Fetch user info
    $query = mysqli_query($con,"SELECT * FROM users WHERE id=$id");
    $result = mysqli_fetch_assoc($query);
    $res_Uname = $result['username'];
    $res_email = $result['email'];
    $res_password = $result['password'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/png" href="image/logo.png">
    <link rel="stylesheet" href="style/style.css" />
    <title>Modifier le profil</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <a href="home.php">
            <img src="image/logo.png" alt="Logo" />
            </a>
        </div>
        <div class="right-links">
            <a href="home.php">Accueil</a>
            <a href="php/logout.php"><button class="btn">Se déconnecter</button></a>
        </div>
    </div>

    <div class="container">
        <div class="box form-box">
            <header>Modifier le profil</header>
            <form action="" method="post">

                <?php if($role === 'admin'): ?>
                    <!-- Admin can edit username and email -->
                    <div class="field input">
                        <label for="username">Nom d'utilisateur</label>
                        <input type="text" name="username" id="username" 
                               value="<?php echo htmlspecialchars($res_Uname); ?>" required />
                    </div>

                    <div class="field input">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" 
                               value="<?php echo htmlspecialchars($res_email); ?>" required />
                    </div>
                <?php else: ?>

                <?php endif; ?>

                <div class="field input">
                    <label for="password">Mot de passe</label>
                    <input type="text" name="password" id="password" 
                           value="<?php echo htmlspecialchars($res_password); ?>" required />
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Mettre à jour" />
                </div>
            </form>
        </div>
    </div>
</body>
</html>
