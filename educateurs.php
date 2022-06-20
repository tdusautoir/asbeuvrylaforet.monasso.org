<?php
session_start();

require("./function.php");

if (isset($_GET['action']) && !empty($_GET['action']) && $_GET['action'] == "logout") {
    clean_php_session();
    header("location: index.php");
}

?>
<!DOCTYPE html>
<html lang="fr">

<head> <?php require("./components/head.php"); ?>
    <title>Éducateurs - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php include('./components/header.php'); ?>
        <div class="container">
            <div class="container-content">
                <?php include "./components/display_error.php"; ?>
                <div class="edu-container">
                    <div class="edu-li-admin">
                        <h2>
                            Liste des éducateurs :
                        </h2>
                        <?php
                        $req = $db->prepare("CALL PRC_LSTEDU"); //Liste des éducateurs 
                        $req->execute();
                        $rowCount = $req->rowCount();
                        if ($rowCount > 0) : //si on trouve des educateurs ajoutés on affiche la liste de la requete.
                        ?>
                            <div class="educateur-tab">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Adresse mail</th>
                                            <th>Catégorie(s)</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        //Tout récupérer et stocker les lignes dans un array puis le parcourir
                                        //nous permettant d'appeler une procédure pour chaque ligne du tableau
                                        $rows = $req->fetchAll(PDO::FETCH_ASSOC);
                                        $req->closeCursor();
                                        foreach ($rows as $EDUC) :
                                        ?>
                                            <tr>
                                                <td>
                                                    <?= strtoupper($EDUC["nom"]) ?>
                                                </td>
                                                <td>
                                                    <?= ucfirst($EDUC["prenom"]) ?>
                                                </td>
                                                <td>
                                                    <?= $EDUC["mail"] ?>
                                                </td>
                                                <td>
                                                    <?php

                                                    $educId = $EDUC["idEduc"];
                                                    $educCat = "";
                                                    $reqCat = $db->prepare("CALL PRC_GETEDUCAT($educId)"); //Liste des catégories d'un éducateur
                                                    $reqCat->execute();
                                                    $catRowCount = $reqCat->rowCount();

                                                    if ($catRowCount > 0) {
                                                        $row = 1;

                                                        $catRows = $reqCat->fetchAll(PDO::FETCH_ASSOC);
                                                        $reqCat->closeCursor();
                                                        foreach ($catRows as $CAT) {
                                                            if ($row == 1) {
                                                                $educCat = $CAT["nomCategorie"];
                                                                $row++;
                                                            } else {
                                                                //Concaténation dans une chaîne de toutes les catégories 
                                                                $educCat = $educCat . ", " . $CAT["nomCategorie"];
                                                            }
                                                        }
                                                    } else {
                                                        $educCat = "Aucune catégorie attribuée";
                                                    }
                                                    ?>

                                                    <?= $educCat ?>

                                                </td>
                                                <td class="action-btns btns-1">
                                                    <a href="">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                                <td class="action-btns btns-2">
                                                    <a href="#" onclick="displayModal('Modal-<?= $EDUC['idEduc']; ?>')">
                                                        <i class=" fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <div id="Modal-<?= $EDUC["idEduc"]; ?>" class="Modal">
                                                <p>Confirmez la suppression</p>
                                                <div class="modal-button">
                                                    <a href="./functions/educ-delete.php?idEduc=<?= $EDUC["idEduc"] ?>"><i class="fa fa-check"></i></a>
                                                    <a href=" #" onClick="erase('Modal-<?= $EDUC['idEduc']; ?>');"><i class="fa fa-times"></i></a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else : ?>
                            <p> Aucun educateur n'a encore été créé </p>
                        <?php endif;
                        ?>
                    </div>
                    <div class="return deconnect">
                        <a href="index.php">Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <script>
            let input = document.getElementById("photo-licencie");
            let imageName = document.getElementById("nom-photo-licencie")

            if (input) {
                input.addEventListener("change", () => {
                    let inputImage = document.querySelector("input[type=file]").files[0];

                    imageName.innerText = inputImage.name;
                })
            }
        </script>
        <script>
            var amountPerPage = 12;
            var currentPage = 0;

            redraw(currentPage, amountPerPage);

            var pageCount = Math.ceil($(' tr:has(td):not(.hidden)').length / amountPerPage);


            $('table').append('<div class="pagination-div"><ul class="pagination"><li id="table-vorige" class="disabled"><a href="#" aria-label="Vorige"><span aria-hidden="true">&laquo;</span></a></li><li id="table-volgende"><a href="#" aria-label="Volgende"><span aria-hidden="true">&raquo;</span></a></li></ul></div>');

            for (var i = pageCount; i >= 1; i--) {
                var pagenum = $("<li><a href='#'>" + i + "</a></li>");
                $('table.table .pagination #table-vorige').after(pagenum);

                if (i == 1) {
                    $(pagenum).addClass('active');
                }
            }

            $('.pagination li a').click(function() {
                var p = $(this).text();

                if (isNaN(parseInt(p))) {
                    if ($(this).parent().is('.disabled')) {
                        return;
                    }

                    if ($(this).parent().is('#table-volgende')) {
                        var pageNum = currentPage + 1;
                    } else {
                        var pageNum = currentPage - 1;
                    }
                } else {
                    var pageNum = parseInt(p) - 1;
                }

                currentPage = pageNum;

                redraw(pageNum, amountPerPage);
            });

            function redraw(currentPage, amountPerPage) {
                $('#table-vorige').removeClass('disabled');
                $('#table-volgende').removeClass('disabled');

                if (currentPage === 0) {
                    $('#table-vorige').addClass('disabled');
                }

                if (currentPage === pageCount - 1) {
                    $('#table-volgende').addClass('disabled');
                }

                $('.pagination li.active').removeClass('active');
                $('.pagination li:contains("' + (currentPage + 1) + '")').addClass('active');

                var totalCounter = 0;
                $('table.table tr:has(td):not(.hidden)').each(function(cnt, tr) {
                    var start = currentPage * amountPerPage;
                    var end = start + amountPerPage;

                    if (!(totalCounter >= start && totalCounter < end)) {
                        $(this).addClass('row-hidden');
                    } else {
                        $(this).removeClass('row-hidden');
                    }

                    totalCounter++;
                });
            }
        </script>
        <script>
            function displayModal(idModal) {
                document.getElementById(idModal).style.display = "flex";
            }

            function erase(idModal) {
                document.getElementById(idModal).style.display = "none";
            }
        </script>
        <?php else : require "./components/logged.php"; ?><?php endif; ?>
</body>

</html>