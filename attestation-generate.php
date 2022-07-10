<?php
session_start();

require_once("./function.php");
require_once("./db.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}?>

<?php   
    if (isset($_GET["idLicencie"]) && !empty($_GET["idLicencie"]) && isInteger($_GET["idLicencie"])) {
        $idLicencie = $_GET["idLicencie"];
        $info = $db->prepare("SELECT licencie.nom, licencie.prenom, licencie.dateN, licencie.mail, licencie.sexe, categorie.nomCategorie, cotis.prix, cotis.methode FROM licencie INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN cotis ON cotis.idLicencie = licencie.idLicencie WHERE licencie.idLicencie = ? AND licencie.COSU = 0");
        $info->bindValue(1, $idLicencie);
        $info->execute();
            if ($info->rowCount() > 0) { //search and check if the licencie is in db and not deleted
                $getinfo = $info->fetch(PDO::FETCH_ASSOC);
                $firstname_licencie = $getinfo["prenom"];
                $lastname_licencie = $getinfo["nom"];
                $dateN_licencie = strftime('%d/%m/%Y', strtotime($getinfo["dateN"])); //Y-m-d format to d-m-Y format
                $mail_licencie = $getinfo["mail"];
                $sexe_licencie = $getinfo["sexe"];
                $category_licencie = $getinfo["nomCategorie"];
                $cotisation_prix = $getinfo["prix"];
                if (isset($getinfo["methode"])){
                    if ($getinfo["methode" == 1]){
                        $cotisation_methode = "par chèque";
                    } elseif ($getinfo["methode" == 2]){
                        $cotisation_methode = "en espèces";
                    } elseif ($getinfo["methode" == 3]){
                        $cotisation_methode = "par carte bancaire";
                    }
                } else {
                        $cotisation_methode = "";
                }  
        } else {  //licencie is not in db or is deleted
            header("location: ./licencies.php");
            create_flash_message("not_found", "Licencié introuvable.", FLASH_ERROR);
            exit();
        }
    } else {
        header("location: ./licencies.php");
        create_flash_message("modif_error", "Une erreur est survenue, Veuillez réessayer.", FLASH_ERROR);
        exit();
    } ?>

<?php
// initialistation de la variable date du jour
$today = date('j/m/\2\0y');

//appel à DOMPDF pour la génération de l'attestation au format .PDF
require 'dompdf/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$html = "
<!DOCTYPE html>
<html lang='en'>
<head style='margin:0; padding:0;'>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Attestation de Licence - $firstname_licencie $lastname_licencie - Saison 2022/2023</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        p{
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body style='margin:0; padding:0;'>
<div style='width: 370px;border: 2px solid black;position: absolute;top: 0; margin-bottom:10px;'></div>
<div style='width: 300px;border: 2px solid #289A37;position: absolute;top: 12px;'></div>
<div style='width: 270px;border: 2px solid #289A37;position: absolute;top: 0; top: 300px; right:0;'></div>
<div style='width: 200px;border: 2px solid black;position: absolute;top: 312px; right:0;'></div>
<div style='text-align:center; margin-top:50px;'>
    <img style='width:170px;' src='https://www.dev-asbeuvrylaforet.monasso.org/public/logo/logo-asb.png'>
</div>
<div>
    <h1 style=\"text-align:center; font-family: 'Montserrat', sans-serif; font-size:20px;\">A.S. BEUVRY LA FORÊT</h1>
</div>

<div style='margin-top:100px;'>
<p style=\"font-family: 'Montserrat', sans-serif;\">À Beuvry-la-forêt, le $today,</p>
<p style=\"font-family: 'Montserrat', sans-serif;\"><u>Objet :</u> Attestation de paiement d'une cotisation</p>
</div>

<div style='margin-top:30px;'>
<p style=\"font-family: 'Montserrat', sans-serif;\">Cher&#8226;ère <strong>$firstname_licencie $lastname_licencie, né&#8226;e le $dateN_licencie</strong>,</p>
<p style=\"font-family: 'Montserrat', sans-serif;\">Nous accusons la réception de votre cotisation reçue le $today et nous vous en remercions.</p>
<p style=\"font-family: 'Montserrat', sans-serif;\">Nous reconnaissons que vous avez acquitté la somme de <strong>$cotisation_prix € $cotisation_methode</strong>.</p>
<p style=\"font-family: 'Montserrat', sans-serif;\">Votre adhésion sera donc effective à compter du 1er juillet 2022 jusqu'au 1er juillet 2023.</p>
<p style=\"font-family: 'Montserrat', sans-serif;\">Nous vous prions de recevoir, cher&#8226;ère licencié&#8226;e, nos meilleures salutations.</p>
</div>

<div style='margin-top:50px;'>
<p style=\"font-family: 'Montserrat', sans-serif;\"><strong>Louis DERACHE<br>Président de l'A.S.B.</strong></p>
</div>
<div>
    <img style='width:450px;' src='https://www.dev-asbeuvrylaforet.monasso.org/public/images/signature/CACHETs.png'>
</div>
<div style='width: 270px;border: 2px solid #289A37;position: absolute; bottom: 60px; right:0;'></div>
<div style='width: 200px;border: 2px solid black;position: absolute;bottom: 72px; right:0;'></div>

<div style='position: absolute; bottom: 0; text-align:center; right:0; left:0; margin: 0 auto;'>
    <p style=\"font-family: 'Montserrat', sans-serif; font-size:10px; text-align:center;\"><strong>A.S. BEUVRY LA FORÊT - Complexe Sportif Albert Ricquier, 59310 Beuvry-la-forêt - N° Affiliation FFF : 520837</strong></p>
    <p style=\"font-family: 'Montserrat', sans-serif; font-size:10px; text-align:center;\"><a style='color:#289A37;' href='https://asbeuvrylaforet.fr'>www.asbeuvrylaforet.fr</a> - <a style='color:#289A37;' href='mailto:contact@asbeuvrylaforet.fr'>contact@asbeuvrylaforet.fr</a></p>
</div>
</body>
</html>";

$options = new Options();
$options->setIsRemoteEnabled(true);
$options->set('defaultFont', 'Montserrat');
$dompdf= new Dompdf($options);

$logo = $db->query("SELECT logoPath FROM settings ORDER BY id DESC LIMIT 1;");
$get_logo = $logo->fetch(PDO::FETCH_ASSOC);

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream("Attestation_$firstname_licencie"."_$lastname_licencie", array("Attachment"=>0));

?>
