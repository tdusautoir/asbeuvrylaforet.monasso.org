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
                            <div class="form-add">
                                <input type="tel" placeholder="Téléphone" maxlength="10" onkeyup="javascript:nospaces(this)" onkeydown="javascript:nospaces(this)">
                                <select name="categorie-licencie" id="categorie-licencie" <?php if (isset($form_categorie_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                                    <option value="" disabled <?php if (!isset_info_form("categorie-licencie")) : ?> selected <?php endif; ?>>Catégorie</option>
                                    <?php
                                    if (is_admin()) :
                                        $req_category = $db->query("SELECT idCategorie, nomCategorie FROM categorie");
                                        while ($category = $req_category->fetch()) :
                                            if (isset($category)) :
                                    ?>
                                                <option value="<?= $category["idCategorie"] ?>" <?php if (isset_info_form("categorie-licencie")) :
                                                                                                    if ($_SESSION[FORM]['categorie-licencie'] == $category['nomCategorie']) : ?> selected <?php endif;
                                                                                                                                                                                    endif; ?>><?= $category["nomCategorie"] ?></option>
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
                                                    <option value="<?= $category["idCategorie"] ?>" <?php if (isset_info_form("categorie-licencie")) :
                                                                                                        if ($_SESSION[FORM]['categorie-licencie'] == $category['nomCategorie']) : ?> selected <?php endif;
                                                                                                                                                                                        endif; ?>><?= $category["nomCategorie"] ?></option>
                                    <?php
                                                endif;
                                            endwhile;
                                        endif;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="form-add msg-txtarea">
                                <textarea name="" id="" cols="300" rows="15" placeholder="Message ...">Bonjour,&#13;&#10;Cordialement, <?= htmlspecialchars($_SESSION['prenom']); ?> <?= htmlspecialchars($_SESSION['nom']); ?></textarea>
                            </div>
                            <div class="form-add msg-content-button">
                                <input type="submit" value="Envoyer">
                            </div>
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