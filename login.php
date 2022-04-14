<?php

session_start();

require_once("./db.php");
require_once("./function.php"); 

if(isset($_POST["submit"])){
        if (!empty($_POST["email"]) && isset($_POST["email"])){
            if(!empty($_POST["password"]) && isset($_POST["password"])){
                if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){

                    $usermail = $_POST["email"];
                    $password = $_POST["password"];

                    $rech_admin = $db->prepare("SELECT * FROM admin WHERE usermail = ? "); //recherche les utilisateurs dans la table admin correspondant au usermail entrée 
                    $rech_licencie = $db->prepare("SELECT * FROM educ WHERE usermail = ? "); //recherche les utilisateurs dans la table educ correspondant au usermail entrée 
                    $rech_educ = $db->prepare("SELECT * FROM licencie WHERE usermail = ? "); //recherche les utilisateurs dans la table licencie correspondant au usermail entrée 

                    $rech_admin->bindValue(1, $usermail);
                    $rech_licencie->bindValue(1, $usermail);
                    $rech_educ->bindValue(1, $usermail);

                    $rech_admin->execute();
                    $rech_licencie->execute();
                    $rech_educ->execute();

                    $utilisateur_admin = $rech_admin->fetch(PDO::FETCH_ASSOC);
                    $utilisateur_educ = $rech_licencie->fetch(PDO::FETCH_ASSOC);
                    $utilisateur_licencie = $rech_educ->fetch(PDO::FETCH_ASSOC);

                    if($utilisateur_admin) {  //utilisateur_admin = true donc utilisateur trouvé en tant que admin
                        $passwordHash = $utilisateur_admin['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 3;
                        } else { 
                            create_flash_message(ERROR_PSWD, 'Mot de passe invalide', FLASH_ERROR); //Mot de passe invalide
                            header("location: index.php");
                            exit;
                        }
                    } else if ($utilisateur_licencie) {  //utilisateur_licencie = true donc utilisateur trouvé en tant que educateur
                        $passwordHash = $utilisateur_licencie['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 2;
                        } else {
                            create_flash_message(ERROR_PSWD, 'Mot de passe invalide', FLASH_ERROR); //Mot de passe invalide
                            header("location: index.php");
                            exit;
                        }
                    } else if ($utilisateur_educ) { //utilisateur_educ = true donc utilisateur trouvé en tant que educateur
                        $passwordHash = $utilisateur_educ['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 1;
                        } else { 
                            create_flash_message(ERROR_PSWD, 'Mot de passe invalide', FLASH_ERROR); //Mot de passe invalide
                            header("location: index.php");
                            exit;
                        }
                    } else { //Aucun itilisateur trouvé dans la base de données
                        create_flash_message(ERROR_MAIL, 'Identifiants invalides', FLASH_ERROR); //Identifiants invalides
                        header("location: index.php");
                        exit;

                    }
                } else {
                    create_flash_message(ERROR_MAIL, 'Email non valide', FLASH_ERROR); //Email non valide
                    header("location: index.php");
                    exit;
                }
            } else {
                create_flash_message(ERROR_PSWD, 'Saisissez votre mot de passe', FLASH_ERROR); //Mot de passe non spécifié
                header("location: index.php");
                exit;
            } 
        } else {
            create_flash_message(ERROR_MAIL, 'Saisissez votre email', FLASH_ERROR);  //email non spécifié
            header("location: index.php");
            exit;
        }
 }

header("location: index.php");
