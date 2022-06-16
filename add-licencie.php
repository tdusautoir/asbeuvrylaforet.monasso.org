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
  <title>Espace Ajout Licencié - A.S. BEUVRY LA FORÊT</title>
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
              Derniers licenciés ajoutés :
            </h2>
            <?php
            $req = $db->prepare("CALL PRC_TENLIC()"); //Derniers licenciés ajoutés classé par date croissant et limités à 10. 
            $req->execute();
            $rowCount = $req->rowCount();
            if ($rowCount > 0) : //si on trouve des licenciés ajoutés on affiche la liste de la requete.
            ?>
              <ul>
                <?php while ($LIC = $req->fetch(PDO::FETCH_ASSOC)) : ?>
                  <li>
                    <p><?= $LIC["nomCategorie"] ?> - <span><?= $LIC["prenom"] . " " . strtoupper($LIC["nom"]) ?></span>
                      <?php if (isset($LIC["USRCRE"])) : ?>par <span><?= $LIC["USRCRE"]; ?> </span></p> <?php endif; ?>
                  </li>
                <?php endwhile; ?>
              </ul>
            <?php else : ?>
              <p> Aucun licencié n'a encore été créé </p>
            <?php endif;
            $req->closeCursor(); ?>
            <div class="add-panel-separator"></div>
          </div>
          <div class="add-panel">
            <h1>
              Ajouter un licencié
            </h1>
            <form action="./functions/licencie-add.php" method="POST">
              <div class="form-add">
                <input type="text" class="nom-licencie" placeholder="Nom" name="nom-licencie" maxlength="20">
                <input type="text" class="prenom-licencie" placeholder="Prénom" name="prenom-licencie" maxlength="15">
              </div>
              <div class="form-add">
                <label for="photo-licencie">
                  <i class="fa fa-picture-o"></i> Photo
                  <input id="photo-licencie" type="file" accept="image/png, image/jpeg" />
                  <span id="nom-photo-licencie"></span>
                </label>
                <input type="date" placeholder="Date de naissance" name="dateN-licencie">
              </div>
              <div class="form-add">
                <select name="categorie-licencie" id="categorie-licencie">
                  <option value="" disabled selected>Catégorie</option>
                  <?php
                  $req_category = $db->query("SELECT idCategorie, nomCategorie FROM categorie");
                  while ($category = $req_category->fetch()) :
                    if (isset($category)) :
                  ?>
                      <option value="<?= $category["idCategorie"] ?>"><?= $category["nomCategorie"] ?></option>
                  <?php
                    endif;
                  endwhile;
                  $req_category->closeCursor();
                  ?>
                </select>
                <select name="sexe-licencie" id="sexe-licencie">
                  <option value="" disabled selected>Sexe</option>
                  <option value="m">Homme</option>
                  <option value="f">Femme</option>
                </select>
              </div>
              <div class="mail-form-add">
                <input type="mail" class="mail-licencie" name="mail-licencie" placeholder="Adresse mail" maxlength="40">
              </div>
              <div class="form-add">
                <input type="submit" value="Ajouter" name="submit" class="bouton-ajouter">
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
    <?php else : require "./components/logged.php"; ?><?php endif; ?>
</body>

</html>