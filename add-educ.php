<?php
session_start();

require("./function.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}

//gestion des erreurs
if (isset_flash_message_by_type(FLASH_ERROR)) {
    if (isset_flash_message_by_name("form_lastname_error")) {
        $form_lastname_error = true;
    } else if (isset_flash_message_by_name("form_firstname_error")) {
        $form_firstname_error = true;
    } else if (isset_flash_message_by_name("form_password_error")) {
        $form_password_error = true;
    } else if (isset_flash_message_by_name("form_mail_error")) {
        $form_mail_error = true;
    }
}


?>
<!DOCTYPE html>
<html lang="fr">

<head> <?php require("./components/head.php"); ?>
    <title>Ajout d'éducateurs - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php if (is_admin()) : ?>
            <div class="content">
                <?php include('./components/header.php'); ?>
                <div class="container">
                    <div class="container-content">
                        <?php include "./components/display_error.php"; ?>
                        <div class="add-container">
                            <div class="add-panel" id="fade-in">
                                <h1>
                                    Ajouter un éducateur
                                </h1>
                                <form action="./functions/educ-add.php" method="POST">
                                    <div class="form-add">
                                        <input value="<?php display_info_form("nom-educ"); ?>" type="text" class="nom-licencie" placeholder="Nom" name="nom-educ" maxlength="20" onkeyup="javascript:nospaces(this)" onkeydown="javascript:nospaces(this)" <?php if (isset($form_lastname_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                                        <input value="<?php display_info_form("prenom-educ"); ?>" type="text" class="prenom-licencie" placeholder="Prénom" name="prenom-educ" maxlength="15" onkeyup="javascript:nospaces(this)" onkeydown="javascript:nospaces(this)" <?php if (isset($form_firstname_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                                    </div>
                                    <div class="form-add">
                                        <input value="<?php display_info_form("password-educ"); ?>" type="password" class="password-licencie" name="password-educ" placeholder="Mot de passe" maxlength="40" <?php if (isset($form_password_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                                        <label for="" style="display:flex; justify-content: space-between; align-items: center;" onclick="displayModal('cate-educ-div')">Catégories <i class="fa fa-angle-down"></i></label>
                                    </div>
                                    <div class="form-add list-cate-div" id="cate-educ-div">
                                        <div class="spacer-form"></div>
                                        <div class="cate-lign" id="cate-educ">
                                            <?php
                                            $req = $db->prepare("CALL PRC_LSTCAT"); //Liste des catégories
                                            $req->execute();
                                            $rowCount = $req->rowCount();
                                            if ($rowCount > 0) :
                                                $rows = $req->fetchAll(PDO::FETCH_ASSOC);
                                                $req->closeCursor();
                                                foreach ($rows as $CAT) : ?>
                                                    <div class="cate-check">
                                                        <p style="cursor: default; border: none;"><?= $CAT["nomCategorie"] ?></p>
                                                        <input type="checkbox" name="<?= $CAT["nomCategorie"] ?>-cb" <?php if (isset_info_form($CAT["nomCategorie"] . "-cb")) : ?> checked <?php endif; ?>>
                                                    </div>
                                                <?php
                                                endforeach;
                                            else :
                                                ?>
                                                <span>Aucune catégorie disponible</span>
                                            <?php
                                            endif;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="mail-form-add">
                                        <input value="<?php display_info_form("mail-educ"); ?>" type="email" class="mail-licencie" name="mail-educ" placeholder="Adresse mail" maxlength="40" <?php if (isset($form_mail_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                                    </div>
                                    <div class="form-add list-cate-div">
                                        <div class="responsable">
                                            <label for="check-resp">
                                                Responsable
                                            </label>
                                            <input id="check-resp" type="checkbox" style="margin:0;">
                                        </div>
                                    </div>
                                    <div class="loading" id='loading'>
                                        <img src="./public/images/Rolling-1s-200px-gray.svg">
                                    </div>
                                    <div class="form-add">
                                        <input type="submit" value="Ajouter" name="submit-add" class="bouton-ajouter" id="form-submit" onclick="loading()">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- <div class="return deconnect">
                        <a href="index.php">Retour</a>
                    </div> -->
                    </div>
                </div>
            </div>
            <?php
            //si des infos de formulaire sont présents dans la session, les supprimer
            unset_info_form();
            ?>
            <script>
                function displayModal(idModal) {
                    document.getElementById(idModal).style.display = "flex";
                }

                function erase(idModal) {
                    document.getElementById(idModal).style.display = "none";
                }
            </script>
            <script type="text/javascript">
                function nospaces(input) {
                    input.value = input.value.replace(" ", "");
                    return true;
                }
            </script>
            <?php require './components/footer.php'; ?>
        <?php else :
            create_flash_message("no_rights", "Vous ne possédez pas les droits.", FLASH_ERROR);
            header("location: ./index.php");
            exit();
        endif;
        ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>