<?php

if ($_SERVER["HTTP_HOST"] == "localhost") {

    /**
     * CSS MINIFY
     */
    $minCSS = new \MatthiasMullie\Minify\CSS();
    $cssDir = scandir(dirname(__DIR__, 2) . "/views/assets/css");
    foreach ($cssDir as $css) {
        $cssFile = dirname(__DIR__, 2) . "/views/assets/css/{$css}";
        if (is_file($cssFile) && pathinfo($cssFile)["extension"] == "css") {
            $minCSS->add($cssFile);
        }
    }

    $minCSS->minify(dirname(__DIR__, 2) . "/views/assets/style.min.css");

    /**
     * JS MINIFY
     */
    $minJS = new \MatthiasMullie\Minify\JS();
    $minJS->add(dirname(__DIR__,2)."/views/assets/js/jquery.js");
    $minJS->add(dirname(__DIR__,2)."/views/assets/js/jquery-ui.js");

    $minJS->minify(dirname(__DIR__, 2) . "/views/assets/scripts.min.js");
}