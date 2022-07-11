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
            <a href="./statistiques.php">
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
            <a href="./cotisations.php">
              <i class="fa fa-euro-sign"></i>
              <p>Suivi des cotisations</p>
            </a>
            <a href="./attestation-generator.php">
              <i class="fa fa-file-invoice"></i>
              <p>G&eacute;n&eacute;rer une attestation</p>
            </a>
          </div>
          <div class="admin-panel-separator"></div>
          <section class="panel-down">
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
                      <p><?= htmlspecialchars($LIC["nomCategorie"]) ?> - <span><?= htmlspecialchars($LIC["prenom"]) . " " . strtoupper(htmlspecialchars($LIC["nom"])) ?></span>
                        <?php if (isset($LIC["USRCRE"])) : ?>par <span><?= htmlspecialchars($LIC["USRCRE"]) ?> </span></p> <?php endif; ?>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else : ?>
                <p> Aucun licencié n'a encore été créé </p>
              <?php endif;
              $req->closeCursor(); ?>
            </div>
            <div class="chartJS">
              <h2>Aperçu du suivi des cotisations :</h2>
              <div>
                <canvas id="myChart" width="975" height="160"></canvas>
              </div>
            </div>
          </section>
          <!-- <div class="deconnect">
          <a href="index.php?action=logout">Deconnexion</a>
        </div> -->

          <?php $get_cotis_r = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 1");
          $nb_cotis_r = $get_cotis_r->fetch(PDO::FETCH_BOTH);
          $get_cotis_nr = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 2");
          $nb_cotis_nr = $get_cotis_nr->fetch(PDO::FETCH_BOTH);
          $get_cotis_ne = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 3");
          $nb_cotis_ne = $get_cotis_ne->fetch(PDO::FETCH_BOTH);
          $get_cotis_e = $db->query("SELECT COUNT(*) FROM cotis WHERE cotis.COSU = 0 AND cotis.etat = 4");
          $nb_cotis_e = $get_cotis_e->fetch(PDO::FETCH_BOTH);
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
                          const percentage = (data.parsed / data.dataset.data.reduce((a, b) => a + b, 0)) * 100
                          return `${data.label ?? ''} : ${percentage.toFixed(0)}%`;
                        },
                      },
                    },
                    label: "Aperçu du suivi des cotisations",
                    data: <?= json_encode($data); ?>,
                    backgroundColor: ["rgb(235, 52, 52)", "rgb(235, 110, 52)", "rgba(81, 190, 132, 1)"],
                    borderColor: ["rgba(255, 255, 255, 1)", "rgba(255, 255, 255, 1)", "rgba(255, 255, 255, 1)"],
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
        </div>
      </div>
    </div>
    <?php require './components/footer.php'; ?>
    <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>