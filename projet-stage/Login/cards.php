<?php
session_start();
include("php/config.php"); // Définit $con (kynus DB)

// Connexion à la base indicateur (pour liste_techniciens)
$servername = "10.10.10.55";
$dbname = "indicateur";
$db_username = "cq_projet";
$db_password = "Z9#k*E)dl*o(0I";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed to indicateur DB: " . $conn->connect_error);
}

// Vérification session
if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id'];
$role = $_SESSION['role'];

// Statistiques pour admin (sur kynus DB)
if ($role === 'admin') {
    // Total utilisateurs
    $queryTotalUsers = mysqli_query($con, "SELECT COUNT(*) AS total_users FROM users");
    $totalUsers = mysqli_fetch_assoc($queryTotalUsers)['total_users'];

    // Comptes désactivés

    // Total techniciens
    $queryTechnicians = mysqli_query($conn, "SELECT COUNT(*) AS total_technicians FROM liste_techniciens");
    $totalTechnicians = mysqli_fetch_assoc($queryTechnicians)['total_technicians'];

    // Techniciens actifs (actif = TRUE)
// Techniciens actifs (actif = 'VRAI')
    $queryActiveTech = mysqli_query($conn, "SELECT COUNT(*) AS active_tech FROM liste_techniciens WHERE actif = 'VRAI'");
    $activeTechnicians = mysqli_fetch_assoc($queryActiveTech)['active_tech'];

    // Techniciens non actifs (actif = 'FAUX')
    $queryInactiveTech = mysqli_query($conn, "SELECT COUNT(*) AS inactive_tech FROM liste_techniciens WHERE actif = 'FAUX'");
    $inactiveTechnicians = mysqli_fetch_assoc($queryInactiveTech)['inactive_tech'];

} else {
    $totalUsers = $deactivatedAccounts = $totalTechnicians = $activeTechnicians = $inactiveTechnicians = 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Tableau de bord - Admin</title>
    <link rel="stylesheet" href="style/style.css" />
    <link rel="icon" type="image/png" href="image/logo.png">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #e4e9f7;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .nav {
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
            height: 70px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }
        .logo {
            display: flex;
            align-items: center;
        }
        .logo img {
            height: 70px;
            width: auto;
            display: block;
        }
        .right-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .right-links a {
            padding: 0 10px;
            text-decoration: none;
            font-weight: 500;
            color: #4a4a4a;
        }
        .btn {
            background: rgba(1, 137, 248, 0.808);
            border: 0;
            border-radius: 5px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
            transition: all .3s;
            padding: 6px 12px;
            font-weight: 600;
        }
        .btn:hover {
            opacity: 0.82;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 100px auto 40px; /* top margin for navbar + bottom */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            overflow-x: hidden; /* Remove horizontal scrollbar */
            padding: 0 20px;
        }
        .dashboard-cards {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: nowrap;
            width: 100%;
        }
        .card {
            background: #fdfdfd;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 0 80px 0 rgba(0,0,0,0.1),
                        0 20px 40px -30px rgba(0,0,0,0.5);
            width: 190px;
            min-width: 190px;
            max-width: 190px;
            text-align: center;
            cursor: default;
            transition: transform 0.2s ease;
            user-select: none;
        }
        .card:hover {
            transform: scale(1.03);
        }
        .card h3 {
            font-weight: 600;
            font-size: 20px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e6e6e6;
            margin-bottom: 8px;
            color: #333;
        }
        .card p {
            font-size: 26px;
            font-weight: 700;
            color: rgba(1, 137, 248, 0.9);
            margin: 0;
        }
        @media (max-width: 1150px) {
            .dashboard-container {
                max-width: 100%;
                padding: 0 10px;
            }
            .dashboard-cards {
                overflow-x: auto;
                flex-wrap: nowrap;
            }
            /* Optional: hide scrollbar on webkit browsers */
            .dashboard-cards::-webkit-scrollbar {
                display: none;
            }
        }
        @media (max-width: 850px) {
            .dashboard-cards {
                flex-direction: column;
                gap: 20px;
                align-items: center;
                overflow-x: visible;
            }
            .dashboard-container {
                min-height: auto;
                margin-top: 130px;
            }
        }
    </style>
</head>
<body>

    <div class="nav">
        <div class="logo">
            <a href="home.php"><img src="image/logo.png" alt="Logo"></a>
        </div>
        <div class="right-links">
            <a href="home.php">Accueil</a>
            <a href="php/logout.php"><button class="btn">Se déconnecter</button></a>
        </div>
    </div>

    <?php if ($role === 'admin') { ?>
        <div class="dashboard-container">
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Utilisateurs inscrits</h3>
                    <p><?= $totalUsers ?></p>
                </div>
                <div class="card">
                    <h3>Techniciens (Total)</h3>
                    <p><?= $totalTechnicians ?></p>
                </div>
                <div class="card">
                    <h3>Techniciens actifs</h3>
                    <p><?= $activeTechnicians ?></p>
                </div>
                <div class="card">
                    <h3>Techniciens non actifs</h3>
                    <p><?= $inactiveTechnicians ?></p>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div style="text-align:center; margin-top: 150px;">
            <p>Vous n'avez pas les droits pour voir cette page.</p>
        </div>
    <?php } ?>

</body>
</html>
