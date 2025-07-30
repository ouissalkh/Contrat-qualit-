
<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_SESSION['id']);
$message = "";

if (isset($_GET['action']) && $_GET['action'] === 'delete_self') {
    mysqli_query($con, "DELETE FROM users WHERE id = $id");
    session_destroy();
    $message = "Votre compte a été supprimé avec succès.";
}

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
}

if ($message !== "Votre compte a été supprimé avec succès.") {
    $query = mysqli_query($con, "SELECT * FROM users WHERE id = $id");

    if ($result = mysqli_fetch_assoc($query)) {
        $res_Uname = $result['username'];
        $res_email = $result['email'];
        $res_password = $result['password'];
        $res_id = $result['id'];
        $res_role = $result['role'];
    } else {
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="image/logo.png">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    .message-box {
        margin: 20px auto;
        text-align: center;
        background: #f9eded;
        padding: 15px 0;
        border: 1px solid #699053;
        border-radius: 5px;
        color: red;
        width: 50%;
    }
    .btn-danger {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-danger:hover {
        background-color: #c82333;
    }
    .btn-success {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-success:hover {
        background-color: #218838;
    }
    form {
        display: inline;
    }
    form + form {
        margin-left: 10px;
    }
    .box {
        margin-bottom: 10px;
    }

    /* ✅ Responsive adjustments */
    @media screen and (max-width: 600px) {
        .message-box {
            width: 90%;
            font-size: 0.95rem;
        }

        .box p {
            font-size: 0.95rem;
        }

        .btn,
        .btn-success,
        .btn-danger {
            width: 100%;
            margin-top: 10px;
        }

        form + form {
            margin-left: 0;
            margin-top: 10px;
        }

        .nav {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }

        .right-links {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            margin-top: 0;
        }

        .logo {
            flex: 1;
        }

        .main-box {
            padding: 0 10px;
        }
    }
    </style>
</head>

<body>

<?php if ($message === "Votre compte a été supprimé avec succès.") : ?>
    <div class="message-box" style="margin-top: 50px;">
        <?php echo htmlspecialchars($message); ?><br>
        <a href="index.php" class="btn btn-success" style="margin-top: 15px; display: inline-block;">Retour à la page de connexion</a>
    </div>
<?php else: ?>

<div class="nav" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
    <div class="logo">
        <img src="image/logo.png" alt="Logo" style="max-height: 60px;">
    </div>
    <div class="right-links" style="display: flex; gap: 10px;">
        <a href="edit.php?Id=<?php echo $res_id; ?>">Changer profil</a>
        <a href="php/logout.php"><button class="btn">Se déconnecter</button></a>
    </div>
</div>

<main>
<div class="main-box top">

    <?php if (!empty($message)): ?>
        <div class="message-box"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="top">
        <div class="box">
            <p>Bonjour, vous êtes <b><?php echo htmlspecialchars($res_Uname); ?></b></p>
        </div>
        <div class="box">
            <p>Votre email est <b><?php echo htmlspecialchars($res_email); ?></b></p>
        </div>
        <div class="box">
            <p>
                Votre mot de passe est 
                <b>
                <span id="password">*******</span>
                <i id="togglePassword" class="fa-solid fa-eye" style="float: right; cursor: pointer;"></i>
                </b>
            </p>
        </div>
        <div class="box">
            <p>Votre rôle est <b><?php echo htmlspecialchars($res_role); ?></b></p>
        </div>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <?php if ($res_role === 'admin' || $res_role === 'semi-admin') : ?>
            <form action="delete.php" method="get" style="display:inline;">
                <button type="submit" class="btn btn-success">Gérer les utilisateurs</button>
            </form>
            <form action="technician.php" method="get" style="display:inline;">
                <button type="submit" class="btn btn-success">Chercher les techniciens</button>
            </form>
        <?php else: ?>
            <form action="home.php" method="get" style="display:inline;">
                <input type="hidden" name="action" value="delete_self">
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
                    Supprimer mon compte
                </button>
            </form>
        <?php endif; ?>
    </div>

</div>
</main>

<script>
let isPasswordVisible = false;
const password = "<?php echo addslashes($res_password); ?>";
const display = document.getElementById("password");
const icon = document.getElementById("togglePassword");

icon.addEventListener("click", function () {
    if (isPasswordVisible) {
        display.textContent = "*******";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        display.textContent = password;
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
    isPasswordVisible = !isPasswordVisible;
});
</script>

<?php endif; ?>
</body>
</html>
