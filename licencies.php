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

                <div class="filter">
                    <div>
                        <form method="GET">
                            <label for="category">Catégorie du licencié</label>
                            <select name='categorie' onChange="submit()" class="filter-category">
                                <option disabled <?php if (!isset($_GET['categorie']) || empty($_GET['categorie'])) : ?>selected<?php endif; ?>>Categorie</option>
                                <?php
                                if (is_admin()) :
                                    $getCategorie = $db->query("SELECT DISTINCT nomCategorie FROM categorie WHERE COSU = 0");
                                    if ($getCategorie->rowCount() > 0) :
                                        while ($categorie = $getCategorie->fetch()) : ?>
                                            <option value="<?= $categorie['nomCategorie']; ?>" <?php if (isset($_GET['categorie']) && $categorie['nomCategorie'] == $_GET['categorie']) : ?>selected<?php endif; ?>><?= $categorie['nomCategorie']; ?></option>
                                        <?php endwhile;
                                    endif;
                                elseif (is_educ()) :
                                    $getCategorie = $db->prepare(" SELECT categorie.nomCategorie FROM `categorieeduc` INNER JOIN categorie ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE educ.idEduc = :idEduc");
                                    $getCategorie->bindValue("idEduc", $_SESSION['id']);
                                    $getCategorie->execute();
                                    if ($getCategorie->rowCount() > 0) :
                                        while ($categorie = $getCategorie->fetch()) : ?>
                                            <option value="<?= $categorie['nomCategorie']; ?>" <?php if (isset($_GET['categorie']) && $categorie['nomCategorie'] == $_GET['categorie']) : ?>selected<?php endif; ?>><?= $categorie['nomCategorie']; ?></option>
                                <?php endwhile;
                                    endif;
                                endif;
                                ?>
                            </select>
                        </form>
                    </div>
                    <div>
                        <form method="GET">
                            <label for="q">Tapez le nom du licencié</label>
                            <input type="search" name="q" placeholder="Recherche..." />
                            <input type="submit" value="" />
                        </form>
                    </div>
                </div>

                <?php if (isset($_GET) && !empty($_GET)) : ?>
                    <a href="./licencies.php" class="cancel-filter">Annuler les filtres</a>
                <?php endif; ?>

                <div class="li-container">
                    <div class="li-content">
                        <h2>
                        &#x1F3C3; Liste des licenciés :
                        </h2>
                        <?php
                        if (is_admin()) :
                            if (isset($_GET['q']) && !empty($_GET['q'])) :
                                $q_ = explode(' ', $_GET['q']); //take only the first word
                                $q = $q_[0];
                                $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 AND licencie.nom LIKE '%$q%' ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la recherche q
                                $req->execute();
                                $rowCount = $req->rowCount();
                            elseif (isset($_GET['categorie']) && !empty($_GET['categorie']) && $_GET['categorie'] != '') :
                                $categorie = $_GET['categorie'];
                                $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 AND categorie.nomCategorie = :categorie ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la categorie
                                $req->bindValue('categorie', $categorie);
                                $req->execute();
                                $rowCount = $req->rowCount();
                            else :
                                $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie WHERE licencie.COSU = 0 ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd classé par date croissant 
                                $req->execute();
                                $rowCount = $req->rowCount();
                            endif;
                        elseif (is_educ()) :
                            if (isset($_GET['q']) && !empty($_GET['q'])) :
                                $q_ = explode(' ', $_GET['q']); //take only the first word
                                $q = $q_[0];
                                $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND licencie.nom LIKE '%$q%' ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la recherche q et les catégories associés à l'educateur connecté
                                $req->bindValue('idEduc', $_SESSION['id']);
                                $req->execute();
                                $rowCount = $req->rowCount();
                            elseif (isset($_GET['categorie']) && !empty($_GET['categorie']) && $_GET['categorie'] != '') :
                                $categorie = $_GET['categorie'];
                                $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc AND categorie.nomCategorie = :categorie ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon la recherche q et les catégories associés à l'educateur connecté
                                $req->bindValue('idEduc', $_SESSION['id']);
                                $req->bindValue('categorie', $categorie);
                                $req->execute();
                                $rowCount = $req->rowCount();
                            else :
                                $req = $db->prepare("SELECT licencie.idLicencie, categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie INNER JOIN categorieeduc ON categorieeduc.idCategorie = categorie.idCategorie INNER JOIN educ ON educ.idEduc = categorieeduc.idEduc WHERE licencie.COSU = 0 AND educ.idEduc = :idEduc ORDER BY licencie.DCRE DESC;"); //licenciés de la bdd selon les catégories associés à l'educateur connecté 
                                $req->bindValue('idEduc', $_SESSION['id']);
                                $req->execute();
                                $rowCount = $req->rowCount();
                            endif;
                        endif;

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
                                            <th>Téléphone</th>
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
                                                <td title=<?= strtoupper(htmlspecialchars($LIC["nom"])) ?>>
                                                    <?= strtoupper(htmlspecialchars($LIC["nom"])) ?>
                                                </td>
                                                <td title=<?= ucfirst(htmlspecialchars($LIC["prenom"])) ?>>
                                                    <?= ucfirst(htmlspecialchars($LIC["prenom"])) ?>
                                                </td>
                                                <td>
                                                    <?= strftime('%d-%m-%Y', strtotime($LIC["dateN"])); //Y-m-d format to d-m-Y format 
                                                    ?>
                                                </td>
                                                <td>
                                                    <?= $LIC["mail"] ?>
                                                </td>
                                                <td>
                                                    <?php $getTel = $db->prepare("SELECT tel.tel FROM tel WHERE tel.idLicencie = ? AND tel.COSU = 0");
                                                    $getTel->bindValue(1, $LIC['idLicencie']);
                                                    $getTel->execute();
                                                    if ($getTel->rowCount() > 0) :
                                                        $result_getTel = $getTel->fetch(PDO::FETCH_ASSOC);
                                                        echo wordwrap($result_getTel["tel"], 2, " ", 1); //0000000000 to 00 00 00 00 00
                                                    endif; ?>
                                                </td>
                                                <td>
                                                    <?php $getCotis = $db->prepare("SELECT cotis.prix, cotis.etat FROM cotis WHERE cotis.idLicencie = ? AND cotis.COSU = 0");
                                                    $getCotis->bindValue(1, $LIC['idLicencie']);
                                                    $getCotis->execute();
                                                    if ($getCotis->rowCount() > 0) :
                                                        $Cotis = $getCotis->fetch(PDO::FETCH_ASSOC);
                                                        if ($Cotis["etat"] == 1) : ?>
                                                            <span title="non réglée" class="state-indicator" style="background-color: red;"></span>
                                                        <?php elseif ($Cotis["etat"] == 2) : ?>
                                                            <span title="réglée" class="state-indicator" style="background-color: orange;"></span>
                                                        <?php elseif ($Cotis["etat"] == 3) : ?>
                                                            <span title="non encaissée" class="state-indicator" style="background-color: white; border: 1px solid green;"></span>
                                                        <?php elseif ($Cotis["etat"] == 4) : ?>
                                                            <span title="encaissée" class="state-indicator" style="background-color: green;"></span>
                                                    <?php endif;
                                                        echo $Cotis["prix"] . " €";
                                                    endif; ?>
                                                </td>
                                                <td class="action-btns">
                                                    <?php $getPhoto = $db->prepare("SELECT licencie.idPhoto, photo.imgPath FROM licencie INNER JOIN photo ON licencie.idPhoto = photo.idPhoto WHERE licencie.idLicencie = ? AND photo.COSU = 0");
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
                        <?php elseif (isset($_GET) && !empty($_GET)) : ?>
                            <p> Aucun licencié ne correspond à votre recherche. </p>
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