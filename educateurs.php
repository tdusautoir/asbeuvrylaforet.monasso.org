<?php
session_start();

require("./function.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}

if (is_educ()) {
    create_flash_message("no_rights", "Vous ne possédez pas les droits.", FLASH_ERROR); //the user is not admin 
    header("location: ./index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">

<head> <?php require("./components/head.php"); ?>
    <title>Éducateurs - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <div class="content">
            <?php include('./components/header.php'); ?>
            <div class="container">
                <div class="container-content">
                    <?php include "./components/display_error.php"; ?>
                    <div class="filter">
                        <div>
                            <form method="GET">
                                <label for="q">Tapez le nom de l'éducateur</label>
                                <input type="search" name="q" placeholder="Recherche..." />
                                <input type="submit" value="" />
                            </form>
                        </div>
                    </div>
                    <?php if (isset($_GET['q']) && !empty($_GET['q']) && $_GET['q'] != '') : ?>
                        <a href="./educateurs.php" class="cancel-filter">Annuler les filtres</a>
                    <?php endif; ?>
                    <div class="edu-container">
                        <div class="edu-content">
                            <h2>
                                &#129489; Liste des éducateurs :
                            </h2>
                            <?php
                            if (isset($_GET['q']) && !empty($_GET['q'])) :
                                $q_ = explode(' ', $_GET['q']);
                                $q = $q_[0];
                                $req = $db->prepare("SELECT educ.idEduc, educ.prenom, educ.nom, educ.mail FROM `educ` WHERE educ.COSU = 0 AND educ.nom LIKE '%$q%' ORDER BY educ.DCRE DESC;"); //éducateurs de la bdd selon la recherche q
                                $req->execute();
                                $rowCount = $req->rowCount();
                            else :
                                $req = $db->prepare("SELECT educ.idEduc, educ.prenom, educ.nom, educ.mail FROM `educ` WHERE educ.COSU = 0 ORDER BY educ.DCRE DESC;"); //Liste des éducateurs classé par date d'ajout
                                $req->execute();
                                $rowCount = $req->rowCount();
                            endif;
                            if ($rowCount > 0) : //si on trouve des educateurs ajoutés on affiche la liste de la requete.
                            ?>
                                <div class="educateur-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom</th>
                                                <th>Prénom</th>
                                                <th>Adresse mail</th>
                                                <th>Catégorie(s)</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php
                                            //Tout récupérer et stocker les lignes dans un array puis le parcourir
                                            //nous permettant d'appeler une procédure pour chaque ligne du tableau
                                            $rows = $req->fetchAll(PDO::FETCH_ASSOC);
                                            $req->closeCursor();
                                            foreach ($rows as $EDUC) :
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?= strtoupper(htmlspecialchars($EDUC["nom"])) ?>
                                                    </td>
                                                    <td>
                                                        <?= ucfirst(htmlspecialchars($EDUC["prenom"])) ?>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($EDUC["mail"]) ?>
                                                    </td>
                                                    <td>
                                                        <?php

                                                        $educId = $EDUC["idEduc"];
                                                        $educCat = "";
                                                        $reqCat = $db->prepare("CALL PRC_GETEDUCAT($educId)"); //Liste des catégories d'un éducateur
                                                        $reqCat->execute();
                                                        $catRowCount = $reqCat->rowCount();

                                                        if ($catRowCount > 0) {
                                                            $row = 1;

                                                            $catRows = $reqCat->fetchAll(PDO::FETCH_ASSOC);
                                                            $reqCat->closeCursor();
                                                            foreach ($catRows as $CAT) {
                                                                if ($row == 1) {
                                                                    $educCat = $CAT["nomCategorie"];
                                                                    $row++;
                                                                } else {
                                                                    //Concaténation dans une chaîne de toutes les catégories 
                                                                    $educCat = $educCat . ", " . $CAT["nomCategorie"];
                                                                }
                                                            }
                                                        } else {
                                                            $educCat = "Aucune catégorie attribuée";
                                                        }
                                                        ?>

                                                        <?= htmlspecialchars($educCat) ?>
                                                    </td>
                                                    <td class="action-btns btns-1">
                                                        <a href="./modif-educ.php?idEduc=<?= $EDUC["idEduc"] ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td class="action-btns btns-2">
                                                        <a href="#" onclick="displayModalDelete('<?= $EDUC['idEduc']; ?>')">
                                                            <i class=" fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php elseif (isset($_GET) && !empty($_GET)) : ?>
                                <p> Aucun éducateur ne correspond à votre recherche. </p>
                            <?php else : ?>
                                <p> Aucun éducateur n'a encore été créé </p>
                            <?php endif; ?>
                        </div>
                        <!-- <div class="return deconnect">
                            <a href="index.php">Retour</a>
                        </div> -->
                    </div>
                </div>
            </div>
            <div id="Modal">
                <div class="Modal-delete" id="Modal-delete">
                    <p>Confirmez la suppression</p>
                    <div class="modal-button">
                        <a id="valid-btn"><i class="fa fa-check"></i></a>
                        <a id="erase-btn" onclick="erase()"><i class="fa fa-times"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="./public/js/tableau.js"></script>
        <script type="text/javascript" src="./public/js/modal-educ.js"></script>
        <?php require './components/footer.php'; ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>