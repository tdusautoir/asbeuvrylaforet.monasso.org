<?php header("Content-type: text/css");

require_once("../../db.php");
require_once("../../function.php");

$sql = $db->query("SELECT color FROM settings ORDER BY id DESC LIMIT 1");
$donnees = $sql->fetch(PDO::FETCH_ASSOC);

//#289a37;
?>

/* initialisation des couleurs propre à une association */
:root {
--mainColor: <?= $donnees['color']; ?>;
--secondaryColor: black;
--errorColor: #d20000;
}

/*font family*/
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap');

/*icones*/
.fa,
.fas,
.far,
.fal,
.fad,
.fab {
-moz-osx-font-smoothing: grayscale;
-webkit-font-smoothing: antialiased;
display: inline-block;
font-style: normal;
font-variant: normal;
text-rendering: auto;
line-height: 1;
}

.fa-home:before {
content: "\f015";
}

.fa-plus:before {
content: "\f067";
}

.fa-user-plus:before {
content: "\f234";
}

.fa-cogs:before {
content: "\f085";
}

.fa-user:before {
content: "\f007";
}

.fa-info:before {
content: "\f129";
}

.fa-eye-dropped:before {
content: "\f1fb"
}
.fa-cog:before {
content: "\f013"
}

.fa-users:before {
content: "\f0c0";
}

.fa-euro-sign:before {
content: "\f153";
}

.fa-file-invoice:before {
content: "\f570";
}

.fa-check:before {
content: "\f00c";
}

.fa-times:before {
content: "\f00d"
}

.fa-picture-o:before {
content: "\f03e"
}

.fa-lock:before {
content: "\f023";
}

.fa-bar-chart:before {
content: "\f080";
}

.fa-pencil:before {
content: "\f040";
}

.fa-trash:before {
content: "\f1f8";
}

.fa-angle-down:before {
content: "\f107";
font-weight: 300;
}

.fa-bars:before {
content: "\f0c9";
}

.fa-eye:before {
content: "\f06e";
}

.fa-eye-slash:before {
content: "\f070";
}

.fa-arrow-left:before {
content: "\f060";
padding-right: 5px;
}
.fa-file-pdf:before{
    content: "\f1c1";
}
.fa-search:before {
content: "\f002";
}

.fa-plus:before{
content: "\f067";
}

.fa-minus:before{
content: "\f068";
}

.fa-download:before {
  content: "\f019";
}

.fa-paper-plane:before {
  content: "\f1d8"; }

.fa-sign-out:before {
  content: "\f08b"; }

@font-face {
font-family: 'Font Awesome 5 Pro';
font-style: normal;
font-weight: 300;
font-display: block;
src: url("../webfonts/fa-light-300.eot");
src: url("../webfonts/fa-light-300.eot?#iefix") format("embedded-opentype"), url("../webfonts/fa-light-300.woff2") format("woff2"), url("../webfonts/fa-light-300.woff") format("woff"), url("../webfonts/fa-light-300.ttf") format("truetype"), url("../webfonts/fa-light-300.svg#fontawesome") format("svg");
}

.fal {
font-family: 'Font Awesome 5 Pro';
font-weight: 300;
}

@font-face {
font-family: 'Font Awesome 5 Pro';
font-style: normal;
font-weight: 400;
font-display: block;
src: url("../webfonts/fa-regular-400.eot");
src: url("../webfonts/fa-regular-400.eot?#iefix") format("embedded-opentype"), url("../webfonts/fa-regular-400.woff2") format("woff2"), url("../webfonts/fa-regular-400.woff") format("woff"), url("../webfonts/fa-regular-400.ttf") format("truetype"), url("../webfonts/fa-regular-400.svg#fontawesome") format("svg");
}

.far {
font-family: 'Font Awesome 5 Pro';
font-weight: 400;
}

@font-face {
font-family: 'Font Awesome 5 Pro';
font-style: normal;
font-weight: 900;
font-display: block;
src: url("../webfonts/fa-solid-900.eot");
src: url("../webfonts/fa-solid-900.eot?#iefix") format("embedded-opentype"), url("../webfonts/fa-solid-900.woff2") format("woff2"), url("../webfonts/fa-solid-900.woff") format("woff"), url("../webfonts/fa-solid-900.ttf") format("truetype"), url("../webfonts/fa-solid-900.svg#fontawesome") format("svg");
}

.fa,
.fas {
font-family: 'Font Awesome 5 Pro';
font-weight: 900;
}