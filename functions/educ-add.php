<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

if (is_logged()) {
    if (isset($_POST["submit"])) {
        if (isset($_POST["nom-educ"]) && !empty($_POST["nom-educ"])) {
            if (isset($_POST["prenom-educ"]) && !empty($_POST["prenom-educ"])) {
                if (isset($_POST["dateN-educ"]) && !empty($_POST["dateN-educ"])) {
                    if (isset($_POST["categorie-educ"]) && !empty($_POST["categorie-educ"])) {
                        if (isset($_POST["sexe-educ"]) && !empty($_POST["sexe-educ"])) {
                            if (isset($_POST["mail-educ"]) && !empty($_POST["mail-educ"]) && filter_var($_POST["mail-educ"], FILTER_VALIDATE_EMAIL)) {
                                $nom_educ = $_POST["nom-educ"];
                                $prenom_educ = $_POST["prenom-educ"];
                                $dateN_educ = $_POST["dateN-educ"];
                                $mail_educ = $_POST["mail-educ"];
                                $categorie_educ = $_POST["categorie-educ"];
                                $sexe_educ = $_POST["sexe-educ"];
                                $current_user = $_SESSION["prenom"] . " " . strtoupper($_SESSION["nom"]);
                                $req = $db->prepare("INSERT INTO educ (prenom, nom, mail, password, dateN,  idCategorie, USRCRE) VALUES (?, ?, ?, ?, ?, ?, ?);");
                                $req->bindValue(1, $prenom_educ, PDO::PARAM_STR);
                                $req->bindValue(2, $nom_educ, PDO::PARAM_STR);
                                $req->bindValue(3, $sexe_educ, PDO::PARAM_STR);
                                $req->bindValue(4, $dateN_educ, PDO::PARAM_STR);
                                $req->bindValue(5, $mail_educ, PDO::PARAM_STR);
                                $req->bindValue(6, $categorie_educ, PDO::PARAM_INT);
                                $req->bindValue(7, $current_user, PDO::PARAM_STR);
                                $result = $req->execute();

                                if ($result) {
                                    header("location: ../add-licencie.php");
                                    create_flash_message("add_success", "Licencié ajouté", FLASH_SUCCESS);
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
                            create_flash_message("form_sexe_error", "Veuillez remplir tous les champs", FLASH_ERROR);
                            exit();
                        }
                    } else {
                        header("location: ../add-licencie.php");
                        create_flash_message("form_categorie_error", "Veuillez remplir tous les champs", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../add-licencie.php");
                    create_flash_message("form_dateN_error", "Veuillez remplir tous les champs", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../add-licencie.php");
                create_flash_message("form_firstname_error", "Veuillez remplir tous les champs", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ../add-licencie.php");
            create_flash_message("form_lastname_error", "Veuillez remplir tous les champs", FLASH_ERROR);
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
