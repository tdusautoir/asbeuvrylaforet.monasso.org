<?php

require_once("db.php");
require_once("function.php");

$error_msg = '';

if(isset($_POST["submit"])){
    if (!empty($_POST["email"]) && isset($_POST["email"])){
        if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){

            $usermail = $_POST["email"];

            $rech_admin = $db->prepare("SELECT * FROM admin WHERE usermail = ? "); //recherche les utilisateurs dans la table admin correspondant au usermail entrée 
            $rech_licencie = $db->prepare("SELECT * FROM educ WHERE usermail = ? "); //recherche les utilisateurs dans la table educ correspondant au usermail entrée 
            $rech_educ = $db->prepare("SELECT * FROM licencie WHERE usermail = ? "); //recherche les utilisateurs dans la table licencie correspondant au usermail entrée 

            $rech_admin->bindValue(1, $usermail);
            $rech_licencie->bindValue(1, $usermail);
            $rech_educ->bindValue(1, $usermail);

            $rech_admin->execute();
            $rech_licencie->execute();
            $rech_educ->execute();

            $res_admin = $rech_admin->fetch(PDO::FETCH_ASSOC);
            $res_licencie = $rech_licencie->fetch(PDO::FETCH_ASSOC);
            $res_educ = $rech_educ->fetch(PDO::FETCH_ASSOC);

            if($res_admin) {  //res_admin = true donc utilisateur trouvé en tant que admin

                $error_msg = "Identifiants trouvé en admin";
                $token = guidv4();

                var_dump($token);
                var_dump($usermail);

                $insert_token = $db->prepare("UPDATE admin SET date_cr_token = NOW(), pw_recup_token = ? WHERE usermail = ? ");

                $insert_token->bindValue(1, $token);
                $insert_token->bindValue(2, $usermail);

                $success = $insert_token->execute();
                
                if($success){
                    echo "Token ajouté";
                }

            } else if ($res_educ) {  //res_educ = true donc utilisateur trouvé en tant que educateur
                $error_msg = "Identifiants trouvé en educ";
            } else if ($res_licencie) { //res_licencie = true donc utilisateur trouvé en tant que educateur
                $error_msg = "Identifiants trouvé en licencie";
            } else { //Aucun itilisateur trouvé dans la base de données
                $error_msg = "Identifiants introuvables";
            }
        } else {
            $error_msg = "Veuillez rentrer une adresse email valide";
        } 
    } else {
        $error_msg = "Veuillez remplir votre email";
    }
}

echo $error_msg;