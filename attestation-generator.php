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
    <title>Générer une attestation - A.S. BEUVRY LA FORÊT</title>
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
                                <label for="category">Catégorie du licencié</label>
                                <select name='categorie' onChange="submit()" class="filter-category">
                                    <option disabled <?php if (!isset($_GET['categorie']) || empty($_GET['categorie'])) : ?>selected<?php endif; ?>>Categorie</option>
                                    <?php
                                    if (is_admin()) :
                                        $getCategorie = $db->query("SELECT DISTINCT nomCategorie FROM categorie WHERE COSU = 0");
                                        if ($getCategorie->rowCount() > 0) :
                                            while ($categorie = $getCategorie->fetch()) : ?>
                                                <option value="<?= $categorie['nomCategorie']; ?>" <?php if (isset($_GET['categorie']) && $categorie['nomCategorie'] == $_GET['categorie']) : ?>selected<?php endif; ?>><?= $categorie['nomCategorie']; ?></option>
                                            <?php endwhile;
                                        endif;
                                    elseif (is_educ()) :
                                        $getCategorie = $db->prepare(" SELECT categorie.nomCategorie FROM `categorieeduc` INNER JOIN categorie ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE educ.idEduc = :idEduc");
                                        $getCategorie->bindValue("idEduc", $_SESSION['id']);
                                        $getCategorie->execute();
                                        if ($getCategorie->rowCount() > 0) :
                                            while ($categorie = $getCategorie->fetch()) : ?>
                                                <option value="<?= $categorie['nomCategorie']; ?>" <?php if (isset($_GET['categorie']) && $categorie['nomCategorie'] == $_GET['categorie']) : ?>selected<?php endif; ?>><?= $categorie['nomCategorie']; ?></option>
                                    <?php endwhile;
                                        endif;
                                    endif;
                                    ?>
                                </select>
                            </form>
                        </div>
                        <div>
                            <form method="GET">
                                <label for="q">Tapez le nom du licencié</label>
                                <input type="search" name="q" placeholder="Recherche..." />
                                <input type="submit" value="" />
                            </form>
                        </div>
                    </div>

                    <?php if (isset($_GET) && !empty($_GET)) : ?>
                        <a href="./attestation-generator.php" class="cancel-filter">Annuler les filtres</a>
                    <?php endif; ?>

                    <div class="li-container">
                        <div class="li-li-admin">
                            <h2>
                                Générer une attestation :
                            </h2>
                            <?php
                            if (is_admin()) :
                                if (isset($_GET['q']) && !empty($_GET['q'])) :
                                    $q_ = explode(' ', $_GET['q']); //take only the first word
                                    $q = $q_[0];
                                    $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN cotis ON licencie.idLicencie = cotis.idLicencie WHERE licencie.COSU = 0 AND licencie.nom AND cotis.etat != 1 LIKE '%$q%' ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la recherche q
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                elseif (isset($_GET['categorie']) && !empty($_GET['categorie']) && $_GET['categorie'] != '') :
                                    $categorie = $_GET['categorie'];
                                    $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN cotis ON licencie.idLicencie = cotis.idLicencie WHERE licencie.COSU = 0 AND categorie.nomCategorie = :categorie AND cotis.etat != 1 ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la categorie
                                    $req->bindValue('categorie', $categorie);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                else :
                                    $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN cotis ON licencie.idLicencie = cotis.idLicencie WHERE licencie.COSU = 0 AND cotis.etat != 1 ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd classé par date croissant 
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                endif;
                            elseif (is_educ()) :
                                if (isset($_GET['q']) && !empty($_GET['q'])) :
                                    $q_ = explode(' ', $_GET['q']); //take only the first word
                                    $q = $q_[0];
                                    $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc INNER JOIN cotis ON licencie.idLicencie = cotis.idLicencie WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND licencie.nom AND cotis.etat != 1 LIKE '%$q%' ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la recherche q et les catégories associés à l'educateur connecté
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                elseif (isset($_GET['categorie']) && !empty($_GET['categorie']) && $_GET['categorie'] != '') :
                                    $categorie = $_GET['categorie'];
                                    $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc INNER JOIN cotis ON licencie.idLicencie = cotis.idLicencie WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND categorie.nomCategorie = :categorie AND cotis.etat != 1 ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la recherche q et les catégories associés à l'educateur connecté
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->bindValue('categorie', $categorie);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                else :
                                    $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc INNER JOIN cotis ON licencie.idLicencie = cotis.idLicencie WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND cotis.etat != 1 ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon les catégories associés à l'educateur connecté 
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                endif;
                            endif;

                            if ($rowCount > 0) : //si on trouve des licenciés ajoutés on affiche la liste de la requete.
                            ?>
                                <div class="licencie-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Nom</th>
                                                <th>Prénom</th>
                                                <th>Naissance</th>
                                                <th>Adresse mail</th>
                                                <th>Téléphone</th>
                                                <th>Cotisation</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php while ($LIC = $req->fetch(PDO::FETCH_ASSOC)) : ?>
                                                <tr>
                                                    <td>
                                                        <?= $LIC["nomCategorie"] ?>
                                                    </td>
                                                    <td title=<?= strtoupper(htmlspecialchars($LIC["nom"])) ?>>
                                                        <?= strtoupper(htmlspecialchars($LIC["nom"])) ?>
                                                    </td>
                                                    <td title=<?= ucfirst(htmlspecialchars($LIC["prenom"])) ?>>
                                                        <?= ucfirst(htmlspecialchars($LIC["prenom"])) ?>
                                                    </td>
                                                    <td>
                                                        <?= strftime('%d-%m-%Y', strtotime($LIC["dateN"])); //Y-m-d format to d-m-Y format 
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?= $LIC["mail"] ?>
                                                    </td>
                                                    <td>
                                                        <?php $getTel = $db->prepare("SELECT tel.tel FROM tel WHERE tel.idLicencie = ? AND tel.COSU = 0");
                                                        $getTel->bindValue(1, $LIC['idLicencie']);
                                                        $getTel->execute();
                                                        if ($getTel->rowCount() > 0) :
                                                            $result_getTel = $getTel->fetch(PDO::FETCH_ASSOC);
                                                            echo wordwrap($result_getTel["tel"], 2, " ", 1); //0000000000 to 00 00 00 00 00
                                                        endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php $getCotis = $db->prepare("SELECT cotis.prix, cotis.etat FROM cotis WHERE cotis.idLicencie = ? AND cotis.COSU = 0");
                                                        $getCotis->bindValue(1, $LIC['idLicencie']);
                                                        $getCotis->execute();
                                                        if ($getCotis->rowCount() > 0) :
                                                            $Cotis = $getCotis->fetch(PDO::FETCH_ASSOC);
                                                            if ($Cotis["etat"] == 1) : ?>
                                                                <span title="non réglée" class="state-indicator" style="background-color: red;"></span>
                                                            <?php elseif ($Cotis["etat"] == 2) : ?>
                                                                <span title="réglée" class="state-indicator" style="background-color: orange;"></span>
                                                            <?php elseif ($Cotis["etat"] == 3) : ?>
                                                                <span title="non encaissée" class="state-indicator" style="background-color: white; border: 1px solid green;"></span>
                                                            <?php elseif ($Cotis["etat"] == 4) : ?>
                                                                <span title="encaissée" class="state-indicator" style="background-color: green;"></span>
                                                        <?php endif;
                                                            echo $Cotis["prix"] . " €";
                                                        endif; ?>
                                                    </td>
                                                    <td></td>
                                                    <td class="action-btns btns-1">
                                                        <a href="./attestation-generate.php?idLicencie=<?= $LIC["idLicencie"] ?>">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    </td>
                                                    <td class="action-btns btns-2">
                                                        <a href="./attestation-generate.php?idLicencie=<?= $LIC["idLicencie"] ?>">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php elseif (isset($_GET) && !empty($_GET)) : ?>
                                <p> Aucun licencié ne correspond à votre recherche. </p>
                            <?php else : ?>
                                <p> Aucun licencié n'a encore été créé </p>
                            <?php endif;
                            $req->closeCursor(); // Ferme le curseur, permettant à la requête d'être de nouveau exécutée 
                            ?>
                        </div>
                        <!-- <div class="return deconnect">
                        <a href="index.php">Retour</a>
                    </div> -->
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="./public/js/tableau.js"></script>
        <?php require './components/footer.php'; ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>