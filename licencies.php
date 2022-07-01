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
    <title>Licenciés - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php include('./components/header.php'); ?>
        <div class="container">
            <div class="container-content">
                <?php include "./components/display_error.php"; ?>
                <div class="li-container">
                    <div class="li-li-admin">
                        <h2>
                            Liste des licenciés :
                        </h2>
                        <?php
                        $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd classé par date croissant 
                        $req->execute();
                        $rowCount = $req->rowCount();
                        if ($rowCount > 0) : //si on trouve des licenciés ajoutés on affiche la liste de la requete.
                        ?>
                            <div class="licencie-tab">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
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
                                                <td title=<?= strtoupper($LIC["nom"]) ?>>
                                                    <?= strtoupper($LIC["nom"]) ?>
                                                </td>
                                                <td title=<?= ucfirst($LIC["prenom"]) ?>>
                                                    <?= ucfirst($LIC["prenom"]) ?>
                                                </td>
                                                <td>
                                                    <?= strftime('%d-%m-%Y', strtotime($LIC["dateN"])); //Y-m-d format to d-m-Y format 
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["mail"] ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["USRCRE"] ?>
                                                </td>
                                                <td>
                                                </td>
                                                <td class="action-btns">
                                                    <?php $getPhoto = $db->prepare("SELECT licencie.idPhoto, photo.imgPath FROM licencie INNER JOIN photo ON licencie.idPhoto = photo.idPhoto WHERE licencie.idLicencie = ?");
                                                    $getPhoto->bindValue(1, $LIC['idLicencie']);
                                                    $getPhoto->execute();
                                                    if ($getPhoto->rowCount() > 0) :
                                                        $result_getPhoto = $getPhoto->fetch(PDO::FETCH_ASSOC);
                                                        $imgPath = $result_getPhoto["imgPath"]; ?>
                                                        <a href="#" onclick="displayModalPhoto('<?= $imgPath ?>')">
                                                            <i class=" fa fa-picture-o"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action-btns btns-1">
                                                    <a href="./modif-licencie.php?idLicencie=<?= $LIC["idLicencie"] ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                </td>
                                                <td class="action-btns btns-2">
                                                    <a href="#" onclick="displayModalDelete('<?= $LIC['idLicencie']; ?>')">
                                                        <i class=" fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else : ?>
                            <p> Aucun licencié n'a encore été créé </p>
                        <?php endif;
                        $req->closeCursor(); // Ferme le curseur, permettant à la requête d'être de nouveau exécutée 
                        ?>
                    </div>
                    <div class="return deconnect">
                        <a href="index.php">Retour</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="Modal">
            <div id="Modal-photo" class="Modal-photo">
                <div class="modal-photo-icon-close">
                    <i class="fa fa-times" onclick="erase()"></i>
                </div>
                <div class="Modal-image">
                    <img id="img-licencie" alt="image de licencie">
                </div>
            </div>
            <div class="Modal-delete" id="Modal-delete">
                <p>Confirmez la suppression</p>
                <div class="modal-button">
                    <a id="valid-btn"><i class="fa fa-check"></i></a>
                    <a id="erase-btn" onclick="erase()"><i class="fa fa-times"></i></a>
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
            let Modal = document.getElementById("Modal")
            let ModalDelete = document.getElementById("Modal-delete");
            let ModalPhoto = document.getElementById("Modal-photo");
            let imgLicencie = document.getElementById("img-licencie");

            function displayModalDelete(idLicencie) {
                let validBtn = document.getElementById("valid-btn");

                document.body.style.overflow = "hidden";
                Modal.style.display = "block";
                ModalDelete.style.display = "flex";
                validBtn.setAttribute("href", "./functions/licencie-delete.php?idLicencie=" + idLicencie);
            }

            function displayModalPhoto(imgPath) {
                imgLicencie.setAttribute("src", imgPath);
                document.body.style.overflow = "hidden";
                Modal.style.display = "block";
                ModalPhoto.style.display = "flex";
            }

            function erase() {
                document.body.style.overflow = "visible";
                Modal.style.display = "none";
                ModalDelete.style.display = "none";
                ModalPhoto.style.display = "none";
            }

            //When the user pressed escape close the modal
            document.onkeydown = function(e) {
                if (e.key === "Escape" || e.key === "Esc") {
                    erase();
                }
            }

            // When the user clicks anywhere outside of the modal content, close it
            Modal.addEventListener("click", function(event) {
                if (event.target != imgLicencie && event.target != ModalDelete) {
                    erase();
                }
            })
        </script>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>