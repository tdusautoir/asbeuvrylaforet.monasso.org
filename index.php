<?php
    session_start();

    require("./function.php");

    if(isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout"){
        clean_php_session();
        header("location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require("head.php"); ?>
	<link rel="stylesheet" href="https://www.dev-asbeuvrylaforet.monasso.org/public/css/login.css">
    <title>Se connecter - A.S. BEUVRY LA FORÊT</title>
</head>
<body>
    <?php if(is_logged()) : ?>
        <h1>Bonjour <?= htmlspecialchars($_SESSION['usermail']); ?></h1>
        <a href="index.php?action=logout">Deconnexion</a>
    <?php else : ?>
    <div class="content">
        <form method="POST" action="login.php" class="form_bloc">
			<div class="form_container">
				<div class="form_content">
				<div class="logo_association"><img draggable="false" src="./public/images/logo-asb.svg" alt=""></div>

				<div class="mail">
					<label for="mail" class="field_label_top">Adresse mail</label>
					<input id="mail" type="mail" pattern="[^ @]*@[^ @]*" placeholder="Adresse mail" name="email" autocomplete='on' <?php if(isset_flash_message_by_name(ERROR_MAIL)): ?>style="border-bottom: 2px solid rgb(210, 0, 0);"<?php endif; ?>>
					<div class="form_field_error_mail" <?php if(isset_flash_message_by_name(ERROR_MAIL)): ?>style="display: block;"<?php endif; ?>><span role="alert"><?php display_flash_message_by_name(ERROR_MAIL); ?></span></div>
				</div>
				<div class="password">
					<label for="password" class="field_label_top">Mot de passe</label>
					<input id="password" type="password" placeholder="Mot de passe" name="password" autocomplete='on'  <?php if(isset_flash_message_by_name(ERROR_PSWD)): ?>style="border-bottom: 2px solid rgb(210, 0, 0);"<?php endif; ?>>
					<a href="#" role="button" class="view_password_link"><i class="fas fa-eye"></i></a>
					<a href="./backdev/resetpw.php" class="forgot_pwd">Mot de passe oublié ?</a>
					<div class="form_field_error_password" <?php if(isset_flash_message_by_name(ERROR_PSWD)): ?>style="display: block;"<?php endif; ?>><span role="alert"><?php display_flash_message_by_name(ERROR_PSWD); ?></span></div>
				</div>
				<div class="submit">
					<button type="submit" name="submit">Se connecter</button>
                </div>
			</div>
        </form>
    </div>
	<script src="../public/js/login.js" type="text/javascript" async></script>
    <?php endif; ?>
</body>
</html>