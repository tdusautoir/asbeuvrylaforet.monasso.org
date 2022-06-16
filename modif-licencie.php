<?php
session_start();

require("./function.php");

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
        <?php include('./components/header.php'); ?>
        <div class="container">
            <div class="container-content">
                <?php if (isset_flash_message_by_type(FLASH_SUCCESS)) : ?>
                    <div class="add-success"><?php display_flash_message_by_type(FLASH_SUCCESS); ?></div>
                <?php elseif (isset_flash_message_by_type(FLASH_ERROR)) : ?>
                    <div class="add-error"><?php display_flash_message_by_type(FLASH_ERROR); ?></div>
                <?php endif; ?>
                <div class="modif-li-container">
                    <div class="modif-li-panel">
                        <h1>
                            Modifier un licencié
                        </h1>
                        <form action="#" method="POST">
                            <div class="form-modif-li">
                                <input type="text" class="nom-licencie" placeholder="Nom" name="nom-licencie" maxlength="20">
                                <input type="text" class="prenom-licencie" placeholder="Prénom" name="prenom-licencie" maxlength="15">
                            </div>
                            <div class="form-modif-li">
                                <label for="photo-licencie">
                                    Photo du licencié
                                    <input id="photo-licencie" type="file" accept="image/png, image/jpeg" />
                                    <span id="nom-photo-licencie"></span>
                                </label>
                                <input type="date" placeholder="Date de naissance" name="dateN-licencie">
                            </div>
                            <div class="form-modif-li">
                                <select name="categorie-licencie" id="categorie-licencie">
                                    <option value="" disabled selected>Catégorie</option>
                                    <?php
                                    $req_category = $db->query("SELECT idCategorie, nomCategorie FROM categorie");
                                    while ($category = $req_category->fetch()) :
                                        if (isset($category)) :
                                    ?>
                                            <option value="<?= $category["idCategorie"] ?>"><?= $category["nomCategorie"] ?></option>
                                    <?php
                                        endif;
                                    endwhile;
                                    $req_category->closeCursor(); ?>
                                </select>
                                <select name="sexe-licencie" id="sexe-licencie">
                                    <option value="" disabled selected>Sexe</option>
                                    <option value="m">Homme</option>
                                    <option value="f">Femme</option>
                                </select>
                            </div>
                            <div class="mail-form-modif-li">
                                <input type="mail" class="mail-licencie" name="mail-licencie" placeholder="Adresse mail" maxlength="40">
                            </div>
                            <div class="form-modif-li">
                                <input type="submit" value="Enregistrer" name="submit" class="bouton-ajouter">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="return deconnect">
                    <a href="licencies.php">Retour</a>
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