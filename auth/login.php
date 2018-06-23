<?php

require_once("../includes/lib/global.php");
__require("config");
__require("auth");
__require("i18n");

if (Auth::isAuthenticated()) {
    header("HTTP/1.1 307 Temporary Redirect");
    header("Location: ".Config::getEndpointUri("/"));
    exit;
}

$providers = Auth::getEnabledProviders();

$providerAppearance = array(
    "discord" => array(
        "fa-icon" => "discord",
        "bg-color" => "#7289DA",
        "color" => "#FFFFFF",
        "width" => "40px",
        "padding" => "0 0 5px 0"
    ),
    "telegram" => array(
        "fa-icon" => "telegram-plane",
        "bg-color" => "#0088CC",
        "color" => "#FFFFFF",
        "width" => "30px",
        "padding" => "5px 5px 10px 5px",
    )
);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <title><?php echo Config::get("site/name"); ?> | <?php echo I18N::resolve("login.title"); ?></title>
        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="../css/<?php echo Config::get("themes/color/admin"); ?>.css">
        
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
                <h1><?php echo I18N::resolve("login.title"); ?></h1>
                <h2><?php echo I18N::resolve("login.desc"); ?></h2>
            </div>

            <div class="content">
                <?php foreach ($providers as $provider) { ?>
                    <a href="./oa2/<?php echo $provider; ?>.php" style="text-decoration: none;">
                        <div style="color: <?php echo $providerAppearance[$provider]["color"]; ?>; text-align: left; border-radius: 5px; margin: 20px auto 0 auto; width: 280px; background-color: <?php echo $providerAppearance[$provider]["bg-color"]; ?>; font-size: 1.3em; padding: 7px 5px;">
                            <table><tbody><tr><td>
                            <i class="fab fa-<?php echo $providerAppearance[$provider]["fa-icon"]; ?>" style="vertical-align: middle; display: inline-block; font-size: 1.5em; margin: 5px 15px 5px 10px;"></i>
                            </td><td>
                            <span>Log in using <?php echo I18N::resolve("admin.section.auth.{$provider}.name"); ?></span>
                            </td></tr></tbody></table>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
        <script src="../js/ui.js"></script>
    </body>
</html>