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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js"></script>
</head>

<body>
  <?php if (is_logged()) : ?>
    <div class="content">
      <?php include('./components/header.php'); ?>
      <div class="container">
        <div class="container-content">
          <?php include "./components/display_error.php"; ?>
          <div class="head-admin">
            <h1>Bonjour <?= htmlspecialchars($_SESSION['prenom']); ?>,
              <?php if (is_admin()) : ?>
                <p>Tu es sur l'espace d'administration</p>
              <?php else : ?>
                <p>Tu es sur l'espace éducateur</p>
              <?php endif; ?>
            </h1>
          </div>
          <div class="head-separator"></div>
          <div class="admin-panel">
            <?php if (is_admin()) : ?>
              <!-- Afficher ce bouton que si l'utilisateur est admin  -->
              <div class="admin-button-hover">
                <a href="./add-educ.php">
                  <i class="fa fa-plus"></i>
                  <p>Ajouter un &eacute;ducateur</p>
                </a>
              </div>
            <?php endif; ?>
            <div class="admin-button-hover">
              <a href="./add-licencie.php">
                <i class="fa fa-user-plus"></i>
                <p>Ajouter un licenci&eacute;</p>
              </a>
            </div>
            <div class="admin-button-hover">
              <a href="./statistiques.php">
                <i class="fa fa-bar-chart"></i>
                <p>Statistiques de la saison</p>
              </a>
            </div>
            <div class="admin-button-hover">
              <a href="./compte.php">
                <i class="fa fa-cogs"></i>
                <p>Mon compte</p>
              </a>
            </div>
            <?php if (is_admin()) : ?>
              <!-- Afficher ce bouton que si l'utilisateur est admin  -->
              <div class="admin-button-hover">
                <a href="./educateurs.php">
                  <i class="fa fa-user"></i>
                  <p>Gestion des &eacute;ducateurs</p>
                </a>
              </div>
            <?php endif; ?>
            <div class="admin-button-hover">
              <a href="./licencies.php">
                <i class="fa fa-users"></i>
                <p>Gestion des licenci&eacute;s</p>
              </a>
            </div>
            <div class="admin-button-hover">
              <a href="./cotisations.php">
                <i class="fa fa-euro-sign"></i>
                <p>Suivi des cotisations</p>
              </a>
            </div>
            <div class="admin-button-hover">
              <a href="./attestation-generator.php">
                <i class="fa fa-file-invoice"></i>
                <p>G&eacute;n&eacute;rer une attestation</p>
              </a>
            </div>
          </div>
          <div class="admin-panel-separator"></div>
          <div class="panel-down">
            <div class="chartJS">
              <h2>Aperçu du suivi des cotisations :</h2>
              <?php
              if (is_admin()) :
                //S'il est admin, il est associé a des catégories
                $has_category = true;

                $cotis = $db->query("SELECT * FROM cotis WHERE cotis.COSU = 0");
                //Verification si des cotisations existe 
                if ($cotis->rowCount() > 0) :
                  //La requête envoie des lignes
                  $cotis_exist = true;
                else :
                  $cotis_exist = false;
                endif;
                //Ferme le curseur, permettant à la requête d'être de nouveau exécutée
                $cotis->closeCursor();
                if ($cotis_exist) :
                  //si des cotis existent afficher le camenbert 
              ?>
                  <div>
                    <canvas id="myChart" width="975" height="160"></canvas>
                  </div>
                <?php else : ?>
                  <p>Aucune cotisation n'a encore été récupérée.</p>
                <?php endif; ?>
                <?php elseif (is_educ()) :
                //S'il est éducateur verifier si il est associé à des catégories
                $get_cat = $db->prepare('CALL PRC_GETEDUCAT(?)');
                $get_cat->bindValue(1, $_SESSION['id']);
                $get_cat->execute();
                if ($get_cat->rowCount()) {
                  //il posséde des catégories
                  $has_category = true;
                } else {
                  //il n'en posséde pas, et donc aucune cotisation existe
                  $has_category = false;
                  $cotis_exist = false;
                }
                $get_cat->closeCursor();
                //Ferme le curseur, permettant à la requête d'être de nouveau exécutée
                if ($has_category) :
                  //S'il a des catégories, récupérer les cotisations de sa/ses catégorie(s)
                  $cotis = $db->prepare("SELECT DISTINCT categorie.nomCategorie FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE cotis.COSU = 0 AND categorieeduc.idEduc = :idEduc;");
                  $cotis->bindValue(':idEduc', $_SESSION['id']);
                  $cotis->execute();
                  if ($cotis->rowCount() > 0) :
                    // des cotisations existent dans sa/ses catégorie(s)
                    $cotis_exist = true;
                  else :
                    $cotis_exist = false;
                  endif;
                  $cotis->closeCursor();
                  //Ferme le curseur, permettant à la requête d'être de nouveau exécutée
                  if ($cotis_exist) :
                    //si des cotisations existent : afficher le camembert
                ?>
                    <div>
                      <canvas id="myChart" width="975" height="160"></canvas>
                    </div>
                  <?php else : ?>
                    <p>Aucune cotisation dans vos catégories.</p>
                  <?php endif;
                else :
                  $has_category = false; ?>
                  <p>Vous n'appartenez à aucune catégorie.</p>
              <?php endif;
              endif; ?>
            </div>
            <div class="li-admin">
              <h2>
                Derniers licenciés ajoutés :
              </h2>
              <?php
              //Appel de la procédure pour lister les 10 derniers licenciés
              $req = $db->prepare("CALL PRC_TENLIC()");
              $req->execute();
              $rowCount = $req->rowCount(); //Compte le nom de ligne retourné par la requête 
              if ($rowCount > 0) : //S'il y a une ligne donc des licenciés, afficher la liste
              ?>
                <ul>
                  <?php while ($LIC = $req->fetch(PDO::FETCH_ASSOC)) : //Tant qu'il y a des lignes d'informations : les afficher
                  ?>
                    <li>
                      <?php
                      //Verifier l'etat de la cotisation du licencié afin de déterminer la couleur et le titre
                      if ($LIC["etat"] == 1) : ?>
                        <span title="non réglée" class="state-indicator" style="background-color: red;"></span>
                      <?php elseif ($LIC["etat"] == 2) : ?>
                        <span title="réglée" class="state-indicator" style="background-color: orange;"></span>
                      <?php elseif ($LIC["etat"] == 3) : ?>
                        <span title="non encaissée" class="state-indicator" style="background-color: white; border: 1px solid green;"></span>
                      <?php elseif ($LIC["etat"] == 4) : ?>
                        <span title="encaissée" class="state-indicator" style="background-color: green;"></span>
                      <?php endif;
                      //Afficher les infomations du licencié ajoutés tout en échappant les données 
                      ?>
                      <p><?= htmlspecialchars($LIC["nomCategorie"]) ?> - <a href="./profil-licencie.php?idLicencie=<?= $LIC['idLicencie']; ?>"><?= htmlspecialchars($LIC["prenom"]) . " " . strtoupper(htmlspecialchars($LIC["nom"])) ?></a>
                        <?php if (isset($LIC["USRCRE"])) : ?>par <span><?= htmlspecialchars($LIC["USRCRE"]) ?> </span></p> <?php endif; ?>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else :
                // il n'y a pas d'informations 
              ?>
                <p> Aucun licencié n'a encore été créé </p>
              <?php endif;
              //Ferme le curseur, permettant à la requête d'être de nouveau exécutée
              $req->closeCursor(); ?>
            </div>
          </div>

          <?php
          //si des cotisations existent et que l'admin ou l'educ sont associés à des catégories : inialisation du camembert
          if ($cotis_exist && $has_category) :
            if (is_admin()) :
              //s'il est admin, compter les cotis des différents états et récupérer leurs valeurs
              $get_cotis_r = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 1");
              $nb_cotis_r = $get_cotis_r->fetch(PDO::FETCH_BOTH);
              $get_cotis_nr = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 2");
              $nb_cotis_nr = $get_cotis_nr->fetch(PDO::FETCH_BOTH);
              $get_cotis_ne = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 3");
              $nb_cotis_ne = $get_cotis_ne->fetch(PDO::FETCH_BOTH);
              $get_cotis_e = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 4");
              $nb_cotis_e = $get_cotis_e->fetch(PDO::FETCH_BOTH);
            elseif (is_educ()) :
              //s'il est educ, compter les cotis des différents états selon sa/ses catégorie(s) et récupérer leurs valeurs
              $get_cotis_nr = $db->prepare("SELECT COUNT(*) FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE cotis.COSU = 0 AND cotis.etat = 1 AND categorieeduc.idEduc = :idEduc");
              $get_cotis_nr->bindValue('idEduc', $_SESSION['id']);
              $get_cotis_nr->execute();
              $nb_cotis_nr = $get_cotis_nr->fetch(PDO::FETCH_BOTH);
              $get_cotis_r = $db->prepare("SELECT COUNT(*) FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE cotis.COSU = 0 AND cotis.etat = 2 AND categorieeduc.idEduc = :idEduc");
              $get_cotis_r->bindValue('idEduc', $_SESSION['id']);
              $get_cotis_r->execute();
              $nb_cotis_r = $get_cotis_r->fetch(PDO::FETCH_BOTH);
              $get_cotis_ne = $db->prepare("SELECT COUNT(*) FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE cotis.COSU = 0 AND cotis.etat = 3 AND categorieeduc.idEduc = :idEduc");
              $get_cotis_ne->bindValue('idEduc', $_SESSION['id']);
              $get_cotis_ne->execute();
              $nb_cotis_ne = $get_cotis_ne->fetch(PDO::FETCH_BOTH);
              $get_cotis_e = $db->prepare("SELECT COUNT(*) FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE cotis.COSU = 0 AND cotis.etat = 4 AND categorieeduc.idEduc = :idEduc");
              $get_cotis_e->bindValue('idEduc', $_SESSION['id']);
              $get_cotis_e->execute();
              $nb_cotis_e = $get_cotis_e->fetch(PDO::FETCH_BOTH);
            endif;
            //data a insérer dans le camembert :  le nombre de cotisations non reçues, le nombre de cotisations reçues
            $data = [intval($nb_cotis_r[0]), intval($nb_cotis_nr[0]) + intval($nb_cotis_ne[0]) + intval($nb_cotis_e[0])];
          ?>

            <script>
              $(document).ready(function() {
                (Chart.defaults.font.size = 14),
                (Chart.defaults.font.family = "Montserrat");
                const F = document.getElementById("myChart").getContext("2d");
                new Chart(F, {
                  type: "pie",
                  data: {
                    labels: ["Cotisations non reçues", "Cotisations reçues"],
                    datasets: [{
                      tooltip: {
                        callbacks: {
                          label: function(data) {
                            // calcul du pourcentage, valeur de la données / somme des données * 100
                            const percentage = (data.parsed / data.dataset.data.reduce((a, b) => a + b, 0)) * 100
                            return `${data.label ?? ''} : ${percentage.toFixed(0)}%`;
                          },
                        },
                      },
                      label: "Aperçu du suivi des cotisations",
                      data: <?= json_encode($data); ?>,
                      backgroundColor: ["#dfdfdf", "rgb(47 191 47 / 78%)"],
                      borderColor: ["rgba(255, 255, 255, 1)", "rgba(255, 255, 255, 1)"],
                      borderWidth: 3,
                      hoverOffset: 20,
                      hoverBorderWidth: 0,
                    }, ],
                  },
                  options: {
                    // responsive: !0,
                    // maintainAspectRatio: !1,
                    layout: {
                      padding: 25
                    },
                    plugins: {
                      legend: {
                        position: "bottom",
                        labels: {
                          padding: 20
                        }
                      },
                    }
                  },
                });
              });
            </script>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php require './components/footer.php'; ?>
    <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>