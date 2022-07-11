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
    <title>Cotisations - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php if (is_admin() || is_educ()) : ?>
            <?php include('./components/header.php'); ?>
            <div class="container">
                <div class="container-content">
                    <?php include "./components/display_error.php"; ?>
                    <div class="filter">
                        <div>
                            <form method="GET">
                                <label for="method">Méthode de la cotisation</label>
                                <select name='method' onChange="submit()" class="filter-category">
                                    <option disabled <?php if (!isset($_GET['method']) || empty($_GET['method'])) : ?>selected<?php endif; ?>>Methode</option>
                                    <?php
                                    if (is_admin()) :
                                        $getMethod = $db->query("SELECT DISTINCT cotis.methode FROM cotis WHERE COSU = 0 ORDER BY cotis.methode ASC");
                                        if ($getMethod->rowCount() > 0) :
                                            while ($method = $getMethod->fetch()) : ?>
                                                <?php if (isset($method['methode'])) : ?>
                                                    <option value="<?= $method['methode']; ?>" <?php if (isset($_GET['method']) && $method['methode'] == $_GET['method']) : ?>selected<?php endif; ?>>
                                                        <?php if ($method['methode'] == 1) : ?> Chèque
                                                        <?php elseif ($method['methode'] == 2) : ?> Espèce
                                                        <?php elseif ($method['methode'] == 3) : ?> Carte bancaire <?php endif; ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endwhile;
                                        endif;
                                    elseif (is_educ()) :
                                        $getMethod = $db->prepare("SELECT DISTINCT cotis.methode FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE COSU = 0 AND categorieeduc.idEduc = :idEduc ORDER BY cotis.methode ASC");
                                        if ($getMethod->rowCount() > 0) :
                                            $getMethod->bindValue("idEduc", $_SESSION['id']);
                                            $getMethod->execute();
                                            while ($method = $getMethod->fetch()) : ?>
                                                <?php if (isset($method['methode'])) : ?>
                                                    <option value="<?= $method['methode']; ?>" <?php if (isset($_GET['method']) && $method['methode'] == $_GET['method']) : ?>selected<?php endif; ?>>
                                                        <?php if ($method['methode'] == 1) : ?> Chèque
                                                        <?php elseif ($method['methode'] == 2) : ?> Espèce
                                                        <?php elseif ($method['methode'] == 3) : ?> Carte bancaire <?php endif; ?>
                                                    </option>
                                                <?php endif; ?>
                                    <?php endwhile;
                                        endif;
                                    endif;
                                    ?>
                                </select>
                            </form>
                        </div>
                        <div>
                            <form method="GET">
                                <label for="state">Etat de la cotisation</label>
                                <select name='state' onChange="submit()" class="filter-cotisation">
                                    <option disabled <?php if (!isset($_GET['state']) || empty($_GET['state'])) : ?>selected<?php endif; ?>>Etat</option>
                                    <?php
                                    if (is_admin()) :
                                        $getState = $db->query("SELECT DISTINCT cotis.etat FROM cotis WHERE COSU = 0 ORDER BY cotis.etat");
                                        if ($getState->rowCount() > 0) :
                                            while ($state = $getState->fetch()) : ?>
                                                <option value="<?= $state['etat']; ?>" <?php if (isset($_GET['state']) && $state['etat'] == $_GET['state']) : ?>selected<?php endif; ?>>
                                                    <?php if ($state['etat'] == 1) : ?> Non réglée <?php endif; ?>
                                                    <?php if ($state['etat'] == 2) : ?> Réglée <?php endif; ?>
                                                    <?php if ($state['etat'] == 3) : ?> Non encaissée <?php endif; ?>
                                                    <?php if ($state['etat'] == 4) : ?> Encaissée <?php endif; ?>
                                                </option>
                                            <?php endwhile;
                                        endif;
                                    elseif (is_educ()) :
                                        $getState = $db->prepare("SELECT DISTINCT cotis.etat FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie WHERE categorieeduc.idEduc = :idEduc;");
                                        $getState->bindValue("idEduc", $_SESSION['id']);
                                        $getState->execute();
                                        if ($getState->rowCount() > 0) :
                                            while ($state = $getState->fetch()) : ?>
                                                <option value="<?= $state['etat']; ?>" <?php if (isset($_GET['state']) && $state['state'] == $_GET['state']) : ?>selected<?php endif; ?>>
                                                    <?php if ($state['etat'] == 1) : ?> Non réglée <?php endif; ?>
                                                    <?php if ($state['etat'] == 2) : ?> Réglée <?php endif; ?>
                                                    <?php if ($state['etat'] == 3) : ?> Non encaissée <?php endif; ?>
                                                    <?php if ($state['etat'] == 4) : ?> Encaissée <?php endif; ?>
                                                </option>
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
                        <a href="./cotisations.php" class="cancel-filter">Annuler les filtres</a>
                    <?php endif; ?>
                    <div class="cotis-container">
                        <div class="cotis-content">
                            <h2>
                                Suivi des cotisations :
                            </h2>
                            <?php
                            if (is_admin()) :
                                if (isset($_GET['q']) && !empty($_GET['q'])) :
                                    $q_ = explode(' ', $_GET['q']); //take only the first word
                                    $q = $q_[0];
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie WHERE cotis.COSU = 0 AND licencie.nom LIKE '%$q%' ORDER BY cotis.DCRE DESC;"); //Liste des cotisations
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                elseif (isset($_GET['method']) && !empty($_GET['method']) && $_GET['method'] != '') :
                                    $method = $_GET['method'];
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie WHERE cotis.COSU = 0 AND cotis.methode = :methode ORDER BY cotis.DCRE DESC;"); //Liste des cotisations
                                    $req->bindValue('methode', $method);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                elseif (isset($_GET['state']) && !empty($_GET['state']) && $_GET['state'] != '') :
                                    $state = $_GET['state'];
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie WHERE cotis.COSU = 0 AND cotis.etat = :etat ORDER BY cotis.DCRE DESC;"); //Liste des cotisations
                                    $req->bindValue('etat', $state);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                else :
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie WHERE cotis.COSU = 0 ORDER BY cotis.DCRE DESC;"); //Liste des cotisations
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                endif;
                            elseif (is_educ()) :
                                if (isset($_GET['q']) && !empty($_GET['q'])) :
                                    $q_ = explode(' ', $_GET['q']); //take only the first word
                                    $q = $q_[0];
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie WHERE cotis.COSU = 0 AND categorieeduc.idEduc = :idEduc AND categorie.nomCategorie = :categorie AND licencie.nom LIKE '%$q%' ORDER BY cotis.DCRE DESC;"); //Liste des cotisations selon les catégories de l'educateur
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                elseif (isset($_GET['method']) && !empty($_GET['method']) && $_GET['method'] != '') :
                                    $method = $_GET['method'];
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie WHERE cotis.COSU = 0 AND cotis.methode = :methode AND categorieeduc.idEduc = :idEduc ORDER BY cotis.DCRE DESC;"); //Liste des cotisations
                                    $req->bindValue('methode', $method);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                elseif (isset($_GET['state']) && !empty($_GET['state']) && $_GET['state'] != '') :
                                    $state = $_GET['state'];
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie WHERE cotis.COSU = 0 AND categorieeduc.idEduc = :idEduc AND cotis.etat = :etat ORDER BY cotis.DCRE DESC;"); //Liste des cotisations selon les catégories de l'educateur
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->bindValue('etat', $state);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                else :
                                    $req = $db->prepare("SELECT cotis.idCotis, cotis.methode, cotis.prix, cotis.type, cotis.etat, licencie.prenom, licencie.nom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie WHERE cotis.COSU = 0 AND categorieeduc.idEduc = :idEduc ORDER BY cotis.DCRE DESC;"); //Liste des cotisations selon les catégories de l'educateur
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->execute();
                                    $rowCount = $req->rowCount();
                                endif;
                            endif;
                            if ($rowCount > 0) : //if there is most than one cotisation
                            ?>
                                <div class="cotisations-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Prix</th>
                                                <th>Méthode</th>
                                                <th>Licencié</th>
                                                <th>Etat</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php while ($COTIS = $req->fetch(PDO::FETCH_ASSOC)) : ?>
                                                <tr>
                                                    <td>
                                                        <?= $COTIS["type"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $COTIS["prix"] ?> €
                                                    </td>
                                                    <td>
                                                        <form action="./functions/cotis-state.php?idCotis=<?= $COTIS['idCotis']; ?>" method="POST">
                                                            <select name="methode" onchange="submit()">
                                                                <?php if (!isset($COTIS['methode'])) : ?>
                                                                    <option disabled selected>Méthode</option>
                                                                    <option value="1">Chèque</option>
                                                                    <option value="2">Espèce</option>
                                                                    <option value="3">CB</option>
                                                                <?php else : ?>
                                                                    <option <?php if (isset($COTIS['methode']) && $COTIS['methode'] == 1) : ?> selected <?php endif; ?> value="1">Chèque</option>
                                                                    <option <?php if (isset($COTIS['methode']) && $COTIS['methode'] == 2) : ?> selected <?php endif; ?> value="2">Espèce</option>
                                                                    <option <?php if (isset($COTIS['methode']) && $COTIS['methode'] == 3) : ?> selected <?php endif; ?> value="3">CB</option>
                                                                <?php endif; ?>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <?= htmlspecialchars($COTIS["nom"]) . " " . htmlspecialchars($COTIS["prenom"]) ?>
                                                    </td>
                                                    <td>
                                                        <form action="./functions/cotis-state.php?idCotis=<?= $COTIS['idCotis']; ?>" method="POST">
                                                            <select name="etat" onchange="submit()">
                                                                <option <?php if ($COTIS['etat'] == 1) : ?> selected <?php endif; ?> value="1">Non réglée</option>
                                                                <option <?php if ($COTIS['etat'] == 2) : ?> selected <?php endif; ?> value="2">Réglée</option>
                                                                <option <?php if ($COTIS['etat'] == 3) : ?> selected <?php endif; ?> value="3">Non encaissée</option>
                                                                <option <?php if ($COTIS['etat'] == 4) : ?> selected <?php endif; ?> value="4">Encaissée</option>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td></td>
                                                    <td class="action-btns btns-2">
                                                        <a href="">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <p> Aucune cotisation n'a encore été récupérée. </p>
                            <?php endif;
                            ?>
                        </div>
                        <!-- <div class="return deconnect">
                            <a href="index.php">Retour</a>
                        </div> -->
                    </div>

                </div>
            </div>
            <script type="text/javascript" src="./public/js/tableau.js"></script>
            <script type="text/javascript" src="./public/js/modal.js"></script>
        <?php else :
            create_flash_message(ERROR_PSWD, "Vous ne possédez pas les droits.", FLASH_ERROR); //the user is not admin or educ
            header("location: ./index.php");
            exit();
        endif;
        ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>