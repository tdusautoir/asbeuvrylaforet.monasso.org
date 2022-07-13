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
    <title>Modification de licencié - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <div class="content">
            <?php include('./components/header.php');
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

                    $getTel = $db->prepare("SELECT tel.tel FROM tel WHERE tel.idLicencie = ? AND tel.COSU = 0");
                    $getTel->bindValue(1, $idLicencie);
                    $getTel->execute();
                    if ($getTel->rowCount() > 0) :
                        $result_getTel = $getTel->fetch(PDO::FETCH_ASSOC);
                        $tel_licencie = $result_getTel["tel"];
                    endif;
                } else {  //licencie is not in db or is deleted
                    header("location: ./licencies.php");
                    create_flash_message("not_found", "Licencié introuvable.", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ./licencies.php");
                create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                exit();
            } ?>
            <div class="container">
                <div class="container-content">
                    <?php include "./components/display_error.php"; ?>
                    <div class="profil-container">
                        <div class="profil-container-part1" id="profil-container-part1">
                            <div class="profil-img">
                                <img src="./public/profiles/david_vincent_20220712_resize.png" draggable="false" alt="">
                            </div>
                            <div class="profil-content">
                                <div class="profil-content-head">
                                    <h1>&#x1F3C3; Profil du licencié :</h1>
                                    <div class="welcome-separator" style="width: 80%;"></div>
                                </div>
                                <div class="profil-content-tab" id="profil-content-tab">
                                    <div class="profil-content-tab-ligne profil-content-tab-ligne-head">
                                        <p>Informations</p>
                                        <i class="fa fa-pencil" onclick="displayEdit()"></i>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Nom complet</p>
                                        <p><?= $firstname_licencie ?> <?= $lastname_licencie ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Date de naissance</p>
                                        <p><?= $dateN_licencie ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Catégorie</p>
                                        <p>U10</p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Sexe</p>
                                        <p>
                                            <!-- <?= htmlspecialchars($sexe_licencie) ?> -->
                                            Homme
                                        </p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Téléphone</p>
                                        <p>
                                            <?= htmlspecialchars($tel_licencie) ?>
                                        </p>
                                    </div>
                                    <div class="profil-content-tab-ligne profil-content-tab-ligne-foot">
                                        <p>Mail</p>
                                        <p><?= htmlspecialchars($mail_licencie) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profil-container-part1" id="profil-container-part1-edit">
                            <div class="profil-img">
                                <label for="photo-li-modif">
                                    <div class="profil-img-import">
                                        <i class="fa fa-picture-o"></i>
                                        <p>Importez une image</p>
                                        <span id="nom-photo-li-modif"></span>
                                    </div>
                                    <input id="photo-li-modif" type="file" accept="image/png, image/jpeg" name="logo" value="<?= $get_settings['logoPath'] ?>">
                                </label>
                            </div>
                            <div class="profil-content">
                                <div class="profil-content-head">
                                    <h1>&#x1F3C3; Profil du licencié :</h1>
                                    <div class="welcome-separator" style="width: 80%;"></div>
                                </div>
                                <div class="profil-content-tab">
                                    <div class="profil-content-tab-ligne profil-content-tab-ligne-head">
                                        <p>Informations</p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Nom complet</p>
                                        <p contenteditable="true" class="profil-editable"><?= $firstname_licencie ?> <?= $lastname_licencie ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Date de naissance</p>
                                        <p><?= $dateN_licencie ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Catégorie</p>
                                        <p>U10</p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Sexe</p>
                                        <p>
                                            <!-- <?= htmlspecialchars($sexe_licencie) ?> -->
                                            Homme
                                        </p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Téléphone</p>
                                        <p contenteditable="true" class="profil-editable"><?= htmlspecialchars($tel_licencie) ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne profil-content-tab-ligne-foot">
                                        <p>Mail</p>
                                        <p contenteditable="true" class="profil-editable"><?= htmlspecialchars($mail_licencie) ?></p>
                                    </div>
                                    <!-- <div class="profil-content-tab-ligne profil-content-tab-ligne-foot">
                                        <p>Importer une photo</p>
                                        <label for="photo-li-modif">
                                            <i class="fa fa-upload"></i>
                                            <input id="photo-li-modif" type="file" accept="image/png, image/jpeg" name="logo" value="<?= $get_settings['logoPath'] ?>">
                                        </label>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="profil-content-tab-button" id="profil-content-tab-button">
                            <input type="submit" name="submit-settings" value="Modifier">
                            <button onclick="hideEdit()">Annuler</button>
                        </div>
                    </div>
                    <!-- <div class="modif-li-container">
                        <div class="modif-li-panel">
                            <h1>
                                Profil du
                            </h1>
                            <form action="./functions/licencie-modif.php" enctype="multipart/form-data" method="POST">
                                <div class="form-modif-li">
                                    <input value="<?= htmlspecialchars($lastname_licencie) ?>" type="text" class="nom-licencie" placeholder="" name="nom-licencie" maxlength="20">
                                    <input value="<?= htmlspecialchars($firstname_licencie) ?>" type="text" class="prenom-licencie" placeholder="" name="prenom-licencie" maxlength="15">
                                </div>
                                <div class="form-modif-li">
                                    <label for="photo-licencie">
                                        <i class="fa fa-picture-o"></i>
                                        Photo
                                        <input id="photo-licencie" type="file" name="photo-licencie" accept="image/png, image/jpeg" />
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
                                <div class="form-modif-li">
                                    <input value="<?= htmlspecialchars($mail_licencie) ?>" type="mail" class="mail-licencie" name="mail-licencie" placeholder="" maxlength="40">
                                    <?php if (isset($tel_licencie)) : ?>
                                        <input value="<?= $tel_licencie ?>" type="tel" class="" name="tel-licencie" placeholder="">
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" name="idLicencie" value="<?php if (isset($idLicencie)) : echo $idLicencie;
                                                                                endif; ?>">
                                <div class="loading" id='loading'>
                                    <img src="./public/images/Rolling-1s-200px-gray.svg">
                                </div>
                                <div class="form-modif-li">
                                    <input type="submit" value="Enregistrer" name="submit-modif" class="bouton-ajouter" id="form-submit" onclick="loading()">
                                </div>
                            </form>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        <script>
            let input = document.getElementById("photo-li-modif");
            let imageName = document.getElementById("nom-photo-li-modif")

            input.addEventListener("change", (e) => {
                let inputImage = e.target.files[0];

                imageName.innerText = inputImage.name;
            })
        </script>
        <script>
            function displayEdit() {
                document.getElementById("profil-container-part1").style.display = "none";
                document.getElementById("profil-container-part1-edit").style.display = "flex";
                document.getElementById("profil-content-tab-button").style.display = "flex";
            }

            function hideEdit() {
                document.getElementById("profil-container-part1-edit").style.display = "none";
                document.getElementById("profil-content-tab-button").style.display = "none";
                document.getElementById("profil-container-part1").style.display = "flex";
            }
        </script>
        <script>
            let input = document.getElementById("photo-licencie");
            let imageName = document.getElementById("nom-photo-licencie")

            input.addEventListener("change", () => {
                let inputImage = document.querySelector("input[type=file]").files[0];

                imageName.innerText = inputImage.name;
            })
        </script>
        <?php require './components/footer.php'; ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>