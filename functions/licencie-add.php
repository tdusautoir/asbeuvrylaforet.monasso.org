<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

//verification si l'utilisateur est connecté
if (is_logged()) {

    //recup info from formulaire to display it if there is an error
    if (isset($_POST["submit-add"])) {
        if (isset($_POST["nom-licencie"]) && !empty($_POST["nom-licencie"])) {
            add_info_form("nom-licencie", $_POST["nom-licencie"]);
        }
        if (isset($_POST["prenom-licencie"]) && !empty($_POST["prenom-licencie"])) {
            add_info_form("prenom-licencie", $_POST["prenom-licencie"]);
        }
        if (isset($_POST["dateN-licencie"]) && !empty($_POST["dateN-licencie"])) {
            add_info_form("dateN-licencie", $_POST["dateN-licencie"]);
        }
        if (isset($_POST["categorie-licencie"]) && !empty($_POST["categorie-licencie"])) {
            add_info_form("categorie-licencie", $_POST["categorie-licencie"]);
        }
        if (isset($_POST["sexe-licencie"]) && !empty($_POST["sexe-licencie"])) {
            add_info_form("sexe-licencie", $_POST["sexe-licencie"]);
        }
        if (isset($_POST["mail-licencie"]) && !empty($_POST["mail-licencie"]) && filter_var($_POST["mail-licencie"], FILTER_VALIDATE_EMAIL)) {
            add_info_form("mail-licencie", $_POST["mail-licencie"]);
        }
    }

    //verification before add on database
    if (isset($_POST["submit-add"])) {
        if (isset($_POST["nom-licencie"]) && !empty($_POST["nom-licencie"])) {
            if (isset($_POST["prenom-licencie"]) && !empty($_POST["prenom-licencie"])) {
                if ($_FILES['photo-licencie']['error'] != 4) {
                    if (isset($_POST["dateN-licencie"]) && !empty($_POST["dateN-licencie"]) && validateDate($_POST["dateN-licencie"], "Y-m-d")) {
                        if (isset($_POST["categorie-licencie"]) && !empty($_POST["categorie-licencie"])) {
                            if (isset($_POST["sexe-licencie"]) && !empty($_POST["sexe-licencie"])) {
                                if (isset($_POST["mail-licencie"]) && !empty($_POST["mail-licencie"]) && filter_var($_POST["mail-licencie"], FILTER_VALIDATE_EMAIL)) {

                                    //recup info from $_POST
                                    $nom_licencie = strtoupper($_POST["nom-licencie"]);
                                    $prenom_licencie = ucfirst($_POST["prenom-licencie"]);
                                    $dateN_licencie = $_POST["dateN-licencie"];
                                    $mail_licencie = $_POST["mail-licencie"];
                                    $categorie_licencie = $_POST["categorie-licencie"];
                                    $sexe_licencie = $_POST["sexe-licencie"];
                                    $current_user = $_SESSION["prenom"] . " " . strtoupper($_SESSION["nom"]);

                                    //upload picture from $_FILES
                                    if (is_uploaded_file($_FILES['photo-licencie']['tmp_name'])) {
                                        //file is valid
                                        if ($_FILES['photo-licencie']['size'] < 2000000) {
                                            //file is inferior to 2 mo
                                            $uploadfile = $_FILES['photo-licencie']['tmp_name'];
                                            $sourceProperties = getimagesize($uploadfile);
                                            $newFileName = mb_strtolower($nom_licencie) . "_" . mb_strtolower($prenom_licencie) . "_" . date("Ymd"); //filename = Nom_prenom_date
                                            $uploaddir = dirname(__FILE__) . "/../public/profiles/";
                                            $ext = pathinfo($_FILES['photo-licencie']['name'], PATHINFO_EXTENSION); //get extension
                                            $image_width = $sourceProperties[0]; //get image width
                                            $image_height = $sourceProperties[1]; //get image height
                                            $imageType = $sourceProperties[2]; //get image type
                                            $newImage_width = 200; //$image_width / $image_width * 200
                                            $newImage_height = 200; //$image_height / $image_width * 200

                                            switch ($imageType) {

                                                case IMAGETYPE_PNG: //$imageType == 3
                                                    $imageSrc = imagecreatefrompng($uploadfile);
                                                    $tmp = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                                                    imagepng($tmp, $uploaddir . $newFileName . "_resize." . $ext);
                                                    break;

                                                case IMAGETYPE_JPEG: //$imageType == 2
                                                    $imageSrc = imagecreatefromjpeg($uploadfile);
                                                    $tmp = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                                                    imagejpeg($tmp, $uploaddir . $newFileName . "_resize." . $ext);
                                                    break;

                                                case IMAGETYPE_GIF: //$imageType == 1
                                                    $imageSrc = imagecreatefromgif($uploadfile);
                                                    $tm = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                                                    imagegif($tmp, $uploaddir . $newFileName . "_resize." . $ext);
                                                    break;

                                                default:
                                                    header("location: ../add-licencie.php");
                                                    create_flash_message("form_picture_error", "Le type de votre image doit être jpeg ou png.", FLASH_ERROR);
                                                    exit();
                                                    break;
                                            }

                                            // image upload sucessfuly.

                                            // if you want to download the original file :
                                            // move_uploaded_file($uploadfile, $uploaddir . $newFileName . "." . $ext); 
                                        } else {
                                            //file size is too big
                                            header("location: ../add-licencie.php");
                                            create_flash_message("form_picture_error", "Votre photo doit être inférieur à 2 mo.", FLASH_ERROR);
                                            exit();
                                        }
                                    } else {
                                        //possible attack from file upload
                                        header("location: ../add-licencie.php");
                                        create_flash_message("form_picture_error", "1 - Une erreur est survenue, veuillez réessayer", FLASH_ERROR);
                                        exit();
                                    }

                                    //add photo on database
                                    $imgPath = "./public/profiles/" . $newFileName . "_resize." . $ext;
                                    $addPhoto = $db->prepare("INSERT INTO photo (imgPath, USRCRE) VALUES (?, ?);");
                                    $addPhoto->bindValue(1, $imgPath);
                                    $addPhoto->bindValue(2, $current_user, PDO::PARAM_STR);
                                    $result_add = $addPhoto->execute();

                                    if (!$result_add) {
                                        header("location: ../add-licencie.php");
                                        create_flash_message("add_photo_error", "Une erreur est survenue, veuillez réessayer.", FLASH_SUCCESS);
                                        exit();
                                    }

                                    //get the last idPhoto
                                    $getLastPhotoId = $db->prepare("SELECT photo.idPhoto FROM photo ORDER BY photo.idPhoto DESC LIMIT 1");
                                    $getLastPhotoId->execute();
                                    if (!$getLastPhotoId) {
                                        header("location: ../add-licencie.php");
                                        create_flash_message("get_photo_id_error", "Une erreur est survenue, veuillez réessayer.", FLASH_SUCCESS);
                                        exit();
                                    }
                                    $result_getLastId = $getLastPhotoId->fetch();
                                    $PhotoId = $result_getLastId["idPhoto"];

                                    //add a licencie on database
                                    $req = $db->prepare("INSERT INTO licencie (prenom, nom, sexe, dateN, mail, idCategorie, idPhoto, USRCRE) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");
                                    $req->bindValue(1, $prenom_licencie, PDO::PARAM_STR);
                                    $req->bindValue(2, $nom_licencie, PDO::PARAM_STR);
                                    $req->bindValue(3, $sexe_licencie, PDO::PARAM_STR);
                                    $req->bindValue(4, $dateN_licencie, PDO::PARAM_STR);
                                    $req->bindValue(5, $mail_licencie, PDO::PARAM_STR);
                                    $req->bindValue(6, $categorie_licencie, PDO::PARAM_INT);
                                    $req->bindValue(7, $PhotoId, PDO::PARAM_STR);
                                    $req->bindValue(8, $current_user, PDO::PARAM_STR);
                                    $result = $req->execute();

                                    if ($result) {
                                        header("location: ../add-licencie.php");
                                        create_flash_message("add_success", "Licencié ajouté.", FLASH_SUCCESS);
                                        unset_info_form();
                                        exit();
                                    } else {
                                        header("location: ../add-licencie.php");
                                        create_flash_message("add_error", "Une erreur est survenue, veuillez réessayer.", FLASH_ERROR);
                                        exit();
                                    }
                                } else {
                                    header("location: ../add-licencie.php");
                                    create_flash_message("form_mail_error", "Email invalide.", FLASH_ERROR);
                                    exit();
                                }
                            } else {
                                header("location: ../add-licencie.php");
                                create_flash_message("form_sexe_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                                exit();
                            }
                        } else {
                            header("location: ../add-licencie.php");
                            create_flash_message("form_categorie_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                            exit();
                        }
                    } else {
                        header("location: ../add-licencie.php");
                        create_flash_message("form_dateN_error", "Veuillez remplir une date valide.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../add-licencie.php");
                    create_flash_message("form_picture_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../add-licencie.php");
                create_flash_message("form_firstname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ../add-licencie.php");
            create_flash_message("form_lastname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../add-licencie.php");
        create_flash_message("add_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
