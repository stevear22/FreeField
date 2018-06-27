<?php

class Theme {
    public static function listIcons() {
        return array(
            "potion", "super_potion", "hyper_potion", "max_potion",
            "revive", "max_revive",
            "fast_tm", "charge_tm",
            "stardust", "rare_candy", "encounter",
            "battle", "raid",
            "catch", "throwing_skill", "hatch",
            "power_up", "evolve",
            "unknown"
        );
    }
    
    public static function listIconSets() {
        $themepath = __DIR__."/../../themes/icons";
        $themedirs = array_diff(scandir($themepath), array('..', '.'));
        $themelist = array();
        foreach ($themedirs as $theme) {
            if (!file_exists("{$themepath}/{$theme}/pack.ini")) continue;
            $themelist[] = $theme;
        }
        return $themelist;
    }
    
    public static function getIconSet($set = null, $variant = null) {
        if ($set === null) {
            __require("config");
            $set = Config::get("themes/icons/default");
        }
        return new IconSet($set, $variant);
    }
}

class IconSet {
    private $set = null;
    private $data = array();
    private $variant = null;
    
    public function __construct($set, $variant) {
        $this->set = $set;
        $this->variant = $variant;
        $packini = __DIR__."/../../themes/icons/{$set}/pack.ini";
        if (file_exists($packini)) {
            $this->data = parse_ini_file($packini, true);
        }
    }
    
    public function getVariant() {
        return $this->variant;
    }
    
    public function setVariant($variant) {
        $this->variant = $variant;
    }
    
    public function getIconUrl($icon) {
        if (isset($this->data["vector"][$icon])) {
            return $this->formatUrl($this->data["vector"][$icon]);
        } else {
            return $this->getRasterUrl($icon);
        }
    }
    
    public function getRasterUrl($icon) {
        if (isset($this->data["raster"][$icon])) {
            return $this->formatUrl($this->data["raster"][$icon]);
        } else {
            return null;
        }
    }
    
    private function formatUrl($url) {
        __require("config");
        $pack = urlencode($this->set);
        if ($this->variant !== null) {
            $url = str_replace("{%variant%}", $this->variant, $url);
        }
        return Config::getEndpointUri("/themes/icons/{$pack}/{$url}");
    }
}

?>