<?php header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="statistiques.csv"');

session_start();

require_once("../function.php");
require_once("../db.php");

$req = $db->query('SELECT categorie.nomCategorie, licencie.nom, licencie.prenom, statistiques.nbButs, statistiques.passeD FROM statistiques INNER JOIN licencie ON statistiques.idLicencie = licencie.idLicencie INNER JOIN categorie ON categorie.idCategorie = licencie.idCategorie WHERE licencie.COSU = 0 ORDER BY categorie.idCategorie');

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
