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
                if (isset($_POST['add-goal'])) {
                    // add goal;
                    $req = $db->query("UPDATE statistiques SET nbButs = nbButs + 1 WHERE idStat = $idStat");
                    if ($req) {
                        header("location: ../statistiques.php");
                        create_flash_message("change-success", "Vous venez d'ajouter un but.", FLASH_SUCCESS);
                        exit();
                    }
                } elseif (isset($_POST['remove-goal'])) {
                    // remove goal;
                    $req = $db->query("UPDATE statistiques SET nbButs = nbButs - 1 WHERE idStat = $idStat");
                    if ($req) {
                        header("location: ../statistiques.php");
                        create_flash_message("change-success", "Vous venez de retirer un but.", FLASH_SUCCESS);
                        exit();
                    }
                } elseif (isset($_POST['add-pd'])) {
                    // add pd;
                    $req = $db->query("UPDATE statistiques SET passeD = passeD _ 1 WHERE idStat = $idStat");
                    if ($req) {
                        header("location: ../statistiques.php");
                        create_flash_message("change-success", "Vous venez d'ajouter une passe décisives.", FLASH_SUCCESS);
                        exit();
                    }
                } elseif (isset($_POST['remove-pd'])) {
                    // remove pd;
                    $req = $db->query("UPDATE statistiques SET passeD = passeD - 1 WHERE idStat = $idStat");
                    if ($req) {
                        header("location: ../statistiques.php");
                        create_flash_message("change-success", "Vous venez de retirer une passe décisives.", FLASH_SUCCESS);
                        exit();
                    }
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
    }
} else {
    header("location: ../index.php");
    exit();
}
