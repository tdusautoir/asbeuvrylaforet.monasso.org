<?php 
require_once("function.php");
require_once("db.php");

$error_msg = "";

date_default_timezone_set("Europe/Paris");

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
        <?php if(!isset($_GET["token"]) || empty($_GET["token"])): ?>
        <form method="POST" action="resetpw-sendmail.php">
            <input type="usermail" placeholder="nom@email.com" name="email">
            <button type="submit" name="submit">Réinitialiser</button>
            <a href="index.php">Retour</a>
        </form>
        <?php else : ?>
            <form method="POST" action="#">
            <input type="usermail" placeholder="nom@email.com" name="email">
            <input type="password" placeholder="mot de passe" name="password">
            <input type="password" placeholder="mot de passe" name="password_verif">
            <button type="submit" name="submit">Changer de mot de passe</button>
            <a href="index.php">Retour</a>
        </form>
        <?php endif; ?>
</body>
</html>

<?php 

if(isset($_POST["submit"])){
    if (!empty($_POST["email"]) && isset($_POST["email"])){
        if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
            if(!empty($_POST["password"]) && isset($_POST["password"]) && !empty($_POST["password_verif"]) && isset($_POST["password_verif"])){
                $usermail = $_POST["email"];
                $token = $_GET["token"];

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
                                echo "Votre mot de passe a bien éte modifié";
                            } else {
                                echo "Une erreur est survenue";
                            }
                        } else {
                            echo "Les mots de passe ne correspondent pas";
                        }
                    } else {
                        echo "Votre lien a expiré";
                    }
                // } else if ($utilsateur_educ) {  //$utilsateur_educ = true donc utilisateur trouvé en tant que educateur
                //     $error_msg = "Identifiants trouvé en educ";
                // } else if ($utilisateur_licencie) { //$utilisateur_licencie = true donc utilisateur trouvé en tant que educateur
                //     $error_msg = "Identifiants trouvé en licencie";
                } else { //Aucun itilisateur trouvé dans la base de données
                    $error_msg = "Identifiants invalides ou lien invalide";
                }
            } else {
                $error_msg = "Veuillez remplir tout les champs";
            }
        } else {
            $error_msg = "Veuillez rentrer une adresse email valide";
        } 
    } else {
        $error_msg = "Veuillez remplir votre email";
    }
}

echo $error_msg;

?>