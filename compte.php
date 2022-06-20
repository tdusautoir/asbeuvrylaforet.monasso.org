<?php
session_start();

require_once("./function.php");
require_once("./db.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}

?>
<!DOCTYPE html>
<html lang="fr">

<head> <?php require("./components/head.php"); ?>
    <title>Mon compte - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php include('./components/header.php'); ?>
        <div class="container">
            <div class="container-content">
                <?php include "./components/display_error.php"; ?>
                <div class="account-container">
                    <div class="welcome-admin">
                        <h1>Bonjour <?= htmlspecialchars($_SESSION['prenom']); ?>,
                            <p>Tu es sur l'espace de gestion de compte</p>
                        </h1>
                    </div>
                    <div class="welcome-separator"></div>
                    <div class="account-panel">
                        <div class="account-li">
                            <ul>
                                <li id="li-infos" onclick="displayContent('account-infos','account-droits','account-settings','li-infos','li-droits','li-settings')">Informations</li>
                                <li id="li-droits" onclick="displayContent('account-droits','account-infos','account-settings','li-droits','li-infos','li-settings')">Droits</li>
                                <li id="li-settings" onclick="displayContent('account-settings','account-droits','account-infos','li-settings','li-infos','li-droits')">Paramètres du site</li>
                            </ul>
                        </div>
                        <div class="account-li-content">
                            <div id="account-infos">
                                <h1>Mes informations : </h1>
                                <input type="text">
                            </div>
                            <div id="account-droits">
                                <h1>Mes droits : </h1>
                            </div>
                            <div id="account-settings">
                                <h1>Configurez le site : </h1>
                            </div>
                        </div>
                    </div>
                    <div class="return deconnect">
                        <a href="index.php">Retour</a>
                    </div>
                </div>
            </div>
            <?php else : require "./components/logged.php"; ?><?php endif; ?>
            <script>
                function displayContent(idActive, idOther1, idOther2, idList, idListOther1, idListOther2) {
                    document.getElementById(idActive).style.display = "flex";
                    document.getElementById(idList).style.background = "var(--mainColor)";
                    document.getElementById(idList).style.color = "white";
                    document.getElementById(idOther1).style.display = "none";
                    document.getElementById(idListOther1).style.background = "white";
                    document.getElementById(idListOther1).style.color = "black";
                    document.getElementById(idOther2).style.display = "none";
                    document.getElementById(idListOther2).style.background = "white";
                    document.getElementById(idListOther2).style.color = "black";
                }
            </script>
</body>

</html>