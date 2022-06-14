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
                <?php if (isset_flash_message_by_name("add_success")) : ?>
                    <p class="add-success"><?php display_flash_message_by_name("add_success"); ?></p>
                <?php endif; ?>
                <div class="li-container">
                    <div class="li-li-admin">
                        <h2>
                            Liste des licenciés :
                        </h2>
                        <?php
                        $req = $db->prepare("SELECT categorie.nomCategorie, licencie.prenom, licencie.nom, licencie.dateN, licencie.mail, licencie.USRCRE FROM `licencie` INNER JOIN categorie ON licencie.idCategorie = categorie.idCategorie ORDER BY licencie.DCRE DESC"); //Derniers licenciés ajoutés classé par date croissant et limités à 10. 
                        $req->execute();
                        $rowCount = $req->rowCount();
                        if ($rowCount > 0) : //si on trouve des licenciés ajoutés on affiche la liste de la requete.
                        ?>
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
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <p> Aucun licencié n'a encore été crée </p>
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
    <?php else : ?> <section class="formulaire_login">
            <form method="POST" action="./functions/login.php" class="form_container">
                <div class="form_content">
                    <div class="logo_association">
                        <img draggable="false" src="./public/images/logo-asb.svg" alt="">
                    </div>
                    <div class="mail">
                        <label for="mail" class="field_label_top">Adresse mail</label>
                        <input id="mail" type="mail" pattern="[^ @]*@[^ @]*" placeholder="Adresse mail" name="email" autocomplete='on' <?php if (isset_flash_message_by_name(ERROR_MAIL)) : ?>style="border-bottom: 2px solid rgb(210, 0, 0);" <?php endif; ?>>
                        <div class="form_field_error_mail" <?php if (isset_flash_message_by_name(ERROR_MAIL)) : ?>style="display: block;" <?php endif; ?>>
                            <span role="alert"> <?php display_flash_message_by_name(ERROR_MAIL); ?> </span>
                        </div>
                    </div>
                    <div class="password">
                        <label for="password" class="field_label_top">Mot de passe</label>
                        <input id="password" type="password" placeholder="Mot de passe" name="password" autocomplete='on' <?php if (isset_flash_message_by_name(ERROR_PSWD)) : ?>style="border-bottom: 2px solid rgb(210, 0, 0);" <?php endif; ?>>
                        <a href="#" role="button" class="view_password_link">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="./resetpw.php" class="forgot_pwd">Mot de passe oublié ?</a>
                        <div class="form_field_error_password" <?php if (isset_flash_message_by_name(ERROR_PSWD)) : ?>style="display: block;" <?php endif; ?>>
                            <span role="alert"> <?php display_flash_message_by_name(ERROR_PSWD); ?> </span>
                        </div>
                    </div>
                    <div class="submit">
                        <button type="submit" name="submit">Se connecter</button>
                    </div>
            </form>
        </section>
        <script src="./public/js/login.js" type="text/javascript" async></script><?php endif; ?>
</body>

</html>