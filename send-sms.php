<?php
session_start();

require_once("./function.php");
require_once("./db.php");

if (isset($_GET["idLicencie"]) && !empty($_GET["idLicencie"]) && isInteger($_GET["idLicencie"])) {
    $idLicencie = $_GET["idLicencie"];
    $info = $db->prepare("SELECT licencie.nom, licencie.prenom, licencie.dateN, licencie.mail, licencie.sexe, categorie.nomCategorie FROM licencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.idLicencie = ? AND licencie.COSU = 0");
    $info->bindValue(1, $idLicencie);
    $info->execute();
    if ($info->rowCount() > 0) { //search and check if the licencie is in db and not deleted
        $getinfo = $info->fetch(PDO::FETCH_ASSOC);
        $firstname_licencie = $getinfo["prenom"];
        $lastname_licencie = $getinfo["nom"];
        $dateN_licencie = $getinfo["dateN"];
        $mail_licencie = $getinfo["mail"];
        $sexe_licencie = $getinfo["sexe"];
        $category_licencie = $getinfo["nomCategorie"];
    }
    else {  //licencie is not in db or is deleted
        header("location: ./licencies.php");
        create_flash_message("not_found", "Licencié introuvable.", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ./licencies.php");
    create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
    exit();
} ?>
?>
<!DOCTYPE html>
<html lang="fr">

<head> <?php require("./components/head.php"); ?>
    <title>Envoyer un SMS - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <div class="content">
            <?php include('./components/header.php'); ?>
            <div class="container">
                <div class="container-content">
                    <div class="sms-container">
                        <div class="sms-left-part">
                            <h2>
                                Envoyer un SMS à 
                            </h2>
                        </div>
                        <div class="sms-right-part">

                        </div>
                    </div>
                </div>
            </div>



        </div>
        <?php require './components/footer.php'; ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>