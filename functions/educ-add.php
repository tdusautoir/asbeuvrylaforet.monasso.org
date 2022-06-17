<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

if (is_logged()) {
    if (isset($_POST["submit-add"])) {
        if (isset($_POST["nom-educ"]) && !empty($_POST["nom-educ"])) {
            if (isset($_POST["prenom-educ"]) && !empty($_POST["prenom-educ"])) {
                if (isset($_POST["password-educ"]) && !empty($_POST["password-educ"])) {
                    if (isset($_POST["mail-educ"]) && !empty($_POST["mail-educ"]) && filter_var($_POST["mail-educ"], FILTER_VALIDATE_EMAIL)) {
                        $nom_educ = strtoupper($_POST["nom-educ"]);
                        $prenom_educ = ucfirst($_POST["prenom-educ"]);
                        $mail_educ = $_POST["mail-educ"];
                        $password_educ = $_POST["password-educ"];
                        $password_educ_hash  = password_hash($password_educ, PASSWORD_DEFAULT);
                        $current_user = $_SESSION["prenom"] . " " . strtoupper($_SESSION["nom"]);
                        $req = $db->prepare("INSERT INTO educ (prenom, nom, mail, password, USRCRE) VALUES (?, ?, ?, ?, ?);");
                        $req->bindValue(1, $prenom_educ, PDO::PARAM_STR);
                        $req->bindValue(2, $nom_educ, PDO::PARAM_STR);
                        $req->bindValue(3, $mail_educ, PDO::PARAM_STR);
                        $req->bindValue(4, $password_educ_hash, PDO::PARAM_STR);
                        $req->bindValue(5, $current_user, PDO::PARAM_STR);
                        $result = $req->execute();

                        if ($result) {
                            header("location: ../add-educ.php");
                            create_flash_message("add_success", "Educateur ajouté", FLASH_SUCCESS);
                            exit();
                        } else {
                            header("location: ../add-educ.php");
                            create_flash_message("add_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                            exit();
                        }
                    } else {
                        header("location: ../add-educ.php");
                        create_flash_message("form_mail_error", "Email invalide.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../add-educ.php");
                    create_flash_message("form_password_error", "Veuillez remplir tous les champs", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../add-educ.php");
                create_flash_message("form_firstname_error", "Veuillez remplir tous les champs", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ../add-educ.php");
            create_flash_message("form_lastname_error", "Veuillez remplir tous les champs", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../add-educ.php");
        create_flash_message("add_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
