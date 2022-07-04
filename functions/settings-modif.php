<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

$get_settings = $db->query("SELECT color, logoPath FROM settings ORDER BY id DESC LIMIT 1");
$settings = $get_settings->fetch(PDO::FETCH_ASSOC);

//verification si l'utilisateur est connecté
if (is_logged()) {
    //verification before add on database
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
                        //upload picture from $_FILES
                        if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
                            //file is valid
                            if ($_FILES['logo']['size'] < 2000000) {
                                //file is inferior to 2 mo
                                $uploadfile = $_FILES['logo']['tmp_name'];
                                $sourceProperties = getimagesize($uploadfile);
                                $newFileName = "logo_" . $_FILES['logo']['name'];
                                $uploaddir = dirname(__FILE__) . "/../public/logo/";
                                $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION); //get extension
                                $image_width = $sourceProperties[0]; //get image width
                                $image_height = $sourceProperties[1]; //get image height
                                $imageType = $sourceProperties[2]; //get image type
                                $newImage_width = 100;
                                $newImage_height = 100;

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

                                // image upload sucessfuly.
                                $imgPath = "./public/logo/" . $newFileName;
                                $req = $db->prepare("INSERT INTO settings (color, logoPath) VALUES (:color, :logoPath)");
                                $req->bindValue("color", $_POST['color']);
                                $req->bindValue("logoPath", $imgPath);
                                $result = $req->execute();

                                // if you want to download the original file :
                                // move_uploaded_file($uploadfile, $uploaddir . $newFileName . "." . $ext); 
                            } else {
                                //file size is too big
                                header("location: ../compte.php");
                                create_flash_message("form_picture_size_error", "La photo doit être inférieure à 2Mo.", FLASH_ERROR);
                                exit();
                            }
                        } else {
                            //possible attack from file upload
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
