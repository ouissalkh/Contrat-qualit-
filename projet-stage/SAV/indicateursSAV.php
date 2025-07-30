<?php
require_once 'configSAV.php'; // inclut la connexion PDO $pdo

function tauxCR_OK($pdo, $mois, $annee, $semaine = 'toutes') {
    $conditions = [];
    $params = ['annee' => $annee];

    // Si une semaine valide est fournie, on filtre par semaine
    if (!empty($semaine) && $semaine !== 'toutes') {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
        $params['semaine'] = $semaine;
    } else {
        // Sinon, on filtre par mois
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params['mois'] = $mois;
    }

    // On filtre toujours par année
    $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";

    // Construction de la clause WHERE finale
    $whereClause = implode(" AND ", $conditions);

    // Requête SQL
    $query = "SELECT 
                SUM(CASE WHEN `Statut Intervention` = 'TERMINEE_OK' THEN 1 ELSE 0 END) AS total_OK,
                SUM(CASE WHEN `Statut Intervention` IN ('TERMINEE_OK', 'TERMINEE_KO') THEN 1 ELSE 0 END) AS total_all
              FROM `sav - taux de cr ok - 1er rdv`
              WHERE $whereClause";

    // Exécution
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_OK = $row['total_OK'];
    $total_all = $row['total_all'];

    // Calcul du taux
    return ($total_all > 0) ? round(($total_OK / $total_all) * 100, 2) : 0;
}


function DelaiPriseRDVSAV($pdo, $mois = null, $annee = null, $semaine = null) {
    $conditions = [];
    $params = [];

    // Filtrage dynamique selon les paramètres sélectionnés
    if (!empty($mois)) {
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params[':mois'] = $mois;
    }

    if (!empty($annee)) {
        $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
        $params[':annee'] = $annee;
    }

    if (!empty($semaine) && $semaine !== "toutes") {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine"; // WEEK(..., 1) = ISO-8601 (lundi comme début de semaine)
        $params[':semaine'] = $semaine;
    }

    // Construction de la requête avec conditions dynamiques
    $sql = "SELECT 
                SUM(CASE WHEN `[CONTRAT_QUALITE_2024]Taux_ds_délais` = '1' THEN 1 ELSE 0 END) AS nb_1,
                SUM(CASE WHEN `[CONTRAT_QUALITE_2024]Taux_ds_délais` = '0' THEN 1 ELSE 0 END) AS nb_0
            FROM `sav - taux de cr ok - 1er rdv`";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nb_1 = $result['nb_1'] ?? 0;
    $nb_0 = $result['nb_0'] ?? 0;
    $total = $nb_1 + $nb_0;

    if ($total > 0) {
        $pourcentage = ($nb_1 / $total) * 100;
        return number_format($pourcentage, 2, ',', ' ') . ' %';
    } else {
        return "Error";
    }
}


function SecuRDVSAV($pdo, $mois = null, $annee = null, $semaine = null) {
    $conditions = [];
    $params = [];

    // Filtrage dynamique selon les paramètres sélectionnés
    if (!empty($mois)) {
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params[':mois'] = $mois;
    }

    if (!empty($annee)) {
        $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
        $params[':annee'] = $annee;
    }

    if (!empty($semaine) && $semaine !== "toutes") {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
        $params[':semaine'] = $semaine;
    }

    // Requête SQL pour compter les 1 et 0 (ignorer les NULL / vides)
    $sql = "SELECT 
                SUM(CASE WHEN `flag_secu_interv_cq2024` = '1' THEN 1 ELSE 0 END) AS nb_1,
                SUM(CASE WHEN `flag_secu_interv_cq2024` = '0' THEN 1 ELSE 0 END) AS nb_0
            FROM `sav - taux de cr ok - 1er rdv`";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nb_1 = $result['nb_1'] ?? 0;
    $nb_0 = $result['nb_0'] ?? 0;
    $total = $nb_1 + $nb_0;

    if ($total > 0) {
        $pourcentage = ($nb_1 / $total) * 100;
        return number_format($pourcentage, 2, ',', ' ') . ' %';
    } else {
        return "Données insuffisantes";
    }
}



function conformiteCRSAV($pdo, $mois = null, $annee = null, $semaine = null) {
    $conditions = [];
    $params = [];

    // Filtrage dynamique selon les paramètres sélectionnés
    if (!empty($mois)) {
        $conditions[] = "MONTH(`Date Debut Rdv Client`) = :mois";
        $params[':mois'] = $mois;
    }

    if (!empty($annee)) {
        $conditions[] = "YEAR(`Date Debut Rdv Client`) = :annee";
        $params[':annee'] = $annee;
    }

    if (!empty($semaine) && $semaine !== "toutes") {
        $conditions[] = "WEEK(`Date Debut Rdv Client`, 1) = :semaine";
        $params[':semaine'] = $semaine;
    }

    // Requête SQL pour compter les 1 et 0 (ignorer les NULL / vides)
    $sql = "SELECT 
                SUM(CASE WHEN `volume Cr non exploitable` = '1' THEN 1 ELSE 0 END) AS nb_1,
                SUM(CASE WHEN `volume Cr non exploitable` = '0' THEN 1 ELSE 0 END) AS nb_0
            FROM `sav - taux de cr ok - 1er rdv`";

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $nb_1 = $result['nb_1'] ?? 0;
    $nb_0 = $result['nb_0'] ?? 0;
    $total = $nb_1 + $nb_0;

    if ($total > 0) {
        $pourcentage = ($nb_1 / $total) * 100;
        return number_format($pourcentage, 2, ',', ' ') . ' %';
    } else {
        return "Données insuffisantes";
    }
}

?>