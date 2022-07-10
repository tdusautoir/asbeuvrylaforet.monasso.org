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

$dompdf = new Dompdf(array('enable_remote' => true));

$logo = $db->query("SELECT logoPath FROM settings ORDER BY id DESC LIMIT 1;");
$get_logo = $logo->fetch(PDO::FETCH_ASSOC);

//construire l'url de l'image
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $url_logo = "https";
} else {
    $url_logo = "http";
}
$url_logo .= "://";
$url_logo .= $_SERVER['HTTP_HOST'];
$url_logo .= $_SERVER['REQUEST_URI'];
$url_logo .= '/..' . substr($get_logo['logoPath'], 1);

$html = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Attestation de Licence - Saison 2022/2023</title>
    <style>
        img.logo-pdf{
            display: flex;
            width: 200px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<img class='logo-pdf' draggable='false' src='$url_logo'>
<div>
    <h1>A.S. BEUVRY LA FORÊT</h1>
</div>
    
</body>
</html>";

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("monasso.org", array("Attachment" => 0));
