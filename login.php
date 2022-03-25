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
                            $_SESSION['alert'] = "Mot de passe incorrect";
                        }
                    } else if ($res_educ) {  //res_educ = true donc utilisateur trouvé en tant que educateur
                        $passwordHash = $res_educ['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 2;
                        } else {
                            $_SESSION['alert'] = "Mot de passe incorrect";
                        }
                    } else if ($res_licencie) { //res_licencie = true donc utilisateur trouvé en tant que educateur
                        $passwordHash = $res_licencie['password'];
                        if(password_verify($password, $passwordHash)){ //verifier la correspondance du mot de passe
                            init_php_session();

                            $_SESSION['usermail'] = $usermail;
                            $_SESSION['role'] = 1;
                        } else { 
                            $_SESSION['alert'] = "Mot de passe incorrect";
                        }
                    } else { //Aucun itilisateur trouvé dans la base de données
                            $_SESSION['alert'] = "Identifiants invalides";
                    }
                } else {
                    $_SESSION['alert'] = "Veuillez rentrer une adresse email valide";
                }
            } else {
                $_SESSION['alert'] = "Veuillez remplir votre mot de passe";
            } 
        } else {
            $_SESSION['alert'] = "Veuillez remplir votre email";
        }
 }

header("location: index.php");
