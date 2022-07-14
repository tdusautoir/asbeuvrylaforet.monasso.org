<?php
session_start();

require_once("./function.php");
require_once("./db.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}

//Recuper les infos du licenciés :

//verifier si l'id est passé dans l'url et verifier si c'est un entier
if (isset($_GET["idLicencie"]) && !empty($_GET["idLicencie"]) && isInteger($_GET["idLicencie"])) {

    // recupérer l'id
    $idLicencie = $_GET["idLicencie"];

    // recuperer les infos selon l'id
    $info = $db->prepare("SELECT licencie.nom, licencie.prenom, tel.tel FROM licencie INNER JOIN tel ON licencie.idLicencie = tel.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.idLicencie = ? AND licencie.COSU = 0");
    $info->bindValue(1, $idLicencie);
    $info->execute();
    if ($info->rowCount() > 0) { //verifier si la requete nous renvoies des infos, sinon : le licenciés est supprimé ou n'existe pas
        $getinfo = $info->fetch(PDO::FETCH_ASSOC);
        $tel_licencie = $getinfo["tel"];
        $firstname_licencie = $getinfo["prenom"];
    } else {
        header("location: ./message.php");
        create_flash_message("not_found", "Numéro associé à aucun licencié.", FLASH_ERROR);
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head> <?php require("./components/head.php"); ?>
    <title>Messagerie - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <div class="content">
            <?php include('./components/header.php'); ?>
            <div class="container">
                <div class="container-content">
                    <?php include "./components/display_error.php"; ?>
                    <div class="msg-container">
                        <div class="msg-container-head">
                            <h1>Envoyer un message</h1>
                        </div>
                        <div class="msg-content">
                            <form action="./functions/send-msg.php" method="POST">
                                <div class="form-add">
                                    <input name="msg-tel-licencie" type="text" placeholder="Téléphone" <?php if (isset($tel_licencie)) : echo "value='$tel_licencie'";
                                                                                                        endif; ?> maxlength="10" onkeyup="javascript:nospaces(this)" onkeydown="javascript:nospaces(this)">

                                    <?php if (!isset($tel_licencie)) : ?>
                                        <select name="msg-categorie" id="categorie-licencie">
                                            <option disabled selected>Catégorie</option>
                                            <?php
                                            if (is_admin()) :
                                                $req_category = $db->query("SELECT idCategorie, nomCategorie FROM categorie");
                                                while ($category = $req_category->fetch()) :
                                                    if (isset($category)) :
                                            ?>
                                                        <option value="<?= $category["idCategorie"] ?>"><?= $category["nomCategorie"] ?></option>
                                                        <?php
                                                    endif;
                                                endwhile;
                                                $req_category->closeCursor();
                                            elseif (is_educ()) :
                                                $req_category = $db->prepare(" SELECT categorie.idCategorie, categorie.nomCategorie FROM `categorieeduc` INNER JOIN categorie ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE educ.idEduc = :idEduc");
                                                $req_category->bindValue("idEduc", $_SESSION['id']);
                                                $req_category->execute();
                                                if ($req_category->rowCount() > 0) :
                                                    while ($category = $req_category->fetch()) :
                                                        if (isset($category)) :
                                                        ?>
                                                            <option value="<?= $category["idCategorie"] ?>"><?= $category["nomCategorie"] ?></option>
                                            <?php
                                                        endif;
                                                    endwhile;
                                                endif;
                                            endif;
                                            ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <div class="form-add msg-txtarea">
                                    <textarea name="msg-content" id="" cols="300" rows="15" placeholder="Message...">Bonjour<?php if (isset($tel_licencie)) : echo " " . $firstname_licencie;
                                                                                                                            endif; ?>,&#13;&#10;&#13;&#10;Cordialement, <?= htmlspecialchars($_SESSION['prenom']); ?> <?= htmlspecialchars($_SESSION['nom']); ?></textarea>
                                </div>
                                <div class="form-add msg-content-button">
                                    <?php if (isset($idLicencie)) : ?>
                                        <input type="hidden" value="<?= $idLicencie ?>" name="idLicencie">
                                    <?php endif; ?>
                                    <input type="submit" value="Envoyer">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require 'components/footer.php'; ?>
    <?php else : require "./components/form_login.php";
    endif; ?>
</body>

</html>