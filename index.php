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
        <?php include "./components/display_error.php"; ?>
        <div class="welcome-admin">
          <h1>Bonjour <?= htmlspecialchars($_SESSION['prenom']); ?>,
            <?php if (is_admin()) : ?>
              <p>Tu es sur l'espace d'administration</p>
            <?php else : ?>
              <p>Tu es sur l'espace éducateur</p>
            <?php endif; ?>
          </h1>
        </div>
        <div class="welcome-separator"></div>
        <div class="admin-panel">
          <?php if (is_admin()) : ?>
            <a href="./add-educ.php">
              <i class="fa fa-plus"></i>
              <p>Ajouter un &eacute;ducateur</p>
            </a>
          <?php endif; ?>
          <a href="./add-licencie.php">
            <i class="fa fa-user-plus"></i>
            <p>Ajouter un licenci&eacute;</p>
          </a>
          <a>
            <i class="fa fa-bar-chart"></i>
            <p>Statistiques de la saison</p>
          </a>
          <a href="./compte.php">
            <i class="fa fa-cogs"></i>
            <p>Mon compte</p>
          </a>
          <?php if (is_admin()) : ?>
            <a href="./educateurs.php">
              <i class="fa fa-user"></i>
              <p>Gestion des &eacute;ducateurs</p>
            </a>
          <?php endif; ?>
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
          $req = $db->prepare("CALL PRC_TENLIC()");
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
          <?php endif;
          $req->closeCursor(); ?>
        </div>
        <div class="deconnect">
          <a href="index.php?action=logout">Deconnexion</a>
        </div>
      </div>
    </div>
    <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>