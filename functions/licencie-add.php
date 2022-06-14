<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

if (isset($_POST["submit"])) {
    $nom_licencie = $_POST["nom-licencie"];
    $prenom_licencie = $_POST["prenom-licencie"];
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
        create_flash_message("add_success", "Licencié ajouté", FLASH_SUCCESS);
    } else {
        header("location: ../add-licencie.php");
        create_flash_message("add_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
    }
}
