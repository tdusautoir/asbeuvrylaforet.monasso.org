<?php

session_start();

require_once("../function.php");
require_once("../db.php");


if (is_logged()) {
    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        if (isset($_GET["idEduc"]) && !empty($_GET["idEduc"])) {
            $idEduc = $_GET["idEduc"];
            if (filter_var($idEduc, FILTER_VALIDATE_INT)) {
                $info = $db->prepare("SELECT educ.nom, educ.prenom FROM educ WHERE educ.idEduc = $idEduc");
                $req = $db->prepare("BEGIN; UPDATE educ SET educ.COSU = 1 WHERE educ.idEduc = ?; COMMIT;");
                $req->bindValue(1, $idEduc, PDO::PARAM_INT);
                $info->execute();
                $result = $req->execute();
                $getinfo = $info->fetch(PDO::FETCH_ASSOC);
                $firstname_Educateur = $getinfo["prenom"];
                $lastname_Educateur = $getinfo["nom"];

                if ($result) {
                    create_flash_message("delete_success", "L'éducateur « $lastname_Educateur $firstname_Educateur » a bien été supprimé.", FLASH_SUCCESS);
                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                    exit();
                } else {
                    create_flash_message("delete_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                    exit();
                }
            } else {
                create_flash_message("delete_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
                header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                exit();
            }
        } else {
            create_flash_message("delete_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
            header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
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
