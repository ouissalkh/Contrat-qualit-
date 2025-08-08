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
    header("Location: login.php");
    exit();
}
$current_user_id = intval($_SESSION['id']);
$stmt = $con->prepare("SELECT role, email, username FROM users WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();
if (!$current_user || !in_array($current_user['role'], ['admin'])) {
    die("Accès refusé.");
}

$current_user_email = $current_user['email'];
$current_user_name = $current_user['username'];

$id = $_SESSION['id'];
$role = $_SESSION['role'];

if ($role === 'admin') {

    $queryTotalUsers = mysqli_query($con, "SELECT COUNT(*) AS total_users FROM users");
    $totalUsers = mysqli_fetch_assoc($queryTotalUsers)['total_users'];


    $queryTechnicians = mysqli_query($conn, "SELECT COUNT(*) AS total_technicians FROM liste_techniciens");
    $totalTechnicians = mysqli_fetch_assoc($queryTechnicians)['total_technicians'];

    $queryActiveTech = mysqli_query($conn, "SELECT COUNT(*) AS active_tech FROM liste_techniciens WHERE actif = 'VRAI'");
    $activeTechnicians = mysqli_fetch_assoc($queryActiveTech)['active_tech'];

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
            <a href="home.php"><img src="image/logo.png" alt="Logo"></a>
        </div>
 <div class="dropdown" x-data="{ open: false }" style="color:black;">
        <button @click="open = !open" class="profile-btn">
            <span><?= htmlspecialchars($current_user_name) ?></span>
            <img src="image/profile.jpg" alt="Profile">
            <i class="fas fa-caret-down"></i>
        </button>
        <div x-show="open" @click.outside="open = false" x-transition class="dropdown-menu">
            <a href="home.php">Profile</a>
            <div style="border-top:1px solid #ddd; margin:5px 0;"></div>
            <a href="php/logout.php" style="color: red;">Logout</a>
        </div>
    </div>
    </div>

        <?php if ($role === 'admin') { ?>
        <div class="dashboard-container">
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Utilisateurs inscrits</h3>
                    <p><?= htmlspecialchars($totalUsers) ?></p>
                </div>
                <div class="card">
                    <h3>Techniciens (Total)</h3>
                    <p><?= htmlspecialchars($totalTechnicians) ?></p>
                </div>
                <div class="card">
                    <h3>Techniciens actifs</h3>
                    <p><?= htmlspecialchars($activeTechnicians) ?></p>
                </div>
                <div class="card">
                    <h3>Techniciens non actifs</h3>
                    <p><?= htmlspecialchars($inactiveTechnicians) ?></p>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div style="text-align:center; margin-top: 150px;">
            <p>Vous n'avez pas les droits pour voir cette page.</p>
        </div>
    <?php } ?>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
