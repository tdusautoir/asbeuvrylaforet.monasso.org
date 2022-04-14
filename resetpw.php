<?php 

session_start();

require_once("function.php");
require_once("db.php");

date_default_timezone_set("Europe/Paris");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require("head.php"); ?>
    <title>Rénitialiser votre mot de passe - A.S. BEUVRY LA FORÊT</title>
</head>
<body>
        <?php if(!isset($_GET["token"]) || empty($_GET["token"])): ?>
        <form method="POST" action="resetpw-sendmail.php" class="form_container">
            <div class="form_content">
                <div class="logo_association"><img draggable="false" src="./public/images/logo-asb.svg" alt=""></div>
                        <h1>Réinitialisation du mot de passe</h1>
                        <p>Saisissez l'adresse e-mail associée à votre compte et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
                        <div class="mail">
                            <label for="mail" class="field_label_top">Adresse mail</label>
                            <input id="mail" type="email" pattern="[^ @]*@[^ @]*" placeholder="Adresse mail" name="email" autocomplete='on' <?php if(isset_flash_message_by_name(ERROR_MAIL)): ?>style="border-bottom: 2px solid rgb(210, 0, 0);"<?php endif; ?>>
                            <div class="form_field_error_mail" <?php if(isset_flash_message_by_name(ERROR_MAIL)): ?>style="display: block"<?php endif; ?>><span role="alert"><?php display_flash_message_by_name(ERROR_MAIL); ?></span></div>
                        </div>
                        <div class="submit">
                            <button class="reverse" type="submit" name="submit">Réinitialiser</button>
                        </div>
                </div>
                <?php if(isset_flash_message_by_type(FLASH_SUCCESS)): ?><p class='success'><?php display_flash_message_by_type(FLASH_SUCCESS); ?></p><?php endif; ?>
                <?php if(isset_flash_message_by_type(FLASH_WARNING)): ?><p class='warning'><?php display_flash_message_by_type(FLASH_WARNING); ?></p><?php endif; ?>
        </form>
        <?php else : ?>
        <form method="POST" action="#" class="form_container">
            <div class="form_content">
                <div class="logo_association"><img draggable="false" src="./public/images/logo-asb.svg" alt=""></div>
                <div class="mail">
                    <label for="mail" class="field_label_top">Adresse mail</label>
                    <input id="mail" type="email" pattern="[^ @]*@[^ @]*" placeholder="Adresse mail" name="email" autocomplete='on'<?php if(isset_flash_message_by_name(ERROR_MAIL)): ?>style="border-bottom: 2px solid rgb(210, 0, 0);"<?php endif; ?>>
                    <div class="form_field_error_mail"<?php if(isset_flash_message_by_name(ERROR_MAIL)): ?>style="display: block"<?php endif; ?>><span role="alert"><?php display_flash_message_by_name(ERROR_MAIL); ?></span></div>
                </div>
                <div class="password">
                    <label for="password" class="field_label_top">Nouveau mot de passe</label>
                    <input id="password" type="password" placeholder="Nouveau mot de passe" name="password" autocomplete='off' <?php if(isset_flash_message_by_name(ERROR_PSWD)): ?>style="border-bottom: 2px solid rgb(210, 0, 0);"<?php endif; ?>>
                    <a href="#" role="button" class="view_password_link"><i class="fas fa-eye"></i></a>
                    <div class="form_field_error_password" <?php if(isset_flash_message_by_name(ERROR_PSWD)): ?>style="display: block"<?php endif; ?>><span role="alert"><?php display_flash_message_by_name(ERROR_PSWD); ?></span></div>
                </div>
                <div class="password">
                    <label for="password" class="field_label_top">Ressaisissez votre nouveau mot de passe</label>
                    <input id="password" type="password" placeholder="Ressaisissez votre nouveau mot de passe" name="password_verif" autocomplete='off' <?php if(isset_flash_message_by_name(ERROR_SECOND_PSWD)): ?>style="border-bottom: 2px solid rgb(210, 0, 0);"<?php endif; ?>>
                    <a href="#" role="button" class="view_password_link"><i class="fas fa-eye"></i></a>
                    <div class="form_field_error_password" <?php if(isset_flash_message_by_name(ERROR_SECOND_PSWD)): ?>style="display: block"<?php endif; ?>><span role="alert"><?php display_flash_message_by_name(ERROR_SECOND_PSWD); ?></span></div>
                </div>
                <button type="submit" name="submit">Changer de mot de passe</button>
            </div>
            <?php if(isset_flash_message_by_type(FLASH_SUCCESS)): ?><p class='success'><?php display_flash_message_by_type(FLASH_SUCCESS); ?></p><?php endif; ?>
            <?php if(isset_flash_message_by_type(FLASH_WARNING)): ?><p class='warning'><?php display_flash_message_by_type(FLASH_WARNING); ?></p><?php endif; ?>
        </form>
        <?php endif; ?>
        <script src="../public/js/login.js" type="text/javascript" async></script>
</body>
</html>



<?php 

