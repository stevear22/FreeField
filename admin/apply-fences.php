<?php
/*
    This file handles submission of geofence changes from the administration
    interface.
*/

require_once("../includes/lib/global.php");
__require("config");
__require("auth");
__require("db");
__require("research");
__require("security");

$returnpath = "./?d=fences";

/*
    As this script is for submission only, only POST is supported. If a user
    tries to GET this page, they should be redirected to the configuration UI
    where they can make their desired changes.
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("HTTP/1.1 303 See Other");
    header("Location: {$returnpath}");
    exit;
}

/*
    Perform CSRF validation.
*/
if (!Security::validateCSRF()) {
    header("HTTP/1.1 303 See Other");
    header("Location: {$returnpath}");
    exit;
}

/*
    If the requesting user does not have permission to make changes here, they
    should be kicked out.
*/
if (!Auth::getCurrentUser()->hasPermission("admin/fences/general")) {
    header("HTTP/1.1 303 See Other");
    header("Location: {$returnpath}");
    exit;
}

/*
    The geofences list we get from the configuration file contains a list of
    fences in an indexed array. We'll convert it to an associative array where
    the fence ID is the key, to make it easier to fetch the details for a fence
    given its ID. This is because the updates POSTed from the client contains
    changes where the geofence ID is the identifier for the fences whose
    settings have changed.

    There may not be any geofences defined yet, in which case the returned list
    of fences will be `null`. In that case, create an empty geofences array to
    populate with new fences.
*/
$fencelist = Config::getRaw("geofences");
if ($fencelist === null) $fencelist = array();
$fences = array();
foreach ($fencelist as $fence) {
    $fences[$fence["id"]] = $fence;
}

/*
    Track deleted geofences. If a geofence is deleted, all references to that
    fence should be erased from the configuration file and replaced with null.
*/
$deletions = array();

foreach ($_POST as $postid => $data) {
    /*
        Ensure that the POST field we're working on now is a geofence change
        field. These all have field names in the format "fence_<fenceID>". If
        this matches, extract the geofence ID from the field name.
    */
    if (strlen($postid) < 1 || substr($postid, 0, 6) !== "fence_") continue;
    $fenceid = substr($postid, 6);
    /*
        If a geofence with the given ID does not exist, create a new one and
        populate it with default values.
    */
    if (!isset($fences[$fenceid])) {
        $fences[$fenceid] = array(
            "id" => $fenceid,
            "label" => "",
            "vertices" => array()
        );
    }

    $fence = $fences[$fenceid];

    /*
        Handle actions such as geofence deletion.
    */
    if ($data["action"] === "delete") {
        unset($fences[$fenceid]);
        $deletions[] = $fenceid;
        continue;
    }

    /*
        This is where updates to the geofence fields are handled. Each field
        undergoes validation to make sure that the data that is set is valid
        before it is saved to the configuration file.
    */

    // Geofence label
    if ($fence["label"] !== $data["label"]) {
        $fence["label"] = $data["label"];
    }

    // Geofence constituent vertex coordinates
    $vertices = Geofence::parseVerticesString($data["vertices"]);
    if ($vertices === null) $vertices = array();
    if ($fence["vertices"] !== $vertices) {
        $fence["vertices"] = $vertices;
    }

    /*
        Save the geofence.
    */
    $fences[$fenceid] = $fence;
}

/*
    Also ensure that the configuration is updated to remove all references to
    deleted geofences. To do this, we obtain a flat list of all options
    available in /includes/config/defs.php. We search for settings using
    `GeofenceOption` and change them accordingly.
*/
$configchanges = array();
if (count($deletions) > 0) {
    $keys = Config::listAllKeys();
    /*
        Loop over all settings that are geofences.
    */
    foreach ($keys as $path) {
        if (Config::getOptionType($path) == "GeofenceOption") {
            /*
                Get the value of each setting.
            */
            $value = Config::get($path)->value();
            if ($value !== null) {
                /*
                    Check if the geofence that the setting represents has an ID
                    that is on the list of deleted geofence IDs. If so, set the
                    value of that setting to null.
                */
                foreach ($deletions as $deletedID) {
                    if ($value->getID() == $deletedID) {
                        $configchanges[$path] = null;
                    }
                }
            }
        }
    }
}

/*
    Convert the associative `$fences` array back into an indexed array before
    saving it to the configuration file.
*/
$fencelist = array_values($fences);
Config::set(array_merge(array("geofences" => $fencelist), $configchanges));

header("HTTP/1.1 303 See Other");
header("Location: {$returnpath}");

?>
