<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");


//verification si l'utilisateur est connecté
if (is_logged()) {
    //verification before add on database
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_GET["idStat"]) && !empty($_GET["idStat"])) {
            $idStat = $_GET["idStat"];
            if (filter_var($idStat, FILTER_VALIDATE_INT)) {
                //get User info form idStat
                $getInfo = $db->query("SELECT licencie.prenom FROM licencie INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie WHERE statistiques.idStat = $idStat");
                $info = $getInfo->fetch(PDO::FETCH_ASSOC);
                if (isset($_POST['add-goal'])) {
                    // add goal;
                    $req = $db->query("UPDATE statistiques SET nbButs = nbButs + 1 WHERE idStat = $idStat");
                } elseif (isset($_POST['remove-goal'])) {
                    // remove goal;
                    $req = $db->query("UPDATE statistiques SET nbButs = nbButs - 1 WHERE idStat = $idStat");
                } elseif (isset($_POST['add-pd'])) {
                    // add pd;
                    $req = $db->query("UPDATE statistiques SET passeD = passeD + 1 WHERE idStat = $idStat");
                } elseif (isset($_POST['remove-pd'])) {
                    // remove pd;
                    $req = $db->query("UPDATE statistiques SET passeD = passeD - 1 WHERE idStat = $idStat");
                } else {
                    header("location: ../statistiques.php");
                    create_flash_message("submit-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                    exit();
                }
            } else {
                create_flash_message("delete_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
                header("location: ../statistiques.php");
                exit();
            }
        } else {
            create_flash_message("delete_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
            header("location: ../statistiques.php");
            exit();
        }
    } else {
        header("location: ../index.php");
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}

if ($req) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
    } else {
        header("location: ../statistiques.php");
    }
    create_flash_message("change-success", "Vous venez d'ajouter un but à " . $info['prenom'], FLASH_SUCCESS);
    exit();
}
