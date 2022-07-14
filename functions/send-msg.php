<?php

use FontLib\Table\Type\head;

session_start();

require_once("../function.php");
require_once("../db.php");
require_once(__DIR__ . '/../vendor/autoload.php');

$config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');

$apiInstance = new SendinBlue\Client\Api\TransactionalSMSApi(
    new GuzzleHttp\Client(),
    $config
);

if (is_admin()) {
    if (isset($_POST['msg-content']) && !empty($_POST['msg-content'])) {
        if (strlen($_POST['msg-content']) <= 140) {
            if (isset($_POST['msg-categorie']) && !empty($_POST['msg-categorie'])) {
                $idCategorie = $_POST['msg-categorie'];

                //verification si le licencie existe et recuperation des numeros de téléphone 
                $req = $db->prepare("SELECT tel.tel FROM tel INNER JOIN licencie ON tel.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE categorie.idCategorie = :idCategorie");
                $req->bindValue(':idCategorie', $idCategorie);
                $req->execute();

                //le licencie existe dans la/les catégorie(s) de l'educateur
                if ($req->rowCount() > 0) {
                    //recuperation des différents tel
                    while ($info = $req->fetch(PDO::FETCH_ASSOC)) {
                        //envoie du message
                        echo "<p>Envoyé le message à " . $info['tel'] . " : </p>";
                        echo "<p>" . $_POST['msg-content'] . "</p>";

                        // $sendTransacSms = new \SendinBlue\Client\Model\SendTransacSms();
                        // $sendTransacSms['sender'] = 'no-Reply-monasso.org';
                        // $sendTransacSms['recipient'] = "+33" . $info['tel'];
                        // $sendTransacSms['content'] = $info['msg-content'];
                        // $sendTransacSms['type'] = 'transactional';
                        // $sendTransacSms['webUrl'] = 'https://www.monasso.org/';

                        // try {
                        //     $result = $apiInstance->sendTransacSms($sendTransacSms);
                        //     print_r($result);
                        // } catch (Exception $e) {
                        //     echo 'Exception when calling TransactionalSMSApi->sendTransacSms: ', $e->getMessage(), PHP_EOL;
                        // }
                    }
                } else {
                    header("location: ../message.php");
                    create_flash_message("unknown_licencie", "Aucun numéro n'a été trouvé dans cette categorie.", FLASH_ERROR);
                    exit();
                }
            } else {
                if (isset($_POST['msg-name']) && !empty($_POST['msg-name'])) {
                    $name_licencie = $_POST['msg-name'];

                    //verification si le licencie existe et recuperation de son numero de téléphone
                    $req = $db->prepare("SELECT tel.tel FROM tel INNER JOIN licencie ON tel.idLicencie = licencie.idLicencie WHERE licencie.nom = :nomLicencie");
                    $req->bindValue(':nomLicencie', $name_licencie);
                    $req->execute();

                    //le licencie existe  dans la/les catégorie(s) de l'educateur
                    if ($req->rowCount() > 0) {

                        //recuperation du tel
                        $info = $req->fetch(PDO::FETCH_ASSOC);

                        //envoie du message
                        echo "<p>Envoyé le message à " . $info['tel'] . " : </p>";
                        echo "<p>" . $_POST['msg-content'] . "</p>";

                        // $sendTransacSms = new \SendinBlue\Client\Model\SendTransacSms();
                        // $sendTransacSms['sender'] = 'no-Reply-monasso.org';
                        // $sendTransacSms['recipient'] = "+33".$info['tel'];
                        // $sendTransacSms['content'] = $info['content'];
                        // $sendTransacSms['type'] = 'transactional';
                        // $sendTransacSms['webUrl'] = 'https://www.monasso.org/';

                        // try {
                        //     $result = $apiInstance->sendTransacSms($sendTransacSms);
                        //     print_r($result);
                        // } catch (Exception $e) {
                        //     echo 'Exception when calling TransactionalSMSApi->sendTransacSms: ', $e->getMessage(), PHP_EOL;
                        // }
                    } else {
                        header("location: ../message.php");
                        create_flash_message("unknown_licencie", "Aucun numéro n'a été trouvé.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../message.php");
                    create_flash_message("form_error", "Veuillez renseigner un nom ou une catégorie.", FLASH_ERROR);
                    exit();
                }
            }
        } else {
            header("location: ../message.php");
            create_flash_message("form_error", "Votre contenu doit être inférieur à 140 caractères.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../message.php");
        create_flash_message("form_error", "Veuillez écrire du contenu", FLASH_ERROR);
        exit();
    }
} elseif (is_educ()) {
    if (isset($_POST['msg-content']) && !empty($_POST['msg-content'])) {
        if (strlen($_POST['msg-content']) <= 140) {
            if (isset($_POST['msg-categorie']) && !empty($_POST['msg-categorie'])) {
                $categorie = $_POST['msg-categorie'];

                //verification si le licencie existe et recuperation des numeros de téléphone 
                $req = $db->prepare("SELECT tel.tel FROM tel INNER JOIN licencie ON tel.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE categorie.idCategorie = :idCategorie");
                $req->bindValue(':idCategorie', $categorie);
                $req->execute();

                //le licencie existe  dans la/les catégorie(s) de l'educateur
                if ($req->rowCount() > 0) {

                    //recuperation des différents tel
                    while ($info = $req->fetch(PDO::FETCH_ASSOC)) {
                        //envoie du message
                        echo "<p>Envoyé le message à " . $info['tel'] . " : </p>";
                        echo "<p>" . $_POST['msg-content'] . "</p>";


                        // $sendTransacSms = new \SendinBlue\Client\Model\SendTransacSms();
                        // $sendTransacSms['sender'] = 'no-Reply-monasso.org';
                        // $sendTransacSms['recipient'] = "+33".$info['tel'];
                        // $sendTransacSms['content'] = $info['content'];
                        // $sendTransacSms['type'] = 'transactional';
                        // $sendTransacSms['webUrl'] = 'https://www.monasso.org/';

                        // try {
                        //     $result = $apiInstance->sendTransacSms($sendTransacSms);
                        //     print_r($result);
                        // } catch (Exception $e) {
                        //     echo 'Exception when calling TransactionalSMSApi->sendTransacSms: ', $e->getMessage(), PHP_EOL;
                        // }
                    }
                } else {
                    header("location: ../message.php");
                    create_flash_message("unknown_licencie", "Licencie introuvable", FLASH_ERROR);
                    exit();
                }
            } else {
                if (isset($_POST['msg-name']) && !empty($_POST['msg-name'])) {
                    $name_licencie = $_POST['msg-name'];

                    //verification si le licencie existe dans la/les catégorie(s) de l'educateur et recuperation de son numero de téléphone
                    $req = $db->prepare("SELECT tel.tel FROM tel INNER JOIN licencie ON tel.idLicencie = licencie.idLicencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE categorieeduc.idEduc = :idEduc AND licencie.nom = :nomLicencie");
                    $req->bindValue(':idEduc', $_SESSION['id']);
                    $req->bindValue(':nomLicencie', $name_licencie);
                    $req->execute();

                    //le licencie existe  dans la/les catégorie(s) de l'educateur
                    if ($req->rowCount() > 0) {

                        //recuperation du tel
                        $info = $req->fetch(PDO::FETCH_ASSOC);

                        //envoie du message
                        echo "<p>Envoyé le message à " . $info['tel'] . " : </p>";
                        echo "<p>" . $_POST['msg-content'] . "</p>";

                        // $sendTransacSms = new \SendinBlue\Client\Model\SendTransacSms();
                        // $sendTransacSms['sender'] = 'no-Reply-monasso.org';
                        // $sendTransacSms['recipient'] = "+33".$info['tel'];
                        // $sendTransacSms['content'] = $info['content'];
                        // $sendTransacSms['type'] = 'transactional';
                        // $sendTransacSms['webUrl'] = 'https://www.monasso.org/';

                        // try {
                        //     $result = $apiInstance->sendTransacSms($sendTransacSms);
                        //     print_r($result);
                        // } catch (Exception $e) {
                        //     echo 'Exception when calling TransactionalSMSApi->sendTransacSms: ', $e->getMessage(), PHP_EOL;
                        // }
                    } else {
                        header("location: ../message.php");
                        create_flash_message("unknown_licencie", "Aucun numéro n'a été trouvé dans cette categorie.", FLASH_ERROR);
                        exit();
                    }
                } else {
                    header("location: ../message.php");
                    create_flash_message("form_error", "Veuillez renseigner un nom ou une catégorie.", FLASH_ERROR);
                    exit();
                }
            }
        } else {
            header("location: ../message.php");
            create_flash_message("form_error", "Votre contenu doit être inférieur à 140 caractères.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ../message.php");
        create_flash_message("form_error", "Veuillez écrire du contenu", FLASH_ERROR);
        exit();
    }
} else {
    header("location: ../message.php");
    create_flash_message("no_rights", "Vous n'avez pas les droits.", FLASH_ERROR);
    exit();
}
