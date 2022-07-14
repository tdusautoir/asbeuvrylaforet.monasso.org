<?php require_once "./db.php";
$logo = $db->query("SELECT logoPath FROM settings ORDER BY id DESC LIMIT 1;");
$get_logo = $logo->fetch(PDO::FETCH_ASSOC);
?>

<section class="formulaire_login">
    <form method="POST" action="./functions/login.php" class="form_container">
        <div class="form_content">
            <div class="logo_association">
                <img draggable="false" src="<?= $get_logo["logoPath"] ?>" alt="">
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
<script src="./public/js/login.js" type="text/javascript" async></script>