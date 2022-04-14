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
    <?php include('head.php'); ?>
    <title>Se connecter | A.S. BEUVRY LA FORÊT</title>
</head>
<body>
    <?php include('header.php'); ?>
    <?php if(is_logged()) : ?>
        <h1>Bonjour <?= htmlspecialchars($_SESSION['usermail']); ?></h1>
        <a href="index.php?action=logout">Deconnexion</a>
    <?php else : ?>
        <form method="POST" action="login.php">
            <input type="text" placeholder="nom@email.com" name="email">
            <input type="password" placeholder="mot de passe" name="password">
            <button type="submit" name="submit">CONNEXION</button>
        </form>
        <?php if(isset($_SESSION['alert'])){ alert($_SESSION['alert']); }?>
        <a href="resetpw.php">Mot de passe oublié ?</a>
    <?php endif; ?>
</body>
</html>