<?php 

$mess = NULL;
function alert($mess) : void{
    if($mess && $mess != ''){
        echo "<p class='alert'>".$mess."</p>";
    } 
}

function init_php_session() : bool
{
    if(!session_id()){
        session_start();
        session_regenerate_id();
        return true;
    }
    return false;
}

function clean_php_session() : void{
    session_unset();
    session_destroy();
}

function is_logged() : bool{
    if(isset($_SESSION["usermail"])){
        return true;
    }
    return false;
}

function is_admin() : bool{
    if(is_logged()){ 
        if(isset($_SESSION["role"]) && $_SESSION["role"] == 3){
            return true;
        }
    }
    return false;
}

function is_educ() : bool{
    if(is_logged()){ 
        if(isset($_SESSION["role"]) && $_SESSION["role"] == 2){
            return true;
        }
    }
    return false;
}

function guidv4($data = null) {
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