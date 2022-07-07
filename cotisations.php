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
        <?php if (is_admin() || is_educ()) : ?>
            <?php include('./components/header.php'); ?>
            <div class="container">
                <div class="container-content">
                    <?php include "./components/display_error.php"; ?>
                    <div>
                        <div>
                            <h2>
                                Suivi des cotisations :
                            </h2>
                            <?php
                            $req = $db->prepare("SELECT cotis.methode, cotis.prix, cotis.type, licencie.prenom FROM cotis INNER JOIN licencie ON cotis.idLicencie = licencie.idLicencie WHERE cotis.COSU = 0 ORDER BY cotis.DCRE DESC;"); //Liste des éducateurs classé par date d'ajout
                            $req->execute();
                            $rowCount = $req->rowCount();
                            if ($rowCount > 0) : //if there is most than one cotisation
                            ?>
                                <div>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Prix</th>
                                                <th>Méthode</th>
                                                <th>Type</th>
                                                <th>Licencié</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php while ($COTIS = $req->fetch(PDO::FETCH_ASSOC)) : ?>
                                                <tr>
                                                    <td>
                                                        <?= $COTIS["prix"] ?> €
                                                    </td>
                                                    <td>
                                                        <?php if ($COTIS["methode"] == 1) : echo "Chéque";
                                                        endif; ?>
                                                    </td>
                                                    <td>
                                                        <?= $COTIS["type"] ?>
                                                    </td>
                                                    <td>
                                                        <?= $COTIS["prenom"] ?>
                                                    </td>
                                                    <td class="action-btns btns-1">
                                                        <a href="">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    </td>
                                                    <td class="action-btns btns-2">
                                                        <a href="" onclick="">
                                                            <i class=" fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else : ?>
                                <p> Aucune cotisation n'a encore été récupérée. </p>
                            <?php endif;
                            ?>
                        </div>
                        <div class="return deconnect">
                            <a href="index.php">Retour</a>
                        </div>
                    </div>

                </div>
            </div>
        <?php else :
            create_flash_message(ERROR_PSWD, "Vous ne possédez pas les droits.", FLASH_ERROR); //the user is not admin or educ
            header("location: ./index.php");
            exit();
        endif;
        ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>