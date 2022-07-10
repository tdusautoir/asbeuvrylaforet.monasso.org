<?php
session_start();

require_once("./function.php");
require_once("./db.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}


require 'dompdf/vendor/autoload.php';
use Dompdf\Dompdf;

$dompdf= new Dompdf();

$dompdf->loadHtml('<h1>Hello World!</h1>');

$dompdf->render();

$dompdf->stream("monasso.org", array("Attachment"=>0));


?>