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
    <title>Modification d'éducateur - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php include('./components/header.php');
        if (isset($_GET["idEduc"]) && !empty($_GET["idEduc"]) && isInteger($_GET["idEduc"])) {
            $idEduc = $_GET["idEduc"];

            //get the educ info from idEduc
            $info = $db->prepare("SELECT educ.nom, educ.prenom, educ.mail, educ.responsable FROM educ WHERE educ.idEduc = ? AND educ.COSU = 0");
            $info->bindValue(1, $idEduc);
            $info->execute();
            if ($info->rowCount() > 0) { //search and check if the educ is in db and not deleted
                $getinfo = $info->fetch(PDO::FETCH_ASSOC);
                $firstname_educ = $getinfo["prenom"];
                $lastname_educ = $getinfo["nom"];
                $mail_educ = $getinfo["mail"];
                $resp_educ = $getinfo["responsable"];

                //get the educ categories from idEduc
                $info_categories = $db->prepare("SELECT categorieeduc.idCategorie FROM educ INNER JOIN categorieeduc ON categorieeduc.idEduc = educ.idEduc WHERE educ.idEduc = $idEduc");
                $info_categories->execute();
                if ($info_categories->rowCount() > 0) { //if the educ is associate with categorie(s), get the name of the categorie(s)
                    $getinfo_categories = $info_categories->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($getinfo_categories as $value) {
                        $info_categorie = $db->prepare("SELECT nomCategorie FROM categorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie WHERE categorie.idCategorie = ? ");
                        $info_categorie->bindValue(1, $value["idCategorie"]);
                        $info_categorie->execute();
                        $getinfo_categorie = $info_categorie->fetch(PDO::FETCH_ASSOC);
                        $categories_educ[] = $getinfo_categorie["nomCategorie"];
                    }
                }
            } else { //educ is not in db or is deleted
                header("location: ./educateurs.php");
                create_flash_message("not_found", "Éducateur introuvable.", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ./educateurs.php");
            create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
            exit();
        } ?>
        <div class="container">
            <div class="container-content">
                <?php include "./components/display_error.php"; ?>
                <div class="modif-li-container">
                    <div class="modif-li-panel">
                        <h1>
                            Modifier un Éducateur
                        </h1>
                        <form action="./functions/educ-modif.php" method="POST">
                            <div class="form-modif-li">
                                <input value="<?= $lastname_educ ?>" type="text" class="nom-licencie" placeholder="" name="nom-educ" maxlength="20">
                                <input value="<?= $firstname_educ ?>" type="text" class="prenom-licencie" placeholder="" name="prenom-educ" maxlength="15">
                            </div>
                            <div class="form-modif-li">
                                <input type="password" class="password-licencie" name="password-educ" placeholder="Ancien mot de passe" maxlength="40">
                                <label for="" style="display:flex; justify-content: space-between; align-items: center;" onclick="displayModal('cate-educ-div')">Catégories <i class="fa fa-angle-down"></i></label>
                            </div>
                            <div class="form-modif-li list-cate-div" id="cate-educ-div">
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
                                                <input type="checkbox" name="<?= $CAT["nomCategorie"] ?>-cb" <?php
                                                                                                                if (isset($categories_educ)) :
                                                                                                                    foreach ($categories_educ as $value) :
                                                                                                                        if ($value == $CAT["nomCategorie"]) : ?> checked <?php endif;
                                                                                                                                                                    endforeach;
                                                                                                                                                                endif;                                                                                                                                            ?>>
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
                            <div class="mail-form-modif-li">
                                <input value="<?= $mail_educ ?>" type="mail" class="mail-licencie" name="mail-educ" placeholder="" maxlength="40">
                            </div>
                            <div class="form-add form-modif-li list-cate-div">
                                <div class="responsable">
                                    <label for="check-resp">
                                        Responsable
                                    </label>
                                    <input id="check-resp" type="checkbox" style="margin:0;" <?php if ($resp_educ) : ?> checked <?php endif; ?>>
                                </div>
                            </div>
                            <input type="hidden" name="idEduc" value="<?php if (isset($idEduc)) {
                                                                            echo $idEduc;
                                                                        } ?>">
                            <div class="form-modif-li">
                                <input type="submit" value="Enregistrer" name="submit-modif" class="bouton-ajouter">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="return deconnect">
                    <a href="./educateurs.php">Annuler</a>
                </div>
            </div>
        </div>
        <script>
            function displayModal(idModal) {
                document.getElementById(idModal).style.display = "flex";
            }

            function erase(idModal) {
                document.getElementById(idModal).style.display = "none";
            }
        </script>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>