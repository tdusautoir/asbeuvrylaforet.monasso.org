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

<head> <?php require("head.php"); ?>
  <link rel="stylesheet" href="./public/css/login.css">
  <link rel="stylesheet" href="./public/css/style.css">
  <link rel="stylesheet" href="./public/css/dev.css">
  <title>Espace Ajout Licencié - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
  <?php if (is_logged()) : ?>
    <?php include('header.php'); ?>
    <div class="container">
      <div class="container-content">
        <div class="add-panel">
            <h1>
                Ajouter un licencié
            </h1>
            <form action="">
            <div class="form-add">
                <input type="text" class="nom-licencie" placeholder="Nom" required="required">
                <input type="text" class="prenom-licencie" placeholder="Prénom" required="required">
            </div>
            <div class="form-add">
              <label for="photo-licencie">
                Photo du licencié
              <input id="photo-licencie" type="file" accept="image/png, image/jpeg" required="required"/>
              <span id="nom-photo-licencie"></span>
              </label>
              <input type="date">
            </div>
            <div class="form-add">
              <select name="groupe-licencie" id="groupe-licencie" required="required">
                <option value="" disabled selected>Catégorie</option>
                <option value="U10">U10</option>
                <option value="U11">U11</option>
                <option value="U12">U12</option>
              </select>
              <select name="sexe-licencie" id="sexe-licencie" required="required">
                <option value="" disabled selected>Sexe</option>
                <option value="m">Homme</option>
                <option value="f">Femme</option>
              </select>
            </div>
            <div class="mail-form-add">
                <input type="mail" class="mail-licencie" placeholder="Adresse mail" required="required">
            </div>
            <div class="form-add">
              <input type="submit" value="Ajouter" class="bouton-ajouter">
            </div>
            </form>
        </div>
        <div class="deconnect">
          <a href="index.php">Retour</a>
        </div>
      </div>
    </div>
    <script>
      let input = document.getElementById("photo-licencie");
        let imageName = document.getElementById("nom-photo-licencie")

        input.addEventListener("change", ()=>{
            let inputImage = document.querySelector("input[type=file]").files[0];

            imageName.innerText = inputImage.name;
        })
    </script>
  <?php else : ?> <section class="formulaire_login">
      <form method="POST" action="login.php" class="form_container">
        <div class="form_content">
          <div class="logo_association">
            <img draggable="false" src="./public/images/logo-asb.svg" alt="">
          </div>
          <div class="mail">
            <label for="mail" class="field_label_top">Adresse mail</label>
            <input id="mail" type="mail" pattern="[^ @]*@[^ @]*" placeholder="Adresse mail" name="email" autocomplete='on' <?php if (isset_flash_message_by_name(ERROR_MAIL)) : ?>style="border-bottom: 2px solid rgb(210, 0, 0);" <?php endif; ?>>
            <div class="form_field_error_mail" <?php if (isset_flash_message_by_name(ERROR_MAIL)) : ?>style="display: block;" <?php endif; ?>>
              <span role="alert"> <?php display_flash_message_by_name(ERROR_MAIL); ?> </span>
            </div>
          </div>
          <div class="password">
            <label for="password" class="field_label_top">Mot de passe</label>
            <input id="password" type="password" placeholder="Mot de passe" name="password" autocomplete='on' <?php if (isset_flash_message_by_name(ERROR_PSWD)) : ?>style="border-bottom: 2px solid rgb(210, 0, 0);" <?php endif; ?>>
            <a href="#" role="button" class="view_password_link">
              <i class="fas fa-eye"></i>
            </a>
            <a href="./resetpw.php" class="forgot_pwd">Mot de passe oublié ?</a>
            <div class="form_field_error_password" <?php if (isset_flash_message_by_name(ERROR_PSWD)) : ?>style="display: block;" <?php endif; ?>>
              <span role="alert"> <?php display_flash_message_by_name(ERROR_PSWD); ?> </span>
            </div>
          </div>
          <div class="submit">
            <button type="submit" name="submit">Se connecter</button>
          </div>
      </form>
    </section>
    <script src="./public/js/login.js" type="text/javascript" async></script><?php endif; ?>
</body>

</html>