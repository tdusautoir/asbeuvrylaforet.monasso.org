<?php
session_start();

require("./function.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
  clean_php_session();
  header("location: index.php");
}

//gestion des erreurs
if (isset_flash_message_by_type(FLASH_ERROR)) {
  if (isset_flash_message_by_name("form_lastname_error")) {
    $form_lastname_error = true;
  } else if (isset_flash_message_by_name("form_firstname_error")) {
    $form_firstname_error = true;
  } else if (isset_flash_message_by_name("form_picture_error")) {
    $form_picture_error = true;
  } else if (isset_flash_message_by_name("form_dateN_error")) {
    $form_dateN_error = true;
  } else if (isset_flash_message_by_name("form_categorie_error")) {
    $form_categorie_error = true;
  } else if (isset_flash_message_by_name("form_sexe_error")) {
    $form_sexe_error = true;
  } else if (isset_flash_message_by_name("form_mail_error")) {
    $form_mail_error = true;
  } else if (isset_flash_message_by_name("form_tel_error")) {
    $form_tel_error = true;
  }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head> <?php require("./components/head.php"); ?>
  <title>Ajout de licenciés - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
  <?php if (is_logged()) : ?>
    <div class="content">
      <?php include('./components/header.php'); ?>
      <div class="container">
        <div class="container-content">
          <?php include "./components/display_error.php"; ?>
          <div class="add-container">
            <div class="add-panel">
              <h1>
                Ajouter un licencié
              </h1>
              <form action="./functions/licencie-add.php" method="POST" enctype="multipart/form-data" name="form">
                <div class="form-add">
                  <input value="<?php display_info_form("nom-licencie"); ?>" type="text" class="nom-licencie" placeholder="Nom" name="nom-licencie" maxlength="20" onkeyup="javascript:nospaces(this)" onkeydown="javascript:nospaces(this)" <?php if (isset($form_lastname_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                  <input value="<?php display_info_form("prenom-licencie"); ?>" type="text" class="prenom-licencie" placeholder="Prénom" name="prenom-licencie" maxlength="15" onkeyup="javascript:nospaces(this)" onkeydown="javascript:nospaces(this)" <?php if (isset($form_firstname_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                </div>
                <div class="form-add">
                  <label for="photo-licencie" <?php if (isset($form_picture_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                    <i class="fa fa-picture-o"></i> Photo
                    <input id="photo-licencie" type="file" accept="image/png, image/jpeg" name="photo-licencie" />
                    <span id="nom-photo-licencie"></span>
                  </label>
                  <input value="<?php display_info_form("dateN-licencie"); ?>" type="date" placeholder="Date de naissance" name="dateN-licencie" <?php if (isset($form_dateN_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                </div>
                <div class="form-add">
                  <select name="categorie-licencie" id="categorie-licencie" <?php if (isset($form_categorie_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                    <option value="" disabled <?php if (!isset_info_form("categorie-licencie")) : ?> selected <?php endif; ?>>Catégorie</option>
                    <?php
                    if (is_admin()) :
                      $req_category = $db->query("SELECT idCategorie, nomCategorie FROM categorie");
                      while ($category = $req_category->fetch()) :
                        if (isset($category)) :
                    ?>
                          <option value="<?= $category["idCategorie"] ?>" <?php if (isset_info_form("categorie-licencie")) :
                                                                            if ($_SESSION[FORM]['categorie-licencie'] == $category['nomCategorie']) : ?> selected <?php endif;
                                                                                                                                                              endif; ?>><?= $category["nomCategorie"] ?></option>
                          <?php
                        endif;
                      endwhile;
                      $req_category->closeCursor();
                    elseif (is_educ()) :
                      $req_category = $db->prepare(" SELECT categorie.idCategorie, categorie.nomCategorie FROM `categorieeduc` INNER JOIN categorie ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE educ.idEduc = :idEduc");
                      $req_category->bindValue("idEduc", $_SESSION['id']);
                      $req_category->execute();
                      if ($req_category->rowCount() > 0) :
                        while ($category = $req_category->fetch()) :
                          if (isset($category)) :
                          ?>
                            <option value="<?= $category["idCategorie"] ?>" <?php if (isset_info_form("categorie-licencie")) :
                                                                              if ($_SESSION[FORM]['categorie-licencie'] == $category['nomCategorie']) : ?> selected <?php endif;
                                                                                                                                                                endif; ?>><?= $category["nomCategorie"] ?></option>
                    <?php
                          endif;
                        endwhile;
                      endif;
                    endif;
                    ?>
                  </select>
                  <select name="sexe-licencie" id="sexe-licencie" <?php if (isset($form_sexe_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                    <option value="" disabled <?php if (!isset_info_form("sexe-licencie")) : ?> selected <?php endif; ?>>Sexe</option>
                    <option value="m" <?php if (isset_info_form("sexe-licencie")) : ?> selected <?php endif; ?>>Homme</option>
                    <option value="f">Femme</option>
                  </select>
                </div>
                <div class="form-add">
                  <input value="<?php display_info_form("mail-licencie") ?>" type="email" class="mail-licencie" name="mail-licencie" placeholder="Adresse mail" maxlength="40" <?php if (isset($form_mail_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                  <input value="<?php display_info_form("tel-licencie") ?>" type="tel" class="tel-licencie" name="tel-licencie" placeholder="Téléphone" <?php if (isset($form_tel_error)) : ?>style="border: 1px solid red;" <?php endif; ?>>
                </div>
                <div class="loading" id='loading'>
                  <img src="./public/images/Rolling-1s-200px-gray.svg">
                </div>
                <div class="form-add">
                  <input type="submit" value="Ajouter" name="submit-add" class="add-button" id="form-submit" onclick="loading()">
                </div>
              </form>
            </div>
          </div>
          <!-- <div class="return deconnect">
          <a href="index.php">Retour</a>
        </div> -->
        </div>
      </div>
    </div>
    <?php
    //si des infos de formulaire sont présents dans la session, les supprimer
    unset_info_form();
    ?>
    <script>
      let input = document.getElementById("photo-licencie");
      let imageName = document.getElementById("nom-photo-licencie")

      input.addEventListener("change", (e) => {
        let inputImage = e.target.files[0];

        imageName.innerText = inputImage.name;
      })
    </script>
    <script type="text/javascript">
      function nospaces(input) {
        input.value = input.value.replace(" ", "");
        return true;
      }
    </script>
    <?php require './components/footer.php'; ?>
    <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>