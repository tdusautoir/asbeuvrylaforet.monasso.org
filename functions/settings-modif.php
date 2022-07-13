<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");


//verification si l'utilisateur est connecté
if (is_logged()) {
    $get_settings = $db->query("SELECT color, logoPath FROM settings ORDER BY id DESC LIMIT 1");
    $settings = $get_settings->fetch(PDO::FETCH_ASSOC);
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['submit-settings'])) {
            if (isset($_POST['color'])) {
                if (isset($_FILES['logo'])) {
                    if (empty($_FILES['logo']['name'])) {
                        $req = $db->prepare("INSERT INTO settings (color, logoPath) VALUES (:color, :logoPath)");
                        $req->bindValue("color", $_POST['color']);
                        $req->bindValue("logoPath", $settings['logoPath']);
                        $result = $req->execute();
                    } else {
                        //telecharger le logo via $_FILES
                        if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
                            //le fichier est valide
                            if ($_FILES['logo']['size'] < 2000000) {
                                //fichier est inferieur a 2mo
                                $uploadfile = $_FILES['logo']['tmp_name'];
                                $sourceProperties = getimagesize($uploadfile);
                                $newFileName = "logo_" . $_FILES['logo']['name'];
                                $uploaddir = dirname(__FILE__) . "/../public/logo/";
                                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION); //recup extensio 
                                $image_width = $sourceProperties[0]; // recup la largeur de l'image
                                $image_height = $sourceProperties[1]; //recuper la hauteur de l'image
                                $imageType = $sourceProperties[2]; //recuperer le type de l'image
                                $newImage_width = $image_width / $image_width * 300; //initialiser la nouvelle largeur a 300px
                                $newImage_height = $image_height / $image_width * 300; //initialiser la hauteur selon la largeur a 300px pour garder le meme ratio

                                switch ($imageType) {

                                    case IMAGETYPE_PNG: //$imageType == 3
                                        $imageSrc = imagecreatefrompng($uploadfile);
                                        $tmp = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                                        imagepng($tmp, $uploaddir . $newFileName);
                                        break;

                                    case IMAGETYPE_JPEG: //$imageType == 2
                                        $imageSrc = imagecreatefromjpeg($uploadfile);
                                        $tmp = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                                        imagejpeg($tmp, $uploaddir . $newFileName);
                                        break;

                                    case IMAGETYPE_GIF: //$imageType == 1
                                        $imageSrc = imagecreatefromgif($uploadfile);
                                        $tm = imageResize($newImage_width, $newImage_height, $imageSrc, $image_width, $image_height);
                                        imagegif($tmp, $uploaddir . $newFileName);
                                        break;

                                    default:
                                        header("location: ../compte.php");
                                        create_flash_message("form_picture_type_error", "Le type de votre image doit être jpeg ou png.", FLASH_ERROR);
                                        exit();
                                        break;
                                }

                                // image telecharger 
                                $imgPath = "./public/logo/" . $newFileName;

                                //inserer en base les nouvelles informations
                                $req = $db->prepare("INSERT INTO settings (color, logoPath) VALUES (:color, :logoPath)");
                                $req->bindValue("color", $_POST['color']);
                                $req->bindValue("logoPath", $imgPath);
                                $result = $req->execute();

                                // Pour telecharger le fichier original sans copie et redimensionnement
                                // move_uploaded_file($uploadfile, $uploaddir . $newFileName . "." . $ext); 
                            } else {
                                //ficher est trop lourd
                                header("location: ../compte.php");
                                create_flash_message("form_picture_size_error", "La photo doit être inférieure à 2Mo.", FLASH_ERROR);
                                exit();
                            }
                        } else {
                            //attaque possible via fichier
                            header("location: ../compte.php");
                            create_flash_message("form_picture_error", "Une erreur est survenue, veuillez réessayer", FLASH_ERROR);
                            exit();
                        }
                    }

                    if ($result) {
                        header("location: ../compte.php");
                        create_flash_message("req-success", "La modification a bien été effectué.", FLASH_SUCCESS);
                        exit();
                    } else {
                        header("location: ../compte.php");
                        create_flash_message("req-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../compte.php");
                    create_flash_message("form_logo_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                    exit();
                }
            } else {
                header("location: ../compte.php");
                create_flash_message("form_color_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                exit();
            }
        } else {
            header("location: ../compte.php");
            create_flash_message("submit-error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../index.php");
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
