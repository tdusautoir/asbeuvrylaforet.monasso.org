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

            $utilisateur_admin = $rech_admin->fetch(PDO::FETCH_ASSOC);
            $utilisateur_licencie = $rech_licencie->fetch(PDO::FETCH_ASSOC);
            $utilisateur_educ = $rech_educ->fetch(PDO::FETCH_ASSOC);

            if($utilisateur_admin) {  //utilsateur_admin = true donc utilisateur trouvé en tant que admin

                $error_msg = "Identifiants trouvé en admin";
                $token = guidv4();

                $insert_token = $db->prepare("UPDATE admin SET date_cr_token = NOW(), pw_recup_token = ? WHERE usermail = ? ");

                $insert_token->bindValue(1, $token);
                $insert_token->bindValue(2, $usermail);

                $success = $insert_token->execute();
                
                if($success){
                    $link = "https://www.dev-asbeuvrylaforet.monasso.org/backdev/?token=".$token;
                    $to = $usermail;
                    $subject = 'Reinitialisation de votre mot de passe';
                    $mailContent = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                    <html xmlns='http://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
                    <head>
                        <!--[if gte mso 9]>
                        <xml>
                            <o:OfficeDocumentSettings>
                            <o:AllowPNG/>
                            <o:PixelsPerInch>96</o:PixelsPerInch>
                            </o:OfficeDocumentSettings>
                        </xml>
                        <![endif]-->
                        <meta http-equiv='Content-type' content='text/html; charset=utf-8' />
                        <meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1' />
                        <meta http-equiv='X-UA-Compatible' content='IE=edge' />
                        <meta name='format-detection' content='date=no' />
                        <meta name='format-detection' content='address=no' />
                        <meta name='format-detection' content='telephone=no' />
                        <meta name='x-apple-disable-message-reformatting' />
                        <!--[if !mso]><!-->
                        <link href='https://fonts.googleapis.com/css?family=Yantramanav:300,400,500,700' rel='stylesheet' />
                        <!--<![endif]-->
                        <title>Mot de pas oublié</title>
                        <!--[if gte mso 9]>
                        <style type='text/css' media='all'>
                            sup { font-size: 100% !important; }
                        </style>
                        <![endif]-->
                        

                        <style type='text/css' media='screen'>
                            /* Linked Styles */
                            body { padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f4f4f4; -webkit-text-size-adjust:none }
                            a { color:#2f774a; text-decoration:none }
                            p { padding:0 !important; margin:0 !important } 
                            img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }
                            .mcnPreviewText { display: none !important; }

                                    
                            /* Mobile styles */
                            @media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
                                u + .body .gwfw { width:100% !important; width:100vw !important; }

                                .m-shell { width: 100% !important; min-width: 100% !important; }
                                
                                .m-center { text-align: center !important; }
                                
                                .center { margin: 0 auto !important; }
                                .nav { text-align: center !important; }
                                .text-top { line-height: 22px !important; }
                                
                                .td { width: 100% !important; min-width: 100% !important; }
                                .bg { height: auto !important; -webkit-background-size: cover !important; background-size: cover !important; }

                                .m-br-15 { height: 15px !important; }
                                .p30-15 { padding: 30px 15px !important; }
                                .p0-15-30 { padding: 0px 15px 30px 15px !important; }
                                .pb40 { padding-bottom: 40px !important; }
                                .pb0 { padding-bottom: 0px !important; }
                                .pb20 { padding-bottom: 20px !important; }

                                .m-td,
                                .m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }

                                .m-height { height: auto !important; }

                                .m-block { display: block !important; }

                                .fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

                                .column,
                                .column-top,
                                .column-dir,
                                .column-bottom,
                                .column-dir-top,
                                .column-dir-bottom { float: left !important; width: 100% !important; display: block !important; }

                                .content-spacing { width: 15px !important; }
                            }
                        </style>
                    </head>
                    <body class='body' style='padding:0 !important; margin:0 !important; display:block !important; min-width:100% !important; width:100% !important; background:#f4f4f4; -webkit-text-size-adjust:none;'>
                        <table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#f4f4f4' class='gwfw'>
                            <tr>
                                <td align='center' valign='top'>
                                    <!-- Main -->
                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                        <tr>
                                            <td align='center' style='padding-bottom: 40px;' class='pb0'>
                                                <!-- Shell -->
                                                <table width='650' border='0' cellspacing='0' cellpadding='0' class='m-shell'>
                                                    <tr>
                                                        <td class='td' style='width:650px; min-width:650px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;'>
                                                            <!-- Top Bar -->
                                                            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                <tr>
                                                                    <td style='padding: 60px 40px 35px 40px;' class='p30-15'>
                                                                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                            <tr>
                                                                                <th class='column-top' width='204' style='font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; vertical-align:top;'>
                                                                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                                    </table>
                                                                                </th>
                                                                                <th style='padding-bottom: 20px !important; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;' class='column' width='1'></th>
                                                                                <th class='column' style='font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;'>
                                                                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                                    </table>
                                                                                </th>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!-- END Top Bar -->

                                                            <!-- Header -->
                                                            <table width='100%' border='0' cellspacing='0' cellpadding='40' bgcolor='#ffffff' style='border-radius: 6px 6px 0px 0px;'>
                                                                <tr>
                                                                    <td style=''>
                                                                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                            <tr>
                                                                                <center>
                                                                                    <th class='column' width='118' style='font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;'>
                                                                                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                                            <tr>
                                                                                                <td class='img m-center' style='font-size:0pt; line-height:0pt; text-align:right;'><a href='#' target='_blank'><img src='https://www.dev-asbeuvrylaforet.monasso.org/public/images/logo-asb.png' width='110' height='110' border='0' alt='' /></a></td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </th>
                                                                                </center>
                                                                                <th class='column' style='font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;'>
                                                                                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                                        <tr>
                                                                                            <td align='center'>
                                                                                                <table border='0' cellspacing='0' cellpadding='0' class='center' style='text-align:center;'>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </th>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!-- END Header -->
                                                            
                                                            <!-- Article Image On The Left -->
                                                            <div mc:repeatable='Select' mc:variant='Article Image On The Left'>
                                                                <table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='#ffffff'>
                                                                    <tr>
                                                                        <td style='padding: 0px 40px 40px 40px;' class='p0-15-30'>
                                                                            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                                <tr>
                                                                                    <th class='column' style='font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal;'>
                                                                                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                                                                            <tr>
                                                                                                <td class='h2' style='padding-bottom: 20px; color:#444444; font-family:Yantramanav, Arial, sans-serif; font-size:40px; line-height:46px; text-align:center; font-weight:300;'>Réinitialiser <span class='m-hide'><br /></span>votre mot de passe</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td class='text' style='padding-bottom: 25px; color:#666666; font-family:Arial, sans-serif; font-size:16px; line-height:30px; text-align:center; min-width:auto !important;'>Cliquez ci-dessous pour réinitialiser votre mot de passe !</td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td align='center'>
                                                                                                    <table border='0' cellspacing='0' cellpadding='0'>
                                                                                                        <tr>
                                                                                                            <td class='text-button' style='color:#ffffff; background:#309C34; border-radius:5px; font-family:Yantramanav, Arial, sans-serif; font-size:14px; line-height:18px; text-align:center; font-weight:500; padding:12px 25px;'><a href='https://www.dev-asbeuvrylaforet.monasso.org/backdev/resetpw.php?token=$token' target='_blank' class='link-white' style='color:#ffffff; text-decoration:none;'><CENTER><span class='link-white' style='color:#ffffff; text-decoration:none;'>Réinitialiser</span></CENTER></a></td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                    
                                                                                                    <tr>
                                                                                                        <td class='text' style='padding-bottom: 25px;padding-top: 25px; color:#666666; font-family:Arial, sans-serif; font-size:16px; line-height:30px;text-align:center; min-width:auto !important;'>Si vous avez un problème pour visualiser ce mail, n'hésitez pas à cliquez <a href='https://www.dev-asbeuvrylaforet.monasso.org/backdev/resetpw.php?token=$token'><strong>ici</strong></a> !</td>
                                                                                                    </tr>
                                                                                                    <tr>
                                                                                                        <td align='right'>
                                                                                                            <table class='center' border='0' cellspacing='0' cellpadding='0' style='text-align:center;'>
                                                                                                                <td class='img' width='55' style='font-size:0pt; line-height:0pt; text-align:left;'><a href='#' target='_blank'><img src='https://www.dev-asbeuvrylaforet.monasso.org/public/images/ico_facebook.png' width='34' height='34' border='0' alt='' /></a></td>
                                                                                                                <td class='img' width='55' style='font-size:0pt; line-height:0pt; text-align:left;'><a href='#' target='_blank'><img src='https://www.dev-asbeuvrylaforet.monasso.org/public/images/ico_instagram.png' width='34' height='34' border='0' alt='' /></a></td>
                                                                                                            </table>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </table>
                                                                                    </th>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                            <!-- END Article Image On The Left -->
                                                            
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- END Shell -->
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- END Main -->
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>";

                    $headers[] = 'MIME-Version: 1.0';
                    $headers[] =  "Content-type: text/html; charset=UTF-8";
                    
                    $send_succ = mail($to, $subject, $mailContent, implode("\r\n", $headers));
                    
                    if($send_succ){
                        $error_msg = "Mail envoyé";
                    } else {
                        $error_msg = "erreur envoie mail";
                    }
                } else {
                    $error_msg = "erreur";
                }

            } else if ($utilisateur_educ) {  //i = true donc utilisateur trouvé en tant que educateur
                $error_msg = "Identifiants trouvé en educ";
            } else if ($utilisateur_licencie) { //i = true donc utilisateur trouvé en tant que educateur
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