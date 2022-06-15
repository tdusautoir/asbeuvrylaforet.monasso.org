<?php

session_start();

require_once("../function.php");
require_once("../db.php");


if (isset($_GET["idLicencie"]) && !empty($_GET["idLicencie"])) {
    $idLicencie = $_GET["idLicencie"];
    if (filter_var($idLicencie, FILTER_VALIDATE_INT)) {
        $req = $db->prepare("UPDATE licencie SET licencie.COSU = 1 WHERE `idLicencie` = ?;");
        $req->bindValue(1, $idLicencie, PDO::PARAM_INT);
        $result = $req->execute();

        if ($result) {
            create_flash_message("delete_success", "Le licencié a bien été supprimé.", FLASH_SUCCESS);
            header("location: ../licencies.php");
        } else {
            create_flash_message("delete_error", "Une erreur est survenue. Veuillez réessayer.", FLASH_ERROR);
            header("location: ../licencies.php");
        }
    }
}
