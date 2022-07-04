<?php
session_start();

require_once("./function.php");
require_once("./db.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}

$settings = $db->query("SELECT color, logoPath FROM settings ORDER BY id DESC LIMIT 1");
$get_settings = $settings->fetch(PDO::FETCH_ASSOC);

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
                                <li id="li-infos" onclick="displayContent('account-infos','account-settings','li-infos','li-settings')"><i class="fa fa-info"></i>
                                    <p>Informations</p>
                                </li>
                                <li id="li-settings" onclick="displayContent('account-settings','account-infos','li-settings','li-infos')"><i class="fa fa-cog"></i>
                                    <p>Paramètres du site</p>
                                </li>
                            </ul>
                        </div>
                        <div class="account-li-content">
                            <div id="account-infos">
                                <h1>Mes informations : </h1>
                                <?php if (is_admin()) : ?>
                                    <?php $account_info = $db->prepare("SELECT prenom, nom, mail, DCRE FROM admin WHERE idAdmin = ?;");
                                    $account_info->bindValue(1, $_SESSION["id"]);
                                    $account_info->execute();

                                    if ($account_info->rowCount() > 0) :
                                        $get_account_info = $account_info->fetch(PDO::FETCH_ASSOC); ?>

                                        <p>Nom : <?= $get_account_info["prenom"] ?></p>
                                        <p>Prenom : <?= $get_account_info["nom"] ?></p>
                                        <p>Mail : <?= $get_account_info["mail"] ?></p>
                                        <p>Date de création : <?= $get_account_info["DCRE"] ?></p>

                                    <?php endif; ?>
                                <?php elseif (is_educ()) : ?>
                                    <?php $account_info = $db->prepare("SELECT prenom, nom, mail, responsable, DCRE FROM educ WHERE idEduc = ?;");
                                    $account_info->bindValue(1, $_SESSION["id"]);
                                    $account_info->execute();

                                    if ($account_info->rowCount() > 0) :
                                        $get_account_info = $account_info->fetch(PDO::FETCH_ASSOC); ?>

                                        <p>Nom : <?= $get_account_info["prenom"] ?></p>
                                        <p>Prenom : <?= $get_account_info["nom"] ?></p>
                                        <p>Mail : <?= $get_account_info["mail"] ?></p>

                                        <?php $account_categorie = $db->prepare("SELECT nomCategorie FROM categorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE idEduc = ?; ");
                                        $account_categorie->bindValue(1, $_SESSION["id"]);
                                        $account_categorie->execute();

                                        if ($account_categorie->rowCount() > 0) : ?>
                                            <p> Categorie :
                                                <?php $get_account_categorie = $account_categorie->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($get_account_categorie as $categorie) :
                                                    if (end($get_account_categorie)["nomCategorie"] == $categorie["nomCategorie"]) :
                                                        echo $categorie["nomCategorie"];
                                                    else :
                                                        echo $categorie["nomCategorie"];
                                                        echo ", ";
                                                    endif;
                                                endforeach
                                                ?>
                                            </p>
                                        <?php else : ?>
                                            <p>Categorie : Aucune catégorie</p>
                                        <?php endif; ?>
                                        <p>Responsable : <?php if ($get_account_info["responsable"] == 1) {
                                                                echo "oui";
                                                            } else {
                                                                echo "non";
                                                            } ?></p>
                                        <p>Date de création : <?php echo $output = date('d-m-Y', strtotime($get_account_info["DCRE"])); ?></p>

                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <div id="account-settings">
                                <h1>Configurez le site : </h1>
                                <?php if ($settings) : ?>
                                    <p>Image : <img src="<?= $get_settings["logoPath"] ?>" class="img"></p>
                                    <p>Couleur : <span class="color"></span></p>

                                    <h2>Modification du site (en développement) :</h2>
                                    <form action="./functions/settings-modif.php" class="modif-settings" method="POST" enctype="multipart/form-data">
                                        <input type="color" name="color" value="<?= $get_settings['color'] ?>">
                                        <input type="file" accept="image/*" name="logo" value="<?= $get_settings['logoPath'] ?>">
                                        <input type="submit" name="submit-settings">
                                    </form>

                                    <form action="./functions/settings-cancel-modif.php" class="cancel-settings" method="POST">
                                        <input type="submit" name="cancel-settings" value="Annuler les modifications">
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="return deconnect">
                        <a href="index.php">Retour</a>
                    </div>
                </div>
            </div>
            <?php else : require "./components/form_login.php"; ?><?php endif; ?>
            <script>
                function displayContent(idActive, idOther1, idList, idListOther1) {
                    document.getElementById(idActive).style.display = "flex";
                    document.getElementById(idList).style.background = "var(--mainColor)";
                    document.getElementById(idList).style.color = "white";
                    document.getElementById(idOther1).style.display = "none";
                    document.getElementById(idListOther1).style.background = "white";
                    document.getElementById(idListOther1).style.color = "black";
                }
            </script>
</body>

</html>