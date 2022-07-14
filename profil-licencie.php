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
            //verifier si l'id est défini dans l'url pour afficher le licencie en question et si c'est bien un entier
            if (isset($_GET["idLicencie"]) && !empty($_GET["idLicencie"]) && isInteger($_GET["idLicencie"])) {
                $idLicencie = $_GET["idLicencie"];
                if (is_admin()) :
                    $info = $db->prepare("SELECT licencie.nom, licencie.prenom, licencie.dateN, licencie.mail, licencie.sexe, categorie.nomCategorie FROM licencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.idLicencie = ? AND licencie.COSU = 0");
                    $info->bindValue(1, $idLicencie);
                else :
                    $info = $db->prepare("SELECT licencie.nom, licencie.prenom, licencie.dateN, licencie.mail, licencie.sexe, categorie.nomCategorie FROM licencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE licencie.idLicencie = ? AND categorieeduc.idEduc = ? AND licencie.COSU = 0");
                    $info->bindValue(1, $idLicencie);
                    $info->bindValue(2, $_SESSION['id']);
                endif;
                $info->execute();
                if ($info->rowCount() > 0) { //Verifier si la requete de recupération des infos nous renvoient les infos.

                    //recuperation des infos
                    $getinfo = $info->fetch(PDO::FETCH_ASSOC);
                    $firstname_licencie = $getinfo["prenom"];
                    $lastname_licencie = $getinfo["nom"];
                    $dateN_licencie = $getinfo["dateN"];
                    $mail_licencie = $getinfo["mail"];
                    $sexe_licencie = $getinfo["sexe"];
                    $category_licencie = $getinfo["nomCategorie"];


                    //recuperer le telephone
                    $getTel = $db->prepare("SELECT tel.tel FROM tel WHERE tel.idLicencie = ? AND tel.COSU = 0");
                    $getTel->bindValue(1, $idLicencie);
                    $getTel->execute();
                    if ($getTel->rowCount() > 0) :
                        $result_getTel = $getTel->fetch(PDO::FETCH_ASSOC);
                        $tel_licencie = $result_getTel["tel"];
                    endif;

                    //recuperer la taille
                    $getTaille = $db->prepare("SELECT taille.nom FROM taille INNER JOIN licencie ON licencie.idTaille = taille.idTaille WHERE licencie.idLicencie = ? AND licencie.COSU = 0");
                    $getTaille->bindValue(1, $idLicencie);
                    $getTaille->execute();
                    if ($getTaille->rowCount() > 0) :
                        $result_getTaille = $getTaille->fetch(PDO::FETCH_ASSOC);
                        $taille_licencie = $result_getTaille["nom"];
                    endif;

                    //recupérer le lien de la photo
                    $getPhoto = $db->prepare("SELECT photo.imgPath FROM photo INNER JOIN licencie ON licencie.idPhoto = photo.idPhoto WHERE licencie.idLicencie = ? AND photo.cosu = 0");
                    $getPhoto->bindValue(1, $idLicencie);
                    $getPhoto->execute();
                    if ($getPhoto->rowCount() > 0) :
                        $result_getPhoto = $getPhoto->fetch(PDO::FETCH_ASSOC);
                        $url_photo = $result_getPhoto["imgPath"];
                    endif;
                } else {
                    if (is_educ()) { //Aucun licenciés trouvés dans sa/ses catégories.
                        header("location: ./licencies.php");
                        create_flash_message("not_found", "Licencié introuvable dans vos catégories.", FLASH_ERROR);
                        exit();
                    } else {
                        header("location: ./licencies.php");
                        create_flash_message("not_found", "Licencié introuvable.", FLASH_ERROR);
                        exit();
                    }
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
                                <img src="<?= $url_photo ?>" draggable="false" alt="">
                            </div>
                            <div class="profil-content">
                                <div class="profil-content-head">
                                    <h1>&#x1F3C3; Profil du licencié :</h1>
                                    <div class="welcome-separator" style="width: 15%;"></div>
                                </div>
                                <div class="profil-content-tab" id="profil-content-tab">
                                    <div class="profil-content-tab-ligne profil-content-tab-ligne-head">
                                        <p>Informations</p>
                                        <i class="fa fa-pencil" onclick="displayEdit()"></i>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Nom complet</p>
                                        <p title="<?= $firstname_licencie ?> <?= $lastname_licencie ?>"><?= $firstname_licencie ?> <?= $lastname_licencie ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Date de naissance</p>
                                        <p><?= $dateN_licencie ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Catégorie</p>
                                        <p><?= $category_licencie ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Sexe</p>
                                        <p>
                                            <?php if ($sexe_licencie == 'f') : ?>
                                                Femme
                                            <?php else : ?>
                                                Homme
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Téléphone</p>
                                        <p>
                                            <?= htmlspecialchars($tel_licencie) ?>
                                        </p>
                                    </div>
                                    <div class="profil-content-tab-ligne">
                                        <p>Mail</p>
                                        <p title="<?= htmlspecialchars($mail_licencie) ?>"><?= htmlspecialchars($mail_licencie) ?></p>
                                    </div>
                                    <div class="profil-content-tab-ligne profil-content-tab-ligne-foot">
                                        <p>Taille</p>
                                        <?php if (isset($taille_licencie)) : ?>
                                            <p><?= htmlspecialchars($taille_licencie) ?></p>
                                        <?php else : ?>
                                            <p> Non définie </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form action="./functions/licencie-modif.php" method="POST" enctype="multipart/form-data">
                            <div class="profil-container-part1 modif" id="profil-container-part1-edit">
                                <div class="profil-img">
                                    <label for="photo-li-modif">
                                        <div class="profil-img-import">
                                            <i class="fa fa-picture-o"></i>
                                            <p>Importez une image</p>
                                            <span id="nom-photo-li-modif"></span>
                                        </div>
                                        <input id="photo-li-modif" type="file" accept="image/png, image/jpeg" name="photo-licencie" value="test">
                                    </label>
                                </div>
                                <div class="profil-content">
                                    <div class="profil-content-head">
                                        <h1>&#x1F3C3; Profil du licencié :</h1>
                                        <div class="welcome-separator" style="width: 15%;"></div>
                                    </div>
                                    <div class="profil-content-tab">
                                        <div class="profil-content-tab-ligne profil-content-tab-ligne-head">
                                            <p>Informations</p>
                                        </div>
                                        <div class="profil-content-tab-ligne">
                                            <p>Nom complet</p>
                                            <input id="inputName" type="text" value="<?= htmlspecialchars($firstname_licencie) . " " . htmlspecialchars($lastname_licencie) ?>" name="name-licencie" value="">
                                        </div>
                                        <div class="profil-content-tab-ligne">
                                            <p>Date de naissance</p>
                                            <input id="inputDate" value="<?= $dateN_licencie ?>" type="date" placeholder="Date de naissance" name="dateN-licencie">
                                        </div>
                                        <div class="profil-content-tab-ligne">
                                            <p>Catégorie</p>
                                            <select name="categorie-licencie">
                                                <?php
                                                if (is_admin()) :
                                                    $req_category = $db->query("SELECT idCategorie, nomCategorie FROM categorie");
                                                    while ($category = $req_category->fetch()) :
                                                        if (isset($category)) : ?>
                                                            <option value="<?= $category["idCategorie"]; ?>" <?php if ($category_licencie == $category["nomCategorie"]) : ?> selected <?php endif; ?>><?= $category["nomCategorie"] ?></option>
                                                    <?php endif;
                                                    endwhile;
                                                    $req_category->closeCursor(); ?>
                                                    <?php elseif (is_educ()) :
                                                    $req_category = $db->prepare("SELECT idCategorie, nomCategorie FROM categorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE categorieeduc.idEduc = :idEduc");
                                                    $req_category->bindValue(':idEduc', $_SESSION['id']);
                                                    $req_category->execute();
                                                    while ($category = $req_category->fetch()) :
                                                        if (isset($category)) : ?>
                                                            <option value="<?= $category["idCategorie"]; ?>" <?php if ($category_licencie == $category["nomCategorie"]) : ?> selected <?php endif; ?>><?= $category["nomCategorie"] ?></option>
                                                <?php endif;
                                                    endwhile;
                                                endif ?>
                                            </select>
                                        </div>
                                        <div class="profil-content-tab-ligne">
                                            <p>Sexe</p>
                                            <p>
                                                <select name="sexe-licencie" id="sexe-licencie">
                                                    <option value="m" <?php if ($sexe_licencie == "m") : ?>selected<?php endif; ?>>Homme</option>
                                                    <option value="f" <?php if ($sexe_licencie == "f") : ?>selected<?php endif; ?>>Femme</option>
                                                </select>
                                            </p>
                                        </div>
                                        <div class="profil-content-tab-ligne">
                                            <p>Téléphone</p>
                                            <input id="inputTel" type="text" name="tel-licencie" value="<?= htmlspecialchars($tel_licencie) ?>">
                                        </div>
                                        <div class="profil-content-tab-ligne">
                                            <p>Mail</p>
                                            <input id="inputMail" type="text" name="mail-licencie" value="<?= htmlspecialchars($mail_licencie) ?>" title="<?= htmlspecialchars($mail_licencie) ?>">
                                        </div>
                                        <div class="profil-content-tab-ligne profil-content-tab-ligne-foot">
                                            <p>Taille</p>
                                            <select name="taille-licencie" id="taille-licencie">
                                                <?php if (!isset($taille_licencie)) :
                                                    //si aucune taille défini afficher Taille 
                                                ?>
                                                    <option disabled selected>Taille</option>
                                                <?php endif; ?>
                                                <?php $req_taille = $db->query("SELECT idTaille, nom FROM taille");
                                                while ($taille = $req_taille->fetch()) :
                                                    if (isset($taille)) :
                                                        if (isset($taille_licencie)) : ?>
                                                            <option value="<?= $taille["idTaille"]; ?>" <?php if ($taille_licencie == $taille["nom"]) : ?> selected <?php endif; ?>><?= $taille["nom"] ?></option>
                                                        <?php else : ?>
                                                            <option value="<?= $taille["idTaille"]; ?>"><?= $taille["nom"] ?></option>
                                                        <?php endif; ?>
                                                <?php endif;
                                                endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="profil-content-tab-button" id="profil-content-tab-button">
                                <input type="hidden" name="idLicencie" value="<?php if (isset($idLicencie)) : echo $idLicencie;
                                                                                endif; ?>">
                                <div class="loading" id="loading">
                                    <img src="./public/images/Rolling-1s-200px-gray.svg">
                                </div>
                                <input type="submit" onclick="loading()" name="submit-modif" id="form-submit" value="Modifier">
                                <a href="" onclick="hideEdit()">Annuler</a>
                            </div>
                        </form>
                    </div>
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

            let name = document.getElementById('inputName'); // récuperer les input
            name.addEventListener('input', resizeInput); // A la modification de l'input, appeler la fonction de redimensionnement
            let tel = document.getElementById('inputTel'); // récuperer les input
            tel.addEventListener('input', resizeInput); // A la modification de l'input, appeler la fonction de redimensionnement
            let dateN = document.getElementById('inputDate'); // récuperer les input
            dateN.addEventListener('input', resizeInput); // A la modification de l'input, appeler la fonction de redimensionnement
            let mail = document.getElementById('inputMail'); // récuperer les input
            mail.addEventListener('input', resizeInput); // A la modification de l'input, appeler la fonction de redimensionnement
            if (name) {
                resizeInput.call(name); //Appeler la fonction
            }
            if (tel) {
                resizeInput.call(tel); //Appeler la fonction
            }
            if (dateN) {
                resizeInput.call(dateN); //Appeler la fonction
            }
            if (mail) {
                resizeInput.call(mail); //Appeler la fonction
            }

            function resizeInput() { //redimensionner les input
                let inputValue = this.value.length;
                this.style.width = (inputValue + 2) + "ch";
            }
        </script>
        <?php require './components/footer.php'; ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>