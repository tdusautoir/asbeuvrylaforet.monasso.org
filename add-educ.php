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
                <?php if (isset_flash_message_by_type(FLASH_SUCCESS)) : ?>
                    <div class="add-success"><?php display_flash_message_by_type(FLASH_SUCCESS); ?></div>
                <?php elseif (isset_flash_message_by_type(FLASH_ERROR)) : ?>
                    <div class="add-error"><?php display_flash_message_by_type(FLASH_ERROR); ?></div>
                <?php endif; ?>
                <div class="add-container">
                    <div class="li-admin">
                        <h2>
                            Derniers éducateurs ajoutés :
                        </h2>
                        <?php
                        //TODO : Procédure
                        $req = $db->prepare("SELECT educ.idEduc, educ.prenom, educ.nom, educ.mail, educ.USRCRE FROM educ WHERE educ.COSU = 0 "); //Derniers licenciés ajoutés classé par date croissant et limités à 10. 
                        $req->execute();
                        $rowCount = $req->rowCount();
                        if ($rowCount > 0) : //si on trouve des licenciés ajoutés on affiche la liste de la requete.
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
                    <div class="add-panel">
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
                            </div>
                            <div class="mail-form-add">
                                <input type="mail" class="mail-licencie" name="mail-educ" placeholder="Adresse mail" maxlength="40">
                            </div>
                            <div class="form-add">
                                <table class="mul-selec-table">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Attribution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $req = $db->prepare("CALL PRC_LSTCAT"); //Liste des catégories
                                            $req->execute();
                                            $rowCount = $req->rowCount();
                                            if ($rowCount > 0) :
                                                $rows = $req->fetchAll(PDO::FETCH_ASSOC);
                                                $req->closeCursor();

                                                foreach($rows as $CAT) :                                        
                                        ?>
                                                <tr>
                                                    <td><?= $CAT["nomCategorie"] ?></td>
                                                    <td>
                                                        <input type="checkbox" id="table-cb" name="<?= $CAT["nomCategorie"]?>-cb">
                                                    </td>
                                                </tr>
                                        <?php
                                            endforeach; else :
                                        ?>
                                            <tr>
                                                <td>Aucune catégorie disponible</td>
                                            </tr>
                                        <?php
                                            endif;
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-add">
                                <label style="padding: 0; cursor: default; display:flex; justify-content: center; border: none; min-width:0;" for="check-resp-hide ">
                                    Responsable
                                    <input id="check-resp" type="checkbox" name="resp-educ">
                                </label>
                                <input id="check-resp-hide" type="checkbox">
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
            var expanded = false;

            function showCheckboxes() {
                var checkboxes = document.getElementById("checkboxes");
                if (!expanded) {
                    checkboxes.style.display = "block";
                    expanded = true;
                } else {
                    checkboxes.style.display = "none";
                    expanded = false;
                }
            }
        </script>
        <?php else : require "./components/logged.php"; ?><?php endif; ?>
</body>

</html>