if(isset($_POST["submit"])){
    $token = $_GET["token"];
    if (!empty($_POST["email"]) && isset($_POST["email"])){
        if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
            if(!empty($_POST["password"]) && isset($_POST["password"])){
                if(!empty($_POST["password_verif"]) && isset($_POST["password_verif"])){
                    $usermail = $_POST["email"];

                    $dateToday = time();

                    $rech_admin = $db->prepare("SELECT * FROM admin WHERE usermail = ? AND pw_recup_token = ? "); //recherche les utilisateurs dans la table admin correspondant au usermail entrée et au token du lien
                    // $rech_licencie = $db->prepare("SELECT * FROM educ WHERE usermail = ? AND pw_recup_token = ? "); //recherche les utilisateurs dans la table educ correspondant au usermail entrée et au token du lien 
                    // $rech_educ = $db->prepare("SELECT * FROM licencie WHERE usermail = ? AND pw_recup_token = ? "); //recherche les utilisateurs dans la table licencie correspondant au usermail entrée et au token du lien

                    $rech_admin->bindValue(1, $usermail);
                    // $rech_licencie->bindValue(1, $usermail);
                    // $rech_educ->bindValue(1, $usermail);

                    $rech_admin->bindValue(2, $token);
                    // $rech_licencie->bindValue(2, $token);
                    // $rech_educ->bindValue(2, $token);

                    $rech_admin->execute();
                    // $rech_licencie->execute();
                    // $rech_educ->execute();

                    $utilisateur_admin = $rech_admin->fetch(PDO::FETCH_ASSOC);
                    // $utilisateur_licencie = $rech_licencie->fetch(PDO::FETCH_ASSOC);
                    // $utilsateur_educ = $rech_educ->fetch(PDO::FETCH_ASSOC);

                    if($utilisateur_admin) {  //$utilisateur_admin = true donc utilisateur trouvé en tant que admin
                        $dateToken = strtotime('+10 minutes', strtotime($utilisateur_admin["date_cr_token"]));
                        if($dateToday < $dateToken){
                            if($_POST["password"] == $_POST["password_verif"]) {
                                $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

                                $pw_update = $db->prepare('UPDATE admin SET password = ?, pw_recup_token = NULL WHERE pw_recup_token = ?');
                                
                                $pw_update->bindValue(1, $password);
                                $pw_update->bindValue(2, $token);

                                var_dump($password);
                                var_dump($pw_update);

                                $success = $pw_update->execute();

                                if($success){
                                    create_flash_message('change_success', 'Votre mot de passe a bien été modifié.', FLASH_SUCCESS); //success changement mot de passe
                                    header("location: resetpw.php?token=$token"); //token=$token
                                    exit;
                                } else {
                                    create_flash_message('change_error', 'Oops, une erreur est survenue, veuillez réessayer.', FLASH_WARNING); //Requete échoué
                                    header("location: resetpw.php?token=$token"); //token=$token
                                    exit;
                                }
                            } else {
                                create_flash_message(ERROR_PSWD, 'Les mots de passe ne correspondent pas', FLASH_ERROR); //Mots de passe non égale
                                create_flash_message(ERROR_SECOND_PSWD, 'Les mots de passe ne correspondent pas', FLASH_ERROR); //Mots de passe non égale
                                header("location: resetpw.php?token=$token"); //token=$token
                                exit;
                            }
                        } else {
                            create_flash_message('link_expired', 'le lien a expiré', FLASH_WARNING); //le lien a expiré
                            header("location: resetpw.php?token=$token"); //token=$token
                            exit;
                        }
                    // } else if ($utilsateur_educ) {  //$utilsateur_educ = true donc utilisateur trouvé en tant que educateur
                    //     $error_msg = "Identifiants trouvé en educ";
                    // } else if ($utilisateur_licencie) { //$utilisateur_licencie = true donc utilisateur trouvé en tant que educateur
                    //     $error_msg = "Identifiants trouvé en licencie";
                    } else { //Aucun itilisateur trouvé dans la base de données
                        create_flash_message(ERROR_MAIL, 'Identifiants invalides ou lien invalide', FLASH_ERROR); //identifiants invalides
                        header("location: resetpw.php?token=$token"); //token=$token
                        exit; 
                    }
                } else {
                    create_flash_message(ERROR_SECOND_PSWD, 'Saisissez votre second mot de passe', FLASH_ERROR); //Second mot de passe non spécifié
                    header("location: resetpw.php?token=$token"); //token=$token
                    exit; 
                }
            } else {
                create_flash_message(ERROR_PSWD, 'Saisissez votre mot de passe', FLASH_ERROR); //Mot de passe non spécifié
                header("location: resetpw.php?token=$token"); //token=$token
                exit; 
            }
        } else {
            create_flash_message(ERROR_MAIL, 'Votre email est invalide', FLASH_ERROR); //email non valide
            header("location: resetpw.php?token=$token"); //token=$token
            exit;
        } 
    } else {
        create_flash_message(ERROR_MAIL, 'Saisissez votre email', FLASH_ERROR); //email non spécifié
        header("location: resetpw.php?token=$token"); //token=$token
        exit;
    }
}

?>
