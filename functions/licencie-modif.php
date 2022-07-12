<?php

session_start();

require_once("../function.php");
require_once("../db.php");

date_default_timezone_set("Europe/Paris");

if (is_logged()) {
    if (isset($_POST["submit-modif"])) {
        if (isset($_POST["nom-licencie"]) && !empty($_POST["nom-licencie"])) {
            if (isset($_POST["prenom-licencie"]) && !empty($_POST["prenom-licencie"])) {
                if (isset($_POST["dateN-licencie"]) && !empty($_POST["dateN-licencie"])) {
                    if (isset($_POST["categorie-licencie"]) && !empty($_POST["categorie-licencie"])) {
                        if (isset($_POST["sexe-licencie"]) && !empty($_POST["sexe-licencie"])) {
                            if (isset($_POST["idLicencie"]) && !empty($_POST["idLicencie"])) {
                                if (isset($_POST["mail-licencie"]) && !empty($_POST["mail-licencie"]) && filter_var($_POST["mail-licencie"], FILTER_VALIDATE_EMAIL)) {
                                    $nom_licencie = strtoupper(htmlspecialchars($_POST["nom-licencie"]));
                                    $prenom_licencie = ucfirst(htmlspecialchars($_POST["prenom-licencie"]));
                                    $dateN_licencie = $_POST["dateN-licencie"];
                                    $mail_licencie = $_POST["mail-licencie"];
                                    $categorie_licencie = $_POST["categorie-licencie"];
                                    $sexe_licencie = $_POST["sexe-licencie"];
                                    $tel_licencie = $_POST["tel-licencie"];
                                    $current_user = $_SESSION["prenom"] . " " . strtoupper($_SESSION["nom"]);
                                    $current_date = date("Y-m-d H:i:s");
                                    $idLicencie = $_POST["idLicencie"];

                                    if ($_FILES['photo-licencie']['error'] != 4) : //photo renseignée
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
                                                $newImage_width = $image_width / $image_height * 500; //resize height to 500px and keep the same ratio
                                                $newImage_height = $image_height / $image_height * 500; //resize height to 500px

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
                                                        if (isset($_SERVER['HTTP_REFERER'])) {
                                                            header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                                        } else {
                                                            header("location: ../licencies.php");
                                                        }
                                                        create_flash_message("form_picture_error", "Le type de votre image doit être jpeg ou png.", FLASH_ERROR);
                                                        exit();
                                                        break;
                                                }

                                                // image upload sucessfuly.

                                                // if you want to download the original file :
                                                // move_uploaded_file($uploadfile, $uploaddir . $newFileName . "." . $ext); 
                                            } else {
                                                //file size is too big
                                                if (isset($_SERVER['HTTP_REFERER'])) {
                                                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                                } else {
                                                    header("location: ../licencies.php");
                                                }
                                                create_flash_message("form_picture_error", "La photo doit être inférieure à 2Mo.", FLASH_ERROR);
                                                exit();
                                            }
                                        } else {
                                            //possible attack from file upload
                                            if (isset($_SERVER['HTTP_REFERER'])) {
                                                header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                            } else {
                                                header("location: ../licencies.php");
                                            }
                                            create_flash_message("form_picture_error", "Une erreur est survenue, veuillez réessayer", FLASH_ERROR);
                                            exit();
                                        }
                                    endif;

                                    //search and check if the licencie is in bdd and not deleted
                                    $rech_licencie = $db->prepare("SELECT idLicencie FROM licencie WHERE idLicencie = ? AND COSU = 0");
                                    $rech_licencie->bindValue(1, $idLicencie);
                                    $rech_licencie->execute();
                                    if ($rech_licencie->rowCount() > 0) { //licencie is not bdd or is deleted
                                        $req = $db->prepare("UPDATE licencie SET prenom = :prenom, nom = :nom, sexe = :sexe, dateN = :dateN, mail = :mail, idCategorie = :idCategorie, idPhoto = :idPhoto, DMAJ = :DMAJ WHERE idLicencie = :idLicencie");
                                        $req->bindValue("prenom", $prenom_licencie, PDO::PARAM_STR);
                                        $req->bindValue("nom", $nom_licencie, PDO::PARAM_STR);
                                        $req->bindValue("sexe", $sexe_licencie, PDO::PARAM_STR);
                                        $req->bindValue("dateN", $dateN_licencie, PDO::PARAM_STR);
                                        $req->bindValue("mail", $mail_licencie, PDO::PARAM_STR);
                                        $req->bindValue("idCategorie", $categorie_licencie, PDO::PARAM_INT);
                                        $req->bindValue("DMAJ", $current_date);
                                        $req->bindValue("idLicencie", $idLicencie);

                                        if (isset($_POST["tel-licencie"]) && !empty($_POST["tel-licencie"])) {
                                            if (preg_match('/^[0-9]{10}+$/', $_POST["tel-licencie"])) {
                                                $modifTel = $db->prepare("UPDATE tel SET tel = :tel WHERE idLicencie = :idLicencie");
                                                $modifTel->bindValue("tel", $tel_licencie, PDO::PARAM_STR);
                                                $modifTel->bindValue("idLicencie", $idLicencie, PDO::PARAM_STR);
                                                $modifTel->execute();
                                            } else {
                                                if (isset($_SERVER['HTTP_REFERER'])) {
                                                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                                } else {
                                                    header("location: ../licencies.php");
                                                }
                                                create_flash_message("form_tel_error", "Numéro de téléphone invalide.", FLASH_ERROR);
                                                exit();
                                            }
                                        }

                                        if ($_FILES['photo-licencie']['error'] != 4) : //photo renseignée

                                            //add photo on database
                                            $imgPath = "./public/profiles/" . $newFileName . "_resize." . $ext;
                                            $addPhoto = $db->prepare("INSERT INTO photo (imgPath, USRCRE) VALUES (?, ?);");
                                            $addPhoto->bindValue(1, $imgPath);
                                            $addPhoto->bindValue(2, $current_user, PDO::PARAM_STR);
                                            $result_add = $addPhoto->execute();

                                            if (!$result_add) {
                                                if (isset($_SERVER['HTTP_REFERER'])) {
                                                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                                } else {
                                                    header("location: ../licencies.php");
                                                }
                                                create_flash_message("add_photo_error", "Une erreur est survenue, veuillez réessayer.", FLASH_ERROR);
                                                exit();
                                            }

                                            //get the last idPhoto
                                            $getLastPhotoId = $db->prepare("SELECT photo.idPhoto FROM photo ORDER BY photo.idPhoto DESC LIMIT 1");
                                            $getLastPhotoId->execute();
                                            if (!$getLastPhotoId) {
                                                if (isset($_SERVER['HTTP_REFERER'])) {
                                                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                                } else {
                                                    header("location: ../licencies.php");
                                                }
                                                create_flash_message("get_photo_id_error", "Une erreur est survenue, veuillez réessayer.", FLASH_ERROR);
                                                exit();
                                            }
                                            $result_getLastId = $getLastPhotoId->fetch();
                                            $PhotoId = $result_getLastId["idPhoto"];

                                            //delete the old photo
                                            $deletePhoto = $db->prepare("UPDATE photo INNER JOIN licencie ON licencie.idPhoto = photo.idPhoto SET photo.COSU = 1 WHERE idLicencie = :idLicencie AND photo.idPhoto != :idPhoto ");
                                            $deletePhoto->bindValue('idLicencie', $idLicencie, PDO::PARAM_INT);
                                            $deletePhoto->bindValue('idPhoto', $PhotoId, PDO::PARAM_INT);
                                            $result_delete = $deletePhoto->execute();

                                            if (!$result_delete) :
                                                if (isset($_SERVER['HTTP_REFERER'])) {
                                                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                                } else {
                                                    header("location: ../licencies.php");
                                                }
                                                create_flash_message("add_photo_error", "Une erreur est survenue, veuillez réessayer.", FLASH_ERROR);
                                                exit();
                                            endif;
                                        else :
                                            $getCurrentPhotoId = $db->prepare("SELECT photo.idPhoto FROM photo INNER JOIN licencie ON licencie.idPhoto = photo.idPhoto WHERE licencie.idLicencie = :idLicencie ORDER BY photo.idPhoto DESC LIMIT 1");
                                            $getCurrentPhotoId->bindValue('idLicencie', $idLicencie);
                                            $getCurrentPhotoId->execute();
                                            if (!$getCurrentPhotoId) {
                                                if (isset($_SERVER['HTTP_REFERER'])) {
                                                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                                } else {
                                                    header("location: ../licencies.php");
                                                }
                                                create_flash_message("get_photo_id_error", "Une erreur est survenue, veuillez réessayer.", FLASH_ERROR);
                                                exit();
                                            } else {
                                                $result_getCurrentId = $getCurrentPhotoId->fetch();
                                                $PhotoId = $result_getCurrentId["idPhoto"];
                                            }
                                        endif;

                                        $req->bindValue("idPhoto", $PhotoId, PDO::PARAM_INT);

                                        //modif licencie
                                        $result = $req->execute();
                                    } else { //licencie is not bdd or is deleted
                                        header("location: ../licencies.php");
                                        create_flash_message("not_found", "Licencié introuvable.", FLASH_ERROR);
                                        exit();
                                    }

                                    if ($result) {
                                        header("location: ../licencies.php");
                                        create_flash_message("modif_success", "Licencié « $nom_licencie $prenom_licencie » a bien été modifié.", FLASH_SUCCESS);
                                        exit();
                                    } else {
                                        header("location: ../licencies.php");
                                        create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                                        exit();
                                    }
                                } else {
                                    if (isset($_SERVER['HTTP_REFERER'])) {
                                        header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                    } else {
                                        header("location: ../licencies.php");
                                    }
                                    create_flash_message("form_mail_error", "Email invalide.", FLASH_ERROR);
                                    exit();
                                }
                            } else {
                                if (isset($_SERVER['HTTP_REFERER'])) {
                                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                                } else {
                                    header("location: ../licencies.php");
                                }
                                create_flash_message("form_id_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
                                exit();
                            }
                        } else {
                            if (isset($_SERVER['HTTP_REFERER'])) {
                                header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                            } else {
                                header("location: ../licencies.php");
                            }
                            create_flash_message("form_sexe_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                            exit();
                        }
                    } else {
                        if (isset($_SERVER['HTTP_REFERER'])) {
                            header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                        } else {
                            header("location: ../licencies.php");
                        }
                        create_flash_message("form_categorie_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    if (isset($_SERVER['HTTP_REFERER'])) {
                        header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                    } else {
                        header("location: ../licencies.php");
                    }
                    create_flash_message("form_dateN_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                    exit();
                }
            } else {
                if (isset($_SERVER['HTTP_REFERER'])) {
                    header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
                } else {
                    header("location: ../licencies.php");
                }
                create_flash_message("form_firstname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
                exit();
            }
        } else {
            if (isset($_SERVER['HTTP_REFERER'])) {
                header("location:" . $_SERVER['HTTP_REFERER']); //L'adresse de la page qui a conduit le client à la page courante
            } else {
                header("location: ../licencies.php");
            }
            create_flash_message("form_lastname_error", "Veuillez remplir tous les champs.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../licencies.php");
        create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
