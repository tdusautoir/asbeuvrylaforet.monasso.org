<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
</head>

<body>
    <div id="container">

        <form action="#" method="POST">
            <h1>Connexion</h1>

            <label><b>Nom d'utilisateur</b></label>
            <input type="text" name="name" required>

            <label><b>Mot de passe</b></label>
            <input type="password" name="password" required>

            <input type="submit" id='submit' name='submit' value='LOGIN'>

        </form>
    </div>
</body>

</html>

<?php
$msg = "";
if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $password = $_POST["password"];
    if ($name == '' || $password == '') {
        $msg = "You must enter all fields";
    } else {
        $sql = "SELECT * FROM utilisateur WHERE name = '$name' AND password = '$password'";
        $query = $db->query($sql);

        if ($query === false) {
            echo "Could not successfully run query ($sql) from DB: ";
            exit;
        }

        if ($query) {
            header("Location: main.html");
            exit;
        } else {
            echo "Username and password do not match";
        }
    }
    var_dump($_POST);
}
?>