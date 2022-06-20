<?php

require_once "db.php";

const FLASH = 'FLASH_MESSAGES';
const FORM = 'FORM_INFO';

//flash type
const FLASH_ERROR = 'error';
const FLASH_WARNING = 'warning';
const FLASH_INFO = 'info';
const FLASH_SUCCESS = 'success';

//flash type name
const ERROR_PSWD = 'error_pswd';
const ERROR_SECOND_PSWD = 'error_second_pswd';
const ERROR_MAIL = "error_mail";


function create_flash_message(string $name, string $message, string $type): void //create a flash message
{
    // remove existing message with the name
    if (isset($_SESSION[FLASH][$name])) {
        unset($_SESSION[FLASH][$name]);
    }
    // add the message to the session
    $_SESSION[FLASH][$name] = ['message' => $message, 'type' => $type];
}

function isset_flash_message_by_name(string $name): bool //check if flash message is isset by his name
{
    if (isset($_SESSION[FLASH][$name])) {
        return true;
    } else {
        return false;
    }
}

function isset_flash_message_by_type(string $type): bool //check if flash message is isset by his type
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) {  //parcours les flashs messages
            if ($value['type'] == $type) {
                return true;
            } else {
                return false;
            }
        }
    }
    return false;
}

function delete_flash_message_by_name(string $name): bool //check if flash message is isset by his name
{
    if (isset($_SESSION[FLASH][$name])) {
        unset($_SESSION[FLASH][$name]);
    } else {
        return false;
    }
}


function delete_flash_message_by_type(string $type): bool //check if flash message is isset by his type
{
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) {  //parcours les flashs messages
            if ($value['type'] == $type) {
                unset($_SESSION[FLASH][$key]);
            } else {
                return false;
            }
        }
    }
    return false;
}

function display_flash_message_by_name(string $name): void
{ //display a flash message by his name
    if (!isset($_SESSION[FLASH][$name])) {
        return;
    }

    // get message from the session
    $flash_message[$name] = $_SESSION[FLASH][$name];

    // delete the flash message
    unset($_SESSION[FLASH][$name]);

    // display the flash message
    echo $flash_message[$name]['message'];
}

function display_flash_message_by_type(string $type): void
{ //display a flash messaye by his type
    if (isset($_SESSION[FLASH])) {
        foreach ($_SESSION[FLASH] as $key => $value) {  //parcours les flashs messages
            if ($value['type'] == $type) {
                $flash_message = $value['message']; // get message from the session

                // delete the flash message
                unset($_SESSION[FLASH][$key]);

                // display the flash message
                echo $flash_message;
            }
        }
    }
}


function isset_form(): bool
{
    if (isset($_SESSION[FORM])) {
        return true;
    }
    return false;
}

function add_info_form(string $info_type, string $value): void
{
    if (!isset($_SESSION[FORM][$info_type])) {
        unset($_SESSION[FORM][$info_type]);
    }

    // add the info to the session
    $_SESSION[FORM][$info_type] = $value;
}

function isset_info_form(string $info_type): bool
{
    if (isset($_SESSION[FORM][$info_type])) {
        return true;
    }
    return false;
}

function display_info_form(string $info_type): void
{
    //display a flash message by his type
    if (!isset($_SESSION[FORM][$info_type])) {
        return;
    }

    // get info from the session
    $form_info = $_SESSION[FORM][$info_type];

    // display the info form
    echo $form_info;
}

function unset_info_form(): bool
{
    if (isset($_SESSION[FORM])) {
        unset($_SESSION[FORM]);
        return true;
    }
    return false;
}

function init_php_session(): bool
{
    if (!session_id()) {
        session_start();
        session_regenerate_id();
        return true;
    }
    return false;
}

function clean_php_session(): void
{
    session_unset();
    session_destroy();
}

function is_logged(): bool
{
    if (isset($_SESSION["usermail"])) {
        return true;
    }
    return false;
}

function is_admin(): bool
{
    if (is_logged()) {
        if (isset($_SESSION["role"]) && $_SESSION["role"] == 3) {
            return true;
        }
    }
    return false;
}

function is_educ(): bool
{
    if (is_logged()) {
        if (isset($_SESSION["role"]) && $_SESSION["role"] == 2) {
            return true;
        }
    }
    return false;
}

function guidv4($data = null)
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

function dump($arg) //function for debug
{
    echo "<pre>";
    var_dump($arg);
    echo "</pre>";
}

function validateDate($date, $format = 'Y-m-d H:i:s') //check if date is valide
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
