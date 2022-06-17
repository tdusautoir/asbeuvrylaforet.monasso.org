<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

//verification si l'utilisateur est connecté
if (is_logged()) {

    //recup info from formulaire to display it if there is an error
    if (isset($_POST["submit-add"])) {
        if (isset($_POST["nom-licencie"]) && !empty($_POST["nom-licencie"])) {
            add_info_form("nom-licencie", $_POST["nom-licencie"]);
        }
        if (isset($_POST["prenom-licencie"]) && !empty($_POST["prenom-licencie"])) {
            add_info_form("prenom-licencie", $_POST["prenom-licencie"]);
        }
        if (isset($_POST["dateN-licencie"]) && !empty($_POST["dateN-licencie"])) {
            add_info_form("dateN-licencie", $_POST["dateN-licencie"]);
        }
        if (isset($_POST["photo-licencie"]) && !empty($_POST["photo-licencie"])) {
            add_info_form("photo-licencie", $_POST["photo-licencie"]);
        }
        if (isset($_POST["categorie-licencie"]) && !empty($_POST["categorie-licencie"])) {
            add_info_form("categorie-licencie", $_POST["categorie-licencie"]);
        }
        if (isset($_POST["sexe-licencie"]) && !empty($_POST["sexe-licencie"])) {
            add_info_form("sexe-licencie", $_POST["sexe-licencie"]);
        }
        if (isset($_POST["mail-licencie"]) && !empty($_POST["mail-licencie"]) && filter_var($_POST["mail-licencie"], FILTER_VALIDATE_EMAIL)) {
            add_info_form("mail-licencie", $_POST["mail-licencie"]);
        }
    }

    //verification to add on database
    if (isset($_POST["submit-add"])) {
        if (isset($_POST["nom-licencie"]) && !empty($_POST["nom-licencie"])) {
            if (isset($_POST["prenom-licencie"]) && !empty($_POST["prenom-licencie"])) {
                if (isset($_POST["photo-licencie"]) && !empty($_POST["photo-licencie"])) {
                    if (isset($_POST["dateN-licencie"]) && !empty($_POST["dateN-licencie"])) {
                        if (isset($_POST["categorie-licencie"]) && !empty($_POST["categorie-licencie"])) {
                            if (isset($_POST["sexe-licencie"]) && !empty($_POST["sexe-licencie"])) {
                                if (isset($_POST["mail-licencie"]) && !empty($_POST["mail-licencie"]) && filter_var($_POST["mail-licencie"], FILTER_VALIDATE_EMAIL)) {
                                    $nom_licencie = strtoupper($_POST["nom-licencie"]);
                                    $prenom_licencie = ucfirst($_POST["prenom-licencie"]);
                                    $dateN_licencie = $_POST["dateN-licencie"];
                                    $mail_licencie = $_POST["mail-licencie"];
                                    $categorie_licencie = $_POST["categorie-licencie"];
                                    $sexe_licencie = $_POST["sexe-licencie"];
                                    $current_user = $_SESSION["prenom"] . " " . strtoupper($_SESSION["nom"]);
                                    $req = $db->prepare("INSERT INTO licencie (prenom, nom, sexe, dateN, mail, idCategorie, USRCRE) VALUES (?, ?, ?, ?, ?, ?, ?);");
                                    $req->bindValue(1, $prenom_licencie, PDO::PARAM_STR);
                                    $req->bindValue(2, $nom_licencie, PDO::PARAM_STR);
                                    $req->bindValue(3, $sexe_licencie, PDO::PARAM_STR);
                                    $req->bindValue(4, $dateN_licencie, PDO::PARAM_STR);
                                    $req->bindValue(5, $mail_licencie, PDO::PARAM_STR);
                                    $req->bindValue(6, $categorie_licencie, PDO::PARAM_INT);
                                    $req->bindValue(7, $current_user, PDO::PARAM_STR);
                                    $result = $req->execute();

                                    if ($result) {
                                        header("location: ../add-licencie.php");
                                        create_flash_message("add_success", "Licencié ajouté.", FLASH_SUCCESS);
                                        unset_info_form();
                                        exit();
                                    } else {
                                        header("location: ../add-licencie.php");
                                        create_flash_message("add_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                                        exit();
                                    }
                                } else {
                                    header("location: ../add-licencie.php");
                                    create_flash_message("form_mail_error", "Email invalide.", FLASH_ERROR);
                                    exit();
                                }
                            } else {
                                header("location: ../add-licencie.php");
                                create_flash_message("form_sexe_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                                exit();
                            }
                        } else {
                            header("location: ../add-licencie.php");
                            create_flash_message("form_categorie_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                            exit();
                        }
                    } else {
                        header("location: ../add-licencie.php");
                        create_flash_message("form_dateN_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../add-licencie.php");
                    create_flash_message("form_picture_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../add-licencie.php");
                create_flash_message("form_firstname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ../add-licencie.php");
            create_flash_message("form_lastname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../add-licencie.php");
        create_flash_message("add_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
