<?php

session_start();

require_once("../db.php");
require_once("../function.php");

if (isset($_POST)) {
    if ($_POST['id'] == $_SESSION['id']) { //if not, you're trying to modify other account
        if (is_admin()) {
            if (isset($_POST["prenom"]) && !empty($_POST["prenom"])) {
                $req = $db->prepare("UPDATE admin SET prenom = :prenom WHERE idAdmin = :idAdmin");
                $req->bindValue(':idAdmin', $_SESSIONT['id']);
                $req->bindValue(':prenom', $_POST['prenom']);
                $req->execute();
                if ($req) {
                    header("location: ../compte.php");
                    create_flash_message("req-success", "La modification de votre prénom a bien été effectué.", FLASH_SUCCESS);
                    exit();
                } else {
                    header("location: ../compte.php");
                    create_flash_message("req-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                    exit();
                }
            } elseif (isset($_POST["nom"]) && !empty($_POST["nom"])) {
                $req = $db->prepare("UPDATE admin SET nom = :nom WHERE idAdmin = :idAdmin");
                $req->bindValue(':idAdmin', $_SESSIONT['id']);
                $req->bindValue(':nom', $_POST['nom']);
                $req->execute();
                if ($req) {
                    header("location: ../compte.php");
                    create_flash_message("req-success", "La modification de votre nom a bien été effectué.", FLASH_SUCCESS);
                    exit();
                } else {
                    header("location: ../compte.php");
                    create_flash_message("req-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                    exit();
                }
            } elseif (isset($_POST["mail"]) && !empty($_POST["mail"])) {
                if (filter_var($_POST["mail"], FILTER_VALIDATE_EMAIL)) {
                    $req = $db->prepare("UPDATE admin SET mail = :mail WHERE idAdmin = :idAdmin");
                    $req->bindValue(':idAdmin', $_SESSIONT['id']);
                    $req->bindValue(':mail', $_POST['mail']);
                    $req->execute();
                    if ($req) {
                        header("location: ../compte.php");
                        create_flash_message("req-success", "La modification de votre mail a bien été effectué.", FLASH_SUCCESS);
                        exit();
                    } else {
                        header("location: ../compte.php");
                        create_flash_message("req-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../compte.php");
                    create_flash_message("form_mail_error", "Email invalide.", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../compte.php");
                create_flash_message("req-error", "Veuillez remplir votre champ", FLASH_ERROR);
                exit();
            }
        } elseif (is_educ()) {
            if (isset($_POST["prenom"]) && !empty($_POST["prenom"])) {
                $req = $db->prepare("UPDATE educ SET prenom = :prenom WHERE idEduc = :idEduc");
                $req->bindValue(':idEduc', $_SESSIONT['id']);
                $req->bindValue(':prenom', $_POST['prenom']);
                $req->execute();
                if ($req) {
                    header("location: ../compte.php");
                    create_flash_message("req-success", "La modification de votre prénom a bien été effectué.", FLASH_SUCCESS);
                    exit();
                } else {
                    header("location: ../compte.php");
                    create_flash_message("req-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                    exit();
                }
            } elseif (isset($_POST["nom"]) && !empty($_POST["nom"])) {
                $req = $db->prepare("UPDATE educ SET nom = :nom WHERE idEduc = :idEduc");
                $req->bindValue(':idEduc', $_SESSIONT['id']);
                $req->bindValue(':nom', $_POST['nom']);
                $req->execute();
                if ($req) {
                    header("location: ../compte.php");
                    create_flash_message("req-success", "La modification de votre nom a bien été effectué.", FLASH_SUCCESS);
                    exit();
                } else {
                    header("location: ../compte.php");
                    create_flash_message("req-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                    exit();
                }
            } elseif (isset($_POST["mail"]) && !empty($_POST["mail"])) {
                if (filter_var($_POST["mail"], FILTER_VALIDATE_EMAIL)) {
                    $req = $db->prepare("UPDATE educ SET mail = :mail WHERE idEduc = :idEduc");
                    $req->bindValue(':idEduc', $_SESSIONT['id']);
                    $req->bindValue(':mail', $_POST['mail']);
                    $req->execute();
                    if ($req) {
                        header("location: ../compte.php");
                        create_flash_message("req-success", "La modification de votre mail a bien été effectué.", FLASH_SUCCESS);
                        exit();
                    } else {
                        header("location: ../compte.php");
                        create_flash_message("req-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../compte.php");
                    create_flash_message("form_mail_error", "Email invalide.", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../compte.php");
                create_flash_message("req-error", "Veuillez remplir votre champ", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ../compte.php");
            create_flash_message("no_rights", "Vous n'avez pas les droits.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../compte.php");
        create_flash_message("no_rights", "Attention, vous essayez de modifier d'autres comptes.", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ../compte.php");
    create_flash_message("post_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
    exit();
}
