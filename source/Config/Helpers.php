<?php

/**
 * @param string|null $param
 * @return string
 */
function site(string $param = null): string
{
    if ($param && !empty(SITE[$param])) {
        return SITE[$param];
    }

    return SITE["root"];
}

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string
{
    if (!empty(password_get_info($password,)["algo"])) {
        return $password;
    }

    return password_hash($password, PASSWD["algo"]);
}

/**
 * @param string $password
 * @return string
 */
function is_passwd(string $password): string
{
    if (password_get_info($password)['algo'] || (mb_strlen($password) >= PASSWD["min"] && mb_strlen($password) <= PASSWD["max"])) {
        return true;
    }

    return false;
}


/**
 * @param string $path
 * @param bool $time
 * @return string
 */
function asset(string $path, bool $time = true): string
{
    $file = SITE["root"] . "/views/assets/{$path}";
    $fileOnDir = dirname(__DIR__, 2) . "/views/assets/{$path}";

    if ($time && file_exists($fileOnDir)) {
        $file .= "?time=" . filemtime($fileOnDir);
    }

    return $file;
}

/**
 * @param string $imageUrl
 * @return string
 */
function routerImage(string $imageUrl): string
{
    return "https://via.placeholder.com/1200x628.png?text={$imageUrl}";
}

/**
 * @param string|null $type
 * @param string|null $message
 * @return string|null
 */
function flash(string $type = null, string $message = null): ?string
{
    if ($type && $message) {
        $_SESSION["flash"] = [
            "type" => $type,
            "message" => $message
        ];

        return null;
    }

    if (!empty($_SESSION["flash"]) && $flash = $_SESSION["flash"]) {
        unset($_SESSION["flash"]);
        return "<div class=\"message {$flash["type"]}\">{$flash["message"]}</div>";
    }

    return null;
}