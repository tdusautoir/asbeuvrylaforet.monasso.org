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
    <title>Espace Modification Licencié - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php include('./components/header.php');
        if (isset($_GET["idLicencie"]) && !empty($_GET["idLicencie"])) {
            $idLicencie = $_GET["idLicencie"];
            $info = $db->prepare("SELECT licencie.nom, licencie.prenom, licencie.dateN, licencie.mail, licencie.sexe, categorie.nomCategorie FROM licencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.idLicencie = $idLicencie");
            $info->execute();
            $getinfo = $info->fetch(PDO::FETCH_ASSOC);
            $firstname_licencie = $getinfo["prenom"];
            $lastname_licencie = $getinfo["nom"];
            $dateN_licencie = $getinfo["dateN"];
            $mail_licencie = $getinfo["mail"];
            $sexe_licencie = $getinfo["sexe"];
            $category_licencie = $getinfo["nomCategorie"];
        } else {
            header("location: licencies.php");
            exit();
        } ?>
        <div class="container">
            <div class="container-content">
                <?php include "./components/display_error.php"; ?>
                <div class="modif-li-container">
                    <div class="modif-li-panel">
                        <h1>
                            Modifier un licencié
                        </h1>
                        <form action="./functions/licencie-modif.php" method="POST">
                            <div class="form-modif-li">
                                <input value="<?= $lastname_licencie ?>" type="text" class="nom-licencie" placeholder="" name="nom-licencie" maxlength="20">
                                <input value="<?= $firstname_licencie ?>" type="text" class="prenom-licencie" placeholder="" name="prenom-licencie" maxlength="15">
                            </div>
                            <div class="form-modif-li">
                                <label for="photo-licencie">
                                    <i class="fa fa-picture-o"></i>
                                    Photo
                                    <input id="photo-licencie" type="file" accept="image/png, image/jpeg" />
                                    <span id="nom-photo-licencie"></span>
                                </label>
                                <input value="<?= $dateN_licencie ?>" type="date" placeholder="Date de naissance" name="dateN-licencie">
                            </div>
                            <div class="form-modif-li">
                                <select name="categorie-licencie" id="categorie-licencie">
                                    <?php
                                    $req_category = $db->query("SELECT idCategorie, nomCategorie FROM categorie");
                                    while ($category = $req_category->fetch()) :
                                        if (isset($category)) :
                                    ?>
                                            <option value="<?= $category["idCategorie"]; ?>" <?php if ($category_licencie == $category["nomCategorie"]) : ?> selected <?php endif; ?>><?= $category["nomCategorie"] ?></option>
                                    <?php
                                        endif;
                                    endwhile;
                                    $req_category->closeCursor(); ?>
                                </select>
                                <select name="sexe-licencie" id="sexe-licencie">
                                    <option value="m" <?php if ($sexe_licencie == "m") : ?>selected<?php endif; ?>>Homme</option>
                                    <option value="f" <?php if ($sexe_licencie == "f") : ?>selected<?php endif; ?>>Femme</option>
                                </select>
                            </div>
                            <div class="mail-form-modif-li">
                                <input value="<?= $mail_licencie ?>" type="mail" class="mail-licencie" name="mail-licencie" placeholder="" maxlength="40">
                            </div>
                            <input type="hidden" name="idLicencie" value="<?php if (isset($idLicencie)) : echo $idLicencie;
                                                                            endif; ?>">
                            <div class="form-modif-li">
                                <input type="submit" value="Enregistrer" name="submit-modif" class="bouton-ajouter">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="return deconnect">
                    <a href="licencies.php">Annuler</a>
                </div>
            </div>
        </div>
        <script>
            let input = document.getElementById("photo-licencie");
            let imageName = document.getElementById("nom-photo-licencie")

            input.addEventListener("change", () => {
                let inputImage = document.querySelector("input[type=file]").files[0];

                imageName.innerText = inputImage.name;
            })
        </script>
        <?php else : require "./components/logged.php"; ?><?php endif; ?>
</body>

</html>