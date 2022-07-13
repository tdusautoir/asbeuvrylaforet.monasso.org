<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");


//verification si l'utilisateur est connecté
if (is_logged()) {
    //verification before change on database
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_GET['idCotis'])) { //verif if idCotis is define
            $idCotis = $_GET["idCotis"];
            if (filter_var($idCotis, FILTER_VALIDATE_INT)) { //verif if this is an int
                if (isset($_POST["etat"])) {
                    if ($_POST["etat"] == 1) {
                        $changeState = $db->query("UPDATE cotis SET cotis.etat = 1 WHERE idCotis = $idCotis");
                    } elseif ($_POST["etat"] == 2) {
                        $changeState = $db->query("UPDATE cotis SET cotis.etat = 2 WHERE idCotis = $idCotis");
                    } elseif ($_POST["etat"] == 3) {
                        $changeState = $db->query("UPDATE cotis SET cotis.etat = 3 WHERE idCotis = $idCotis");
                    } elseif ($_POST["etat"] == 4) {
                        $changeState = $db->query("UPDATE cotis SET cotis.etat = 4 WHERE idCotis = $idCotis");
                    } else {
                        create_flash_message("state_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
                        if (isset($_SERVER['HTTP_REFERER'])) {
                            header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                        } else {
                            header("location: ../cotisations.php");
                        }
                        exit();
                    }
                } elseif (isset($_POST["methode"])) {
                    if ($_POST["methode"] == 1) {
                        $changeMethod = $db->query("UPDATE cotis SET cotis.methode = 1 WHERE idCotis = $idCotis");
                    } elseif ($_POST["methode"] == 2) {
                        $changeMethod = $db->query("UPDATE cotis SET cotis.methode = 2 WHERE idCotis = $idCotis");
                    } elseif ($_POST["methode"] == 3) {
                        $changeMethod = $db->query("UPDATE cotis SET cotis.methode = 3 WHERE idCotis = $idCotis");
                    } else {
                        create_flash_message("state_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
                        if (isset($_SERVER['HTTP_REFERER'])) {
                            header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                        } else {
                            header("location: ../cotisations.php");
                        }
                        exit();
                    }
                } else {
                    create_flash_message("post_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
                    if (isset($_SERVER['HTTP_REFERER'])) {
                        header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                    } else {
                        header("location: ../cotisations.php");
                    }
                    exit();
                }
            } else {
                create_flash_message("id_not_int", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
                if (isset($_SERVER['HTTP_REFERER'])) {
                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                } else {
                    header("location: ../cotisations.php");
                }
                exit();
            }
        } else {
            create_flash_message("id_undefined", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
            if (isset($_SERVER['HTTP_REFERER'])) {
                header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
            } else {
                header("location: ../cotisations.php");
            }
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

if ($changeState) {
    create_flash_message("change_state_success", "État bien modifié.", FLASH_SUCCESS);
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
    } else {
        header("location: ../cotisations.php");
    }
    exit();
} elseif ($changeMethod) {
    create_flash_message("change_method_success", "Methode bien modifiée.", FLASH_SUCCESS);
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
    } else {
        header("location: ../cotisations.php");
    }
    exit();
} else {
    create_flash_message("change_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
    if (isset($_SERVER['HTTP_REFERER'])) {
        header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
    } else {
        header("location: ../cotisations.php");
    }
    exit();
}
