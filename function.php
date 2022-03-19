<?php 

function alert($mess) : void{
    if($mess){
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
