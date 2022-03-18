<?php require("./db.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="#">
        <input type="mail" placeholder="nom@email.com" name="email">
        <input type="password" placeholder="mot de passe" name="password">
        <button type="submit" name="submit">CONNEXION</button>
    </form>
</body>
</html>

<?php


if(isset($_POST["submit"])){
    if (!empty($_POST["email"]) && isset($_POST["email"])){
        if(!empty($_POST["password"]) && isset($_POST["password"])){
            if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){

                $mail = $_POST["email"];
                $password = $_POST["password"];

                $rech_admin = $db->prepare("SELECT * FROM admin WHERE usermail = '$mail'"); //recherche les utilisateurs dans la table admin correspondant au mail entrée 
                $rech_licencie = $db->prepare("SELECT * password FROM educ WHERE usermail = '$mail'"); //recherche les utilisateurs dans la table educ correspondant au mail entrée 
                $rech_educ = $db->prepare("SELECT * password FROM licencie WHERE usermail = '$mail'"); //recherche les utilisateurs dans la table licencie correspondant au mail entrée 

                $rech_admin->execute();
                $rech_licencie->execute();
                $rech_educ->execute();

                $res_admin = $rech_admin->fetch(PDO::FETCH_ASSOC);
                $res_licencie = $rech_licencie->fetch(PDO::FETCH_ASSOC);
                $res_educ = $rech_educ->fetch(PDO::FETCH_ASSOC);

                if($res_admin) {  //res_admin = true donc utilisateur trouvé en tant que admin
                    $passwordHash = $res_admin['password'];
                    if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                        echo "<p class='alert'>Connexion réussie</p>";
                    } else { 
                        echo "<p class='alert'>Votre mot de passe est incorrect</p>";
                    }
                } else if ($res_educ) {  //res_educ = true donc utilisateur trouvé en tant que educateur
                    $passwordHash = $res_educ['password'];
                    if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                        echo "<p class='alert'>Connexion réussie</p>";
                    } else { 
                        echo "<p class='alert'>Votre mot de passe est incorrect</p>";
                    }
                } else if ($res_licencie) { //res_licencie = true donc utilisateur trouvé en tant que educateur
                    $passwordHash = $res_licencie['password'];
                    if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                        echo "<p class='alert'>Connexion réussie</p>";
                    } else { 
                        echo "<p class='alert'>Votre mot de passe est incorrect</p>";
                    }
                } else { //Aucun itilisateur trouvé dans la base de données
                        echo "<p class='alert'>Identifiants invalides</p>";
                }
            } else {
                echo "<p class='alert'>Veuillez rentrer une adresse email valide</p>";
            }
        } else {
            echo "<p class='alert'>Veuillez remplir votre mot de passe</p>";
        } 
    } else {
        echo "<p class='alert'>Veuillez remplir votre email</p>";
    }
}

?>