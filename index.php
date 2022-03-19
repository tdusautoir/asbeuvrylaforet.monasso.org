<?php
    require("./db.php"); 
    require("./function.php");

    $mess = NULL;
    if(isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout"){
        clean_php_session();
        header("location: index.php");
    }

    if(isset($_POST["submit"])){
        if (!empty($_POST["email"]) && isset($_POST["email"])){
            if(!empty($_POST["password"]) && isset($_POST["password"])){
                if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){

                    $usermail = $_POST["email"];
                    $password = $_POST["password"];

                    $rech_admin = $db->prepare("SELECT * FROM admin WHERE usermail = '$usermail'"); //recherche les utilisateurs dans la table admin correspondant au usermail entrée 
                    $rech_licencie = $db->prepare("SELECT * password FROM educ WHERE usermail = '$usermail'"); //recherche les utilisateurs dans la table educ correspondant au usermail entrée 
                    $rech_educ = $db->prepare("SELECT * password FROM licencie WHERE usermail = '$usermail'"); //recherche les utilisateurs dans la table licencie correspondant au usermail entrée 

                    $rech_admin->execute();
                    $rech_licencie->execute();
                    $rech_educ->execute();

                    $res_admin = $rech_admin->fetch(PDO::FETCH_ASSOC);
                    $res_licencie = $rech_licencie->fetch(PDO::FETCH_ASSOC);
                    $res_educ = $rech_educ->fetch(PDO::FETCH_ASSOC);

                    if($res_admin) {  //res_admin = true donc utilisateur trouvé en tant que admin
                        $passwordHash = $res_admin['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 3;
                        } else { 
                            $mess = "Mot de passe incorrect";
                        }
                    } else if ($res_educ) {  //res_educ = true donc utilisateur trouvé en tant que educateur
                        $passwordHash = $res_educ['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 2;
                        } else {
                            $mess = "Mot de passe incorrect";
                        }
                    } else if ($res_licencie) { //res_licencie = true donc utilisateur trouvé en tant que educateur
                        $passwordHash = $res_licencie['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 1;
                        } else { 
                            $mess = "Mot de passe incorrect";
                        }
                    } else { //Aucun itilisateur trouvé dans la base de données
                            $mess = "Identifiants invalides";
                    }
                } else {
                    $mess = "Veuillez rentrer une adresse email valide";
                }
            } else {
                $mess = "Veuillez remplir votre mot de passe";
            } 
        } else {
            $mess = "Veuillez remplir votre email";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php if(is_logged()) : ?>
        <h1>Bonjour <?= htmlspecialchars($_SESSION['usermail']); ?></h1>
        <a href="index.php?action=logout">Deconnexion</a>
    <?php else : ?>
        <form method="POST" action="#">
            <input type="usermail" placeholder="nom@email.com" name="email">
            <input type="password" placeholder="mot de passe" name="password">
            <button type="submit" name="submit">CONNEXION</button>
        </form>
        <?php alert($mess); ?>
    <?php endif; ?>
</body>
</html>