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
  <title>Espace Admin - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
  <?php if (is_logged()) : ?>
    <?php include('./components/header.php'); ?>
    <div class="container">
      <div class="container-content">
        <div class="welcome-admin">
          <h1>Bonjour <?= htmlspecialchars($_SESSION['prenom']); ?>,
            <p>Tu es sur l'espace d'administration</p>
          </h1>
        </div>
        <div class="welcome-separator"></div>
        <div class="admin-panel">
          <a>
            <i class="fa fa-plus"></i>
            <p>Ajouter un &eacute;ducateur</p>
          </a>
          <a href="./add-licencie.php">
            <i class="fa fa-user-plus"></i>
            <p>Ajouter un licenci&eacute;</p>
          </a>
          <a>
            <i class="fa fa-bar-chart"></i>
            <p>Statistiques de la saison</p>
          </a>
          <a>
            <i class="fa fa-cogs"></i>
            <p>Mon compte</p>
          </a>
          <a>
            <i class="fa fa-user"></i>
            <p>Gestion des &eacute;ducateurs</p>
          </a>
          <a href="./licencies.php">
            <i class="fa fa-users"></i>
            <p>Gestion des licenci&eacute;s</p>
          </a>
          <a>
            <i class="fa fa-euro-sign"></i>
            <p>Suivi des cotisations</p>
          </a>
          <a>
            <i class="fa fa-file-invoice"></i>
            <p>G&eacute;n&eacute;rer une attestation</p>
          </a>
        </div>
        <div class="admin-panel-separator"></div>
        <div class="li-admin">
          <h2>
            Derniers licenciés ajoutés :
          </h2>
          <?php
          $req = $db->prepare("SELECT categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.USRCRE  FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY licencie.DCRE DESC LIMIT 10"); //Derniers licenciés ajoutés classé par date croissant et limités à 10. 
          $req->execute();
          $rowCount = $req->rowCount();
          if ($rowCount > 0) : //si on trouve des licenciés ajoutés on affiche la liste de la requete.
          ?>
            <ul>
              <?php while ($LIC = $req->fetch(PDO::FETCH_ASSOC)) : ?>
                <li>
                  <p><?= $LIC["nomCategorie"] ?> - <span><?= $LIC["prenom"] . " " . strtoupper($LIC["nom"]) ?></span>
                    <?php if (isset($LIC["USRCRE"])) : ?>par <span><?= ($LIC["USRCRE"]) ?> </span></p> <?php endif; ?>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php else : ?>
            <p> Aucun licencié n'a encore été créé </p>
          <?php endif; ?>
        </div>
        <div class="deconnect">
          <a href="index.php?action=logout">Deconnexion</a>
        </div>
      </div>
    </div>
    <?php else : require "./components/logged.php"; ?><?php endif; ?>
</body>

</html>