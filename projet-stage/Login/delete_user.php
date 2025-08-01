<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid']) || $_SESSION['role'] !== 'admin') {
    die("Accès refusé.");
}

// Vérifie si l'admin a demandé la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = intval($_POST['delete_user_id']);
    $current_user_email = $_SESSION['email'];

    // Récupération des infos de l'utilisateur à supprimer
    $target_info_res = mysqli_query($con, "SELECT username, email FROM users WHERE id = $delete_user_id");
    $target_info = mysqli_fetch_assoc($target_info_res);

    if ($target_info) {
        $username = mysqli_real_escape_string($con, $target_info['username']);
        $email = mysqli_real_escape_string($con, $target_info['email']);
        $deleted_by = mysqli_real_escape_string($con, $current_user_email);

        // Archivage dans la table compte_sup
        $archive_sql = "INSERT INTO compte_sup (deleted_username, deleted_email, deleted_by, deleted_at)
                        VALUES ('$username', '$email', '$deleted_by', NOW())";
        mysqli_query($con, $archive_sql);
    }

    // Suppression de l'utilisateur
    mysqli_query($con, "DELETE FROM users WHERE id = $delete_user_id");

    header("Location: liste_utilisateurs.php?message=suppression_ok");
    exit();
}
?>
