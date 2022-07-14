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
    <title>Statistiques - A.S. BEUVRY LA FORÊT</title>
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
                                        //recuperer toutes les catégories
                                        $getCategorie = $db->query("SELECT DISTINCT nomCategorie FROM categorie WHERE COSU = 0");
                                        if ($getCategorie->rowCount() > 0) :
                                            while ($categorie = $getCategorie->fetch()) : ?>
                                                <option value="<?= $categorie['nomCategorie']; ?>" <?php if (isset($_GET['categorie']) && $categorie['nomCategorie'] == $_GET['categorie']) : ?>selected<?php endif; ?>><?= $categorie['nomCategorie']; ?></option>
                                            <?php endwhile;
                                        endif;
                                    elseif (is_educ()) :
                                        //recuperer les catégories de l'educateurs
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

                    <?php
                    //s'il y a présence de filtre dans l'url, afficher le bouton
                    if (isset($_GET) && !empty($_GET)) : ?>
                        <a href="./statistiques.php" class="cancel-filter">Annuler les filtres</a>
                    <?php endif; ?>

                    <div class="stats-container">
                        <div class="stats-content">
                            <h2>
                                &#127942; Les statistiques de la saison :
                            </h2>
                            <?php
                            if (is_admin()) :
                                //faire les requêtes selon les filtres
                                if (isset($_GET['q']) && !empty($_GET['q'])) :
                                    $q_ = explode(' ', $_GET['q']); //prendre seulement le premier mot ecrit pour éviter les injections sql
                                    $q = $q_[0];
                                    //requete pour récuperer les infos des statistiques selon la recherche
                                    $req = $db->prepare("SELECT categorie.nomCategorie, licencie.nom, licencie.prenom, statistiques.idStat, statistiques.nbButs, statistiques.passeD FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 AND licencie.nom LIKE '%$q%' ORDER BY licencie.DCRE DESC;"); //statistiques de la bdd selon la recherche q
                                    $req->execute();
                                    $rowCount = $req->rowCount();

                                    //récupérer les meilleurs joueurs
                                    $getTopScorer = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.nbButs, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY statistiques.nbButs DESC LIMIT 1;"); //Top scorer
                                    $getTopScorer->execute();
                                    $getTopAssister = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.passeD, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY statistiques.passeD DESC LIMIT 1;"); //Top assister
                                    $getTopAssister->execute();
                                elseif (isset($_GET['categorie']) && !empty($_GET['categorie']) && $_GET['categorie'] != '') :
                                    $categorie = $_GET['categorie'];
                                    //requete pour récuperer les infos des statistiques selon la catégorie
                                    $req = $db->prepare("SELECT categorie.nomCategorie, licencie.prenom, licencie.nom, statistiques.idStat, statistiques.nbButs, statistiques.passeD FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 AND categorie.nomCategorie = :categorie ORDER BY licencie.DCRE DESC;"); //statistiques de la bdd selon la categorie
                                    $req->bindValue('categorie', $categorie);
                                    $req->execute();
                                    $rowCount = $req->rowCount();

                                    //récupérer les meilleurs joueurs
                                    $getTopScorer = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.nbButs, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 AND categorie.nomCategorie = :categorie ORDER BY statistiques.nbButs DESC LIMIT 1;"); //Top scorer
                                    $getTopScorer->bindValue('categorie', $categorie);
                                    $getTopScorer->execute();
                                    $getTopAssister = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.passeD, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 AND categorie.nomCategorie = :categorie ORDER BY statistiques.passeD DESC LIMIT 1;"); //Top assister
                                    $getTopAssister->bindValue('categorie', $categorie);
                                    $getTopAssister->execute();
                                else :
                                    //requete pour récuperer les infos des statistiques 
                                    $req = $db->prepare("SELECT categorie.nomCategorie, licencie.nom, licencie.prenom, statistiques.idStat, statistiques.nbButs, statistiques.passeD FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY licencie.DCRE DESC;"); //statistiques de la bdd classé par date croissant 
                                    $req->execute();
                                    $rowCount = $req->rowCount();

                                    //récupérer les meilleurs joueurs
                                    $getTopScorer = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.nbButs, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY statistiques.nbButs DESC LIMIT 1;"); //Top scorer
                                    $getTopScorer->execute();
                                    $getTopAssister = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.passeD, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY statistiques.passeD DESC LIMIT 1;"); //Top assister
                                    $getTopAssister->execute();
                                endif;
                            elseif (is_educ()) :
                                //faire les requêtes selon les filtres et ses catégories
                                if (isset($_GET['q']) && !empty($_GET['q'])) :
                                    $q_ = explode(' ', $_GET['q']); //prendre seulement le premier mot ecrit pour éviter les injections sql
                                    $q = $q_[0];
                                    //requete pour récuperer les infos des statistiques selon la recherche q
                                    $req = $db->prepare("SELECT categorie.nomCategorie, licencie.prenom, licencie.nom, statistiques.idStat, statistiques.nbButs, statistiques.passeD FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND licencie.nom LIKE '%$q%' ORDER BY licencie.DCRE DESC;"); //statistiques de la bdd selon la recherche q et les catégories associés à l'educateur connecté
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->execute();
                                    $rowCount = $req->rowCount();

                                    //récupérer les meilleurs joueurs
                                    $getTopScorer = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.nbButs, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc ORDER BY statistiques.nbButs DESC LIMIT 1;"); //Top scorer
                                    $getTopScorer->bindValue('idEduc', $_SESSION['id']);
                                    $getTopScorer->execute();
                                    $getTopAssister = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.passeD, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc ORDER BY statistiques.passeD DESC LIMIT 1;"); //Top assister
                                    $getTopAssister->bindValue('idEduc', $_SESSION['id']);
                                    $getTopAssister->execute();
                                elseif (isset($_GET['categorie']) && !empty($_GET['categorie']) && $_GET['categorie'] != '') :
                                    $categorie = $_GET['categorie'];
                                    //requete pour récuperer les infos des statistiques selon la categorie
                                    $req = $db->prepare("SELECT categorie.nomCategorie, licencie.prenom, licencie.nom, statistiques.idStat, statistiques.nbButs, statistiques.passeD  FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND categorie.nomCategorie = :categorie ORDER BY licencie.DCRE DESC;"); //statistiques de la bdd selon la recherche q et les catégories associés à l'educateur connecté
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->bindValue('categorie', $categorie);
                                    $req->execute();
                                    $rowCount = $req->rowCount();

                                    //récupérer les meilleurs joueurs
                                    $getTopScorer = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.nbButs, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND categorie.nomCategorie = :categorie ORDER BY statistiques.nbButs DESC LIMIT 1;"); //Top scorer
                                    $getTopScorer->bindValue('idEduc', $_SESSION['id']);
                                    $getTopScorer->bindValue('categorie', $categorie);
                                    $getTopScorer->execute();
                                    $getTopAssister = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.passeD, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND categorie.nomCategorie = :categorie ORDER BY statistiques.passeD DESC LIMIT 1;"); //Top assister
                                    $getTopAssister->bindValue('idEduc', $_SESSION['id']);
                                    $getTopAssister->bindValue('categorie', $categorie);
                                    $getTopAssister->execute();
                                else :
                                    //requete pour récuperer les infos des statistiques
                                    $req = $db->prepare("SELECT categorie.nomCategorie, licencie.prenom, licencie.nom, statistiques.idStat, statistiques.nbButs, statistiques.passeD FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc ORDER BY licencie.DCRE DESC;"); //statistiques de la bdd selon les catégories associés à l'educateur connecté 
                                    $req->bindValue('idEduc', $_SESSION['id']);
                                    $req->execute();
                                    $rowCount = $req->rowCount();

                                    //récupérer les meilleurs joueurs
                                    $getTopScorer = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.nbButs, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc ORDER BY statistiques.nbButs DESC LIMIT 1;"); //Top scorer
                                    $getTopScorer->bindValue('idEduc', $_SESSION['id']);
                                    $getTopScorer->execute();
                                    $getTopAssister = $db->prepare("SELECT DISTINCT licencie.nom, licencie.prenom, statistiques.passeD, categorie.nomCategorie FROM `licencie` INNER JOIN statistiques ON licencie.idLicencie = statistiques.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc ORDER BY statistiques.passeD DESC LIMIT 1;"); //Top assister
                                    $getTopAssister->bindValue('idEduc', $_SESSION['id']);
                                    $getTopAssister->execute();
                                endif;
                            endif;

                            if ($rowCount > 0) : //si on trouve des statistiques ajoutés on affiche la liste de la requete. 
                            ?>
                                <div class="tab-and-scoreboard">
                                    <?php if (isset($getTopScorer) && isset($getTopAssister)) : //verifier si les meilleurs joueurs sont définies 
                                        if ($getTopScorer->rowCount() > 0 && $getTopAssister->rowCount() > 0) :  ?>
                                            <!-- Si on trouve des infos, afficher le meilleur buteur et le meilleur passeur -->
                                            <div class="container-scoreboard">
                                                <div class="scoreboard">
                                                    <h3>Meilleur buteur :</h3>
                                                    <?php $TopScorer = $getTopScorer->fetch(PDO::FETCH_ASSOC); ?>
                                                    <p>&#x1F947;<strong><?= $TopScorer['nomCategorie'] ?></strong> - <?= htmlspecialchars($TopScorer['nom']) ?> <?= htmlspecialchars($TopScorer['prenom']) ?> (<?= $TopScorer['nbButs'] ?> buts)</p>
                                                    <h3>Meilleur passeur :</h3>
                                                    <?php $TopAssister = $getTopAssister->fetch(PDO::FETCH_ASSOC); ?>
                                                    <p>&#x1F947;<strong><?= $TopAssister['nomCategorie'] ?></strong> - <?= htmlspecialchars($TopAssister['nom']) ?> <?= htmlspecialchars($TopAssister['prenom']) ?> (<?= $TopAssister['passeD'] ?> passes décisives)
                                                    <p>
                                                </div>
                                                <div class="download-csv">
                                                    <a href="./functions/stats-csv.php">Télécharger (.csv)</a><a href="./functions/stats-reset.php">Réinitialiser</a>
                                                </div>
                                            </div>
                                    <?php endif;
                                    endif; ?>
                                    <div class="statistiques-tab">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Nom</th>
                                                    <th>Prénom</th>
                                                    <th>Buts</th>
                                                    <th>PD</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php while ($STATS = $req->fetch(PDO::FETCH_ASSOC)) :
                                                    //Afficher toutes les données selon la requete 
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?= $STATS["nomCategorie"] ?>
                                                        </td>
                                                        <td title=<?= strtoupper(htmlspecialchars($STATS["nom"])) ?>>
                                                            <?= strtoupper(htmlspecialchars($STATS["nom"])) ?>
                                                        </td>
                                                        <td title=<?= ucfirst(htmlspecialchars($STATS["prenom"])) ?>>
                                                            <?= ucfirst(htmlspecialchars($STATS["prenom"])) ?>
                                                        </td>
                                                        <td>
                                                            <form action="./functions/stats-change.php?idStat=<?= $STATS['idStat'] ?>" method="POST">
                                                                <button type="submit" name="add-goal" value="1" class="stat-btn"><i class="fa fa-plus"></i></button>
                                                                <?= $STATS["nbButs"] ?>
                                                                <?php if ($STATS["nbButs"] > 0) : ?>
                                                                    <button type="submit" name="remove-goal" value="1" class="stat-btn"><i class="fa fa-minus"></i></button>
                                                                <?php endif; ?>
                                                            </form>
                                                        </td>
                                                        <td>
                                                            <form action="./functions/stats-change.php?idStat=<?= $STATS['idStat'] ?>" method="POST">
                                                                <button type="submit" name="add-pd" value="1" class="stat-btn"><i class="fa fa-plus"></i></button>
                                                                <?= $STATS["passeD"] ?>
                                                                <?php if ($STATS["passeD"] > 0) : ?>
                                                                    <button type="submit" name="remove-pd" value="1" class="stat-btn"><i class="fa fa-minus"></i></button>
                                                                <?php endif; ?>
                                                            </form>
                                                        </td>
                                                        </form>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php elseif (isset($_GET) && !empty($_GET)) : //aucune données trouvés alors que des filtres sont définies
                            ?>
                                <p> Aucun licencié ne correspond à votre recherche. </p>
                            <?php else : ?>
                                <p> Aucune statistique enregistrée. </p>
                            <?php endif;
                            $req->closeCursor(); // Ferme le curseur, permettant à la requête d'être de nouveau exécutée 
                            ?>
                        </div>
                    </div>
                    <!-- <div class="deconnect">
                    <a href="index.php">Retour</a>
                </div> -->
                </div>
            </div>
        </div>
        <?php require "./components/footer.php"; ?>
        <script type="text/javascript" src="./public/js/tableau.js"></script>
    <?php else : require "./components/form_login.php"; ?>

    <?php endif; ?>
</body>

</html>