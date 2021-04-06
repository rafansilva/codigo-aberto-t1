<?php

/**
 * SITE CONFIG
 */
define("SITE", [
    "name" => "Auth em MVC com PHP",
    "desc" => "Aprenda a construir uma aplicação de autenticação em MVC com PHP do Jeito Certo",
    "domain" => "localauth.com",
    "locale" => "pt_BR",
    "root" => "https://localhost/codigoaberto/t1",
]);

/**
 * SITE MINIFY
 */
if ($_SERVER["SERVER_NAME"] == "localhost") {
    require __DIR__ . "/Minify.php";
}

/**
 * DATABASE CONNECT
 */
define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "auth",
    "username" => "root",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

/**
 * PASSWORD
 */
define("PASSWD", [
    "algo" => PASSWORD_DEFAULT,
    "min" => 8,
    "max" => 40
]);

/**
 * SOCIAL
 */
define("SOCIAL", [
    "facebook_page" => "",
    "facebook_author" => "profile.php?id=100053931160706",
    "facebook_appId" => "",
    "twitter_creator" => "",
    "twitter_site" => ""
]);

/**
 * MAIL CONNECT
 */
define("CONF_MAIL_HOST", "smtp.sendgrid.net");
define("CONF_MAIL_PORT", "587");
define("CONF_MAIL_USER", "apikey");
define("CONF_MAIL_PASS", "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");
define("CONF_MAIL_SENDER", ["name" => "Rafael N. Silva", "address" => "rafaelnascimento0505@gmail.com"]);
define("CONF_MAIL_OPTION_DEBUG", 0);
define("CONF_MAIL_OPTION_LANG", "br");
define("CONF_MAIL_OPTION_HTML", true);
define("CONF_MAIL_OPTION_AUTH", true);
define("CONF_MAIL_OPTION_SECURE", "tls");
define("CONF_MAIL_OPTION_CHARSET", "utf-8");

/**
 * SOCIAL LOGIN: FACEBOOK
 */
define("FACEBOOK_LOGIN", []);

/**
 * SOCIAL LOGIN: GOOGLE
 */
define("GOOGLE_LOGIN", []);


