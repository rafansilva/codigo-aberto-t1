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
define("CONF_MAIL_HOST", "smtp.example.com"); //Set the SMTP server to send through
define("CONF_MAIL_PORT", 587); //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS`
define("CONF_MAIL_USER", "user@example.com"); //SMTP username
define("CONF_MAIL_PASS", "password"); //SMTP password
define("CONF_MAIL_SENDER", ["name" => "yourName", "address" => "your@email.com"]); //Change here the name and email of who will send the email
define("CONF_MAIL_OPTION_DEBUG", 0); //To enable verbose debug output use 2 or 0 to disable
define("CONF_MAIL_OPTION_LANG", "br"); //Your language
define("CONF_MAIL_OPTION_HTML", true); //Set email format to HTML
define("CONF_MAIL_OPTION_AUTH", true); //Enable SMTP authentication
define("CONF_MAIL_OPTION_SECURE", "tls"); //Enable TLS encryption
define("CONF_MAIL_OPTION_CHARSET", "utf-8"); //Default charset is utf-8

/**
 * SOCIAL LOGIN: FACEBOOK
 */
define("FACEBOOK_LOGIN", [
    'clientId' => '',
    'clientSecret' => '',
    'redirectUri' => '',
    'graphApiVersion' => '',
]);

/**
 * SOCIAL LOGIN: GOOGLE
 */
define("GOOGLE_LOGIN", [
    'clientId' => '',
    'clientSecret' => '',
    'redirectUri' => ''
]);


