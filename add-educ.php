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
    <title>Espace Ajout éducateurs - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php include('./components/header.php'); ?>
        <div class="container">
            <div class="container-content">
                <?php include "./components/display_error.php"; ?>
                <div class="add-container">
                    <div class="li-admin">
                        <h2>
                            Derniers éducateurs ajoutés :
                        </h2>
                        <?php
                        //TODO : Procédure
                        $req = $db->prepare("CALL PRC_TENEDUC()"); //Derniers educateurs ajoutés classé par date croissant et limités à 10. 
                        $req->execute();
                        $rowCount = $req->rowCount();
                        if ($rowCount > 0) : //si on trouve des educateurs ajoutés on affiche la liste de la requete.
                        ?>
                            <ul>
                                <?php while ($LIC = $req->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <li>
                                        <p>
                                            <span><?= $LIC["prenom"] . " " . strtoupper($LIC["nom"]) ?></span>
                                            <?php if (isset($LIC["USRCRE"])) : ?>par
                                            <span><?= $LIC["USRCRE"]; ?> </span>
                                        </p> <?php endif; ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php else : ?>
                            <p> Aucun éducateurs n'a encore été créé </p>
                        <?php endif;
                        $req->closeCursor(); ?>
                        <div class="add-panel-separator"></div>
                    </div>
                    <div class="add-panel" id="fade-in">
                        <h1>
                            Ajouter un éducateur
                        </h1>
                        <form action="./functions/educ-add.php" method="POST">
                            <div class="form-add">
                                <input type="text" class="nom-licencie" placeholder="Nom" name="nom-educ" maxlength="20">
                                <input type="text" class="prenom-licencie" placeholder="Prénom" name="prenom-educ" maxlength="15">
                            </div>
                            <div class="form-add">
                                <input type="password" class="password-licencie" name="password-educ" placeholder="Mot de passe" maxlength="40">
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
                                        foreach ($rows as $CAT) :
                                    ?>
                                            <div class="cate-check">
                                                <p style="cursor: default; border: none;"><?= $CAT["nomCategorie"] ?></p>
                                                <input type="checkbox" name="<?= $CAT["nomCategorie"] ?>-cb">
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
                                <input type="mail" class="mail-licencie" name="mail-educ" placeholder="Adresse mail" maxlength="40">
                            </div>
                            <div class="form-add list-cate-div">
                                <div class="responsable">
                                    <label for="check-resp">
                                        Responsable
                                    </label>
                                    <input id="check-resp" type="checkbox" style="margin:0;">
                                </div>
                            </div>
                            <div class="form-add">
                                <input type="submit" value="Ajouter" name="submit-add" class="bouton-ajouter">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="return deconnect">
                    <a href="index.php">Retour</a>
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
        <script>
            function displayModal(idModal) {
                document.getElementById(idModal).style.display = "flex";
            }

            function erase(idModal) {
                document.getElementById(idModal).style.display = "none";
            }
        </script>
        <?php else : require "./components/logged.php"; ?><?php endif; ?>
</body>

</html>