<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

if (is_logged()) {
    if (isset($_POST["submit-modif"])) {
        if (isset($_POST["nom-educ"]) && !empty($_POST["nom-educ"])) {
            if (isset($_POST["prenom-educ"]) && !empty($_POST["prenom-educ"])) {
                if (isset($_POST["mail-educ"]) && !empty($_POST["mail-educ"]) && filter_var($_POST["mail-educ"], FILTER_VALIDATE_EMAIL)) {
                    if (isset($_POST["idEduc"]) && !empty($_POST["idEduc"])) {
                        $nom_educ = strtoupper(htmlspecialchars($_POST["nom-educ"]));
                        $prenom_educ = ucfirst(htmlspecialchars($_POST["prenom-educ"]));
                        $mail_educ = $_POST["mail-educ"];
                        $current_user = $_SESSION["prenom"] . " " . strtoupper($_SESSION["nom"]);
                        $current_date = date("Y-m-d H:i:s");
                        $idEduc = $_POST["idEduc"];


                        //search and check if the educ is in db and not deleted
                        $rech_educ = $db->prepare("SELECT idEduc FROM educ WHERE idEduc = ? AND COSU = 0");
                        $rech_educ->bindValue(1, $idEduc);
                        $rech_educ->execute();
                        if ($rech_educ->rowCount() > 0) {
                            $req = $db->prepare("UPDATE educ SET prenom = :prenom, nom = :nom, mail = :mail, responsable = :responsable, DMAJ = :DMAJ WHERE idEduc = :idEduc");
                            $req->bindValue("prenom", $prenom_educ, PDO::PARAM_STR);
                            $req->bindValue("nom", $nom_educ, PDO::PARAM_STR);
                            $req->bindValue("mail", $mail_educ, PDO::PARAM_STR);

                            //Resp?
                            if (isset($_POST["resp-educ"])) {
                                $req->bindValue("responsable", 1, PDO::PARAM_INT);
                            } else {
                                $req->bindValue("responsable", 0, PDO::PARAM_INT);
                            }

                            $req->bindValue("DMAJ", $current_date);
                            $req->bindValue("idEduc", $idEduc);
                            $result = $req->execute();

                            //change his password only if its not empty
                            if (isset($_POST["password-educ"]) && !empty($_POST["password-educ"])) {
                                $change_pswd = $db->prepare("UPDATE educ SET password = :password WHERE idEduc = :idEduc");
                                $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
                                $change_pswd->bindValue("password", $password_hash);
                                $change_pswd->bindValue("idEduc", $idEduc);
                                $change_pswd->execute();
                            }
                        } else { //educ is in db or is deleted
                            header("location: ../educateurs.php");
                            create_flash_message("not_found", "Éducateur introuvable.", FLASH_ERROR);
                            exit();
                        }

                        if ($result) {
                            header("location: ../educateurs.php");
                            create_flash_message("modif_success", "Licencié « $nom_educ $prenom_educ » a bien été modifié.", FLASH_SUCCESS);
                            exit();
                        } else {
                            header("location: ../educateurs.php");
                            create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                            exit();
                        }
                    } else {
                        header("location: ../educateurs.php");
                        create_flash_message("form_id_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../educateurs.php");
                    create_flash_message("form_mail_error", "Email invalide.", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../educateurs.php");
                create_flash_message("form_firstname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ../educateurs.php");
            create_flash_message("form_lastname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../educateurs.php");
        create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
