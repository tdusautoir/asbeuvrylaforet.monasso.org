<?php

require_once "db.php";


//LES CONSTANTES
const FLASH = 'FLASH_MESSAGES';
const FORM = 'FORM_INFO';

//flash type
const FLASH_ERROR = 'error';
const FLASH_WARNING = 'warning';
const FLASH_INFO = 'info';
const FLASH_SUCCESS = 'success';

//flash name
const ERROR_PSWD = 'error_pswd';
const ERROR_SECOND_PSWD = 'error_second_pswd';
const ERROR_MAIL = "error_mail";


function create_flash_message(string $name, string $message, string $type): void //creer un flash message
{
    // supprimer le flash message s'il est défini suivant le nom
    if (isset($_SESSION[FLASH][$name])) {
        unset($_SESSION[FLASH][$name]);
    }
    // Ajouter le flash message dans la session
    $_SESSION[FLASH][$name] = ['message' => $message, 'type' => $type];
}

function isset_flash_message_by_name(string $name): bool //Verifier si le flash message est défini via son nom
{
    if (isset($_SESSION[FLASH][$name])) {
        return true;
    } else {
        return false;
    }
}

function isset_flash_message_by_type(string $type): bool //Verifier si le flash message est défini via son type
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) { //parcourir les flashs messages et verifier si le type est défini
            if ($value['type'] == $type) { //si oui, retourner vrai
                return true;
            } else {
                return false;
            }
        }
    }
    return false;
}

function delete_flash_message_by_name(string $name): bool //Supprimer un flash message via son nom
{
    if (isset($_SESSION[FLASH][$name])) {
        unset($_SESSION[FLASH][$name]);
    } else {
        return false;
    }
}


function delete_flash_message_by_type(string $type): bool //Supprimer un flash message via son type
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) { //parcourir les flashs messages et verifier si le type existe
            if ($value['type'] == $type) { //si oui, le supprimer
                unset($_SESSION[FLASH][$key]);
            } else {
                return false;
            }
        }
    }
    return false;
}

function display_flash_message_by_name(string $name): void //Afficher le flash message via son nom
{
    //s'il n'existe pas ne rien renvoyer
    if (!isset($_SESSION[FLASH][$name])) {
        return;
    }

    // recuperer la valeur du message dans une variable
    $flash_message[$name] = $_SESSION[FLASH][$name];

    // supprimer le flash message de la session
    unset($_SESSION[FLASH][$name]);


    echo $flash_message[$name]['message'];
}

function display_flash_message_by_type(string $type): void //Afficher le flash message via son type
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) {  //parcourir les flashs messages et verifier si le type existe
            if ($value['type'] == $type) { //si oui, récupérer le message dans une variable
                $flash_message = $value['message'];

                // supprimer le flash message de la session
                unset($_SESSION[FLASH][$key]);

                // Afficher le flash message
                echo $flash_message;
            }
        }
    }
}

function isset_form(): bool //Verifier si des infos de formulaire sont définies
{
    if (isset($_SESSION[FORM])) {
        return true;
    }
    return false;
}

function add_info_form(string $info_type, string $value): void //Ajouter des infos de formulaire
{
    if (!isset($_SESSION[FORM][$info_type])) {
        unset($_SESSION[FORM][$info_type]);
    }

    // add the info to the session
    $_SESSION[FORM][$info_type] = $value;
}

function isset_info_form(string $info_type): bool //Verifier si une info de formulaire est défini 
{
    if (isset($_SESSION[FORM][$info_type])) {
        return true;
    }
    return false;
}

function display_info_form(string $info_type): void //Afficher l'info de formulaire
{
    //si l'info n'est défini, return
    if (!isset($_SESSION[FORM][$info_type])) {
        return;
    }

    //Recupérer l'info dans une variable
    $form_info = $_SESSION[FORM][$info_type];

    //Afficher l'info
    echo $form_info;
}

function unset_info_form(): bool //Supprimer l'info si elle est défini
{
    if (isset($_SESSION[FORM])) {
        unset($_SESSION[FORM]);
        return true;
    }
    return false;
}

function init_php_session(): bool //Initier la session
{
    if (!session_id()) {
        session_start();
        session_regenerate_id();
        return true;
    }
    return false;
}

function clean_php_session(): void //supprimer la session
{
    session_unset();
    session_destroy();
}

function is_logged(): bool //verifier si l'utilisateur est connecté
{
    if (isset($_SESSION["usermail"])) {
        return true;
    }
    return false;
}

function is_admin(): bool //verifier si l'utilisateur est un admin
{
    if (is_logged()) {
        if (isset($_SESSION["role"]) && $_SESSION["role"] == 3) {
            return true;
        }
    }
    return false;
}

function is_educ(): bool //verifier si l'utilisateur est un educ
{
    if (is_logged()) {
        if (isset($_SESSION["role"]) && $_SESSION["role"] == 2) {
            return true;
        }
    }
    return false;
}

function guidv4($data = null) //generer un identifiant unique universelle
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function dump($arg) //fonction pour debug
{
    echo "<pre>";
    var_dump($arg);
    echo "</pre>";
}

function validateDate($date, $format = 'Y-m-d H:i:s') //verifier si la date est valide selon son format
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function imageResize($newImageWidth, $newImageHeight, $imageSrc, $imageWidth, $imageHeight) //redimensioner une image et la copier dans un dossier
{
    $newImageLayer = imagecreatetruecolor($newImageWidth, $newImageHeight); //créer l'images et ses couleurs selon sa nouvelle hauteur et largeur

    imagecopyresampled($newImageLayer, $imageSrc, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight); //assembler l'image selon sa nouvelle hauteur et largeur et la copier dans un dossier

    return $newImageLayer; //retourne true si l'opération est un succés.
}

function isInteger($input) // Verifier si la valeur d'une valeur est un entier ("23" return true, 23 return true, 23.4 return false)
{
    return (ctype_digit(strval($input)));
}
