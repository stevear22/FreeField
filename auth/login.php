<?php
/*
    This page is the login page for FreeField, and lists all enabled
    authentication providers. Users click on an authentication provider to sign
    in with that provider.
*/

require_once("../includes/lib/global.php");
__require("config");
__require("auth");
__require("i18n");

/*
    If the user is already logged in, they shouldn't be here.
*/
if (Auth::isAuthenticated()) {
    header("HTTP/1.1 307 Temporary Redirect");
    header("Location: ".Config::getEndpointUri("/"));
    exit;
}

/*
    This function returns an array of enabled authentication providers.
*/
$providers = Auth::getEnabledProviders();

/*
    This array defines the appearance of the buttons for each provider. An icon
    is set for each provider.
*/
$providerIcons = array(
    "discord" => "discord",
    "telegram" => "telegram-plane"
);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title><?php echo I18N::resolveArgsHTML(
            "page_title.login.main",
            true,
            Config::get("site/name")
        ); ?></title>
        <link rel="shortcut icon"
              href="../themes/favicon.php?t=<?php
                /*
                    Force refresh the favicon by appending the last changed time
                    of the file to the path. https://stackoverflow.com/a/7116701
                */
                echo Config::getDefinition("themes/meta/favicon")["option"]
                     ->applyToCurrent()->getUploadTime();
              ?>">
        <link rel="stylesheet"
              href="https://unpkg.com/purecss@1.0.0/build/pure-min.css"
              integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w"
              crossorigin="anonymous">
        <link rel="stylesheet"
              href="https://use.fontawesome.com/releases/v5.0.13/css/all.css"
              integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp"
              crossorigin="anonymous">
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="../css/<?php echo Config::getHTML("themes/color/user-settings/theme"); ?>.css">

        <!--[if lte IE 8]>
            <link rel="stylesheet" href="../css/layouts/side-menu-old-ie.css">
        <![endif]-->
        <!--[if gt IE 8]><!-->
            <link rel="stylesheet" href="../css/layouts/side-menu.css">
        <!--<![endif]-->
    </head>
    <body>
        <div id="main">
            <div class="header" style="border-bottom: none; margin-bottom: 50px;">
                <h1><?php echo I18N::resolveHTML("login.title"); ?></h1>
                <h2><?php echo I18N::resolveHTML("login.desc"); ?></h2>
            </div>

            <div class="content">
                <?php foreach ($providers as $provider) { ?>
                    <a href="./oa2/<?php echo $provider; ?>.php" style="text-decoration: none;">
                        <div class="login-button auth-provider-<?php echo $provider; ?>-button">
                            <table><tbody><tr><td>
                                <i class="fab fa-<?php echo $providerIcons[$provider]; ?>"></i>
                            </td><td>
                                <span>
                                    <?php echo I18N::resolveArgsHTML(
                                        "login.perform",
                                        true,
                                        I18N::resolve("admin.section.auth.{$provider}.name")
                                    ); ?>
                                </span>
                            </td></tr></tbody></table>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
        <script src="../js/ui.js"></script>
    </body>
</html>
