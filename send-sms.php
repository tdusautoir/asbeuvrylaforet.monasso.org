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
    <title>Envoyer un SMS - A.S. BEUVRY LA FORÊT</title>
</head>

<body>
    <?php if (is_logged()) : ?>
        <?php if (is_admin() || is_educ()) : ?>
            <div class="content">
                <?php include('./components/header.php'); ?>
                <div class="container">
                    <div class="container-content">
                        <?php include "./components/display_error.php"; ?>

                        <?php
                            # ------------------
                            # Create a campaign\
                            # ------------------
                            # Include the Sendinblue library\
                            require_once(__DIR__ . "/APIv3-php-library/autoload.php");
                            # Instantiate the client\
                            SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey("api-key", "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");
                            $api_instance = new SendinBlue\Client\Api\EmailCampaignsApi();
                            $emailCampaigns = new \SendinBlue\Client\Model\CreateEmailCampaign();
                            # Define the campaign settings\
                            $email_campaigns['name'] = "Campaign sent via the API";
                            $email_campaigns['subject'] = "My subject";
                            $email_campaigns['sender'] = array("name": "From name", "email":"contact@asbeuvrylaforet.fr");
                            $email_campaigns['type'] = "classic";
                            # Content that will be sent\
                            "htmlContent"=> "Congratulations! You successfully sent this example campaign via the Sendinblue API.",
                            # Select the recipients\
                            "recipients"=> array("listIds"=> [2, 7]),
                            # Schedule the sending in one hour\
                            "scheduledAt"=> "2018-01-01 00:00:01"
                            );
                            # Make the call to the client\
                            try {
                            $result = $api_instance->createEmailCampaign($emailCampaigns);
                            print_r($result);
                            } catch (Exception $e) {
                            echo 'Exception when calling EmailCampaignsApi->createEmailCampaign: ', $e->getMessage(), PHP_EOL;
                            }
                        ?>

            <?php require './components/footer.php'; ?>
        <?php else :
            create_flash_message(ERROR_PSWD, "Vous ne possédez pas les droits.", FLASH_ERROR); //the user is not admin or educ
            header("location: ./index.php");
            exit();
        endif;
        ?>
        <?php else : require "./components/form_login.php"; ?><?php endif; ?>
</body>

</html>