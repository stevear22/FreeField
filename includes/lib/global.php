<?php

const FF_VERSION = "0.99.1-dev";

function __require($require) {
    switch ($require) {
        case "config":
            include_once(__DIR__."/config.php");
            break;
        case "theme":
            include_once(__DIR__."/theme.php");
            break;
        case "i18n":
            include_once(__DIR__."/i18n.php");
            break;
        case "auth":
            include_once(__DIR__."/auth.php");
            break;
        case "geo":
            include_once(__DIR__."/geo.php");
            break;
        case "db":
            include_once(__DIR__."/db.php");
            break;
        case "xhr":
            include_once(__DIR__."/xhr.php");
            break;
        case "research":
            include_once(__DIR__."/research.php");
            break;
        case "vendor":
            require_once(__DIR__."/../../vendor/autoload.php");
            break;
        case "vendor/sparrow":
            include_once(__DIR__."./../../vendor/mikecao/sparrow/sparrow.php");
            break;
    }
}

?>
