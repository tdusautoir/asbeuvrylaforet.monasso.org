<?php header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="statistiques.csv"');

session_start();

require_once("../function.php");
require_once("../db.php");

if (is_educ()) {
    $idEduc = $_SESSION['id'];
    $req = $db->query("SELECT categorie.nomCategorie, licencie.nom, licencie.prenom, statistiques.nbButs, statistiques.passeD FROM statistiques INNER JOIN licencie ON statistiques.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie INNER JOIN categorieeduc ON categorie.idCategorie = categorieeduc.idCategorie WHERE licencie.COSU = 0 AND categorieeduc.idEduc = $idEduc ORDER BY categorie.idCategorie");
} elseif (is_admin()) {
    $req = $db->query('SELECT categorie.nomCategorie, licencie.nom, licencie.prenom, statistiques.nbButs, statistiques.passeD FROM statistiques INNER JOIN licencie ON statistiques.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie WHERE licencie.COSU = 0 ORDER BY categorie.idCategorie');
} else {
    create_flash_message("no_rights", "Vous ne possédez pas les droits.", FLASH_ERROR); //the user is not admin or educ
    header("location: ../statistiques.php");
    exit();
}

$tabStat = [];
$tabStat[] = ['CATEGORIE', 'NOM', 'PRENOM', 'BUTS', 'PASSED'];
$tabStat[] = ['', '', '', '', ''];

while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
    $tabStat[] = [$data['nomCategorie'], $data['nom'], $data['prenom'], $data['nbButs'], $data['passeD']];
}

foreach ($tabStat as $ligne) {
    echo implode(";", $ligne);
    echo "\n";
}
