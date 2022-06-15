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
    <title>Espace Licencié - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php include('./components/header.php'); ?>
        <div class="container">
            <div class="container-content">
                <?php if (isset_flash_message_by_name("delete_success")) : ?>
                    <div class="add-success"><?php display_flash_message_by_name("delete_success"); ?></div>
                <?php elseif (isset_flash_message_by_name("delete_error")) : ?>
                    <div class="add-error"><?php display_flash_message_by_name("delete_error"); ?></div>
                <?php endif; ?>
                <div class="li-container">
                    <div class="li-li-admin">
                        <h2>
                            Liste des licenciés :
                        </h2>
                        <?php
                        $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY licencie.DCRE DESC;"); //Derniers licenciés ajoutés classé par date croissant et limités à 10. 
                        $req->execute();
                        $rowCount = $req->rowCount();
                        if ($rowCount > 0) : //si on trouve des licenciés ajoutés on affiche la liste de la requete.
                        ?>
                            <div class="licencie-tab">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                            <th>Naissance</th>
                                            <th>Adresse mail</th>
                                            <th>Création</th>
                                            <th>Cotisation</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php while ($LIC = $req->fetch(PDO::FETCH_ASSOC)) : ?>
                                            <tr>
                                                <td>
                                                    <?= $LIC["nomCategorie"] ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["nom"] ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["prenom"] ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["dateN"] ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["mail"] ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["USRCRE"] ?>
                                                </td>
                                                <td>

                                                </td>
                                                <td>
                                                    <a href="./modif-licencie.php">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="#" onClick="print();">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <div id="Modal">
                                                <p>Confirmez la suppression</p>
                                                <div class="modal-button">
                                                    <a href="./functions/licencie-delete.php?idLicencie=<?= $LIC["idLicencie"] ?>">Oui</a>
                                                    <a href=" #" onClick="erase();">Non</a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else : ?>
                            <p> Aucun licencié n'a encore été créé </p>
                        <?php endif; ?>
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
            function print() {
                document.getElementById("Modal").style.display = "flex";
            }

            function erase() {
                document.getElementById("Modal").style.display = "none";
            }
        </script>
        <?php else : require "./components/logged.php"; ?><?php endif; ?>
</body>

</html>