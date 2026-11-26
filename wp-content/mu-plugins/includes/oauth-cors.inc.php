<?php
// CORS pour /oauth/me (SSO de l app Ecrans, implicit flow)
add_action("init", function () {
    $uri = $_SERVER["REQUEST_URI"] ?? "";
    if (strpos($uri, "/oauth/me") === false) return;
    $allowed = ["http://localhost:5174", "http://127.0.0.1:5174", "http://localhost:5173", "https://ecrans.coworking-metz.fr", "https://ecrans.pages.dev", "https://v2.ecrans.pages.dev", "https://master.ecrans.pages.dev"];
    $origin = $_SERVER["HTTP_ORIGIN"] ?? "";
    if (!in_array($origin, $allowed, true)) return;
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Headers: Authorization, Content-Type");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Vary: Origin");
    if (($_SERVER["REQUEST_METHOD"] ?? "") === "OPTIONS") { status_header(204); exit; }
}, 0);

// Avatar de l utilisateur dans /oauth/me
add_filter("wo_me_resource_return", function ($me, $token) {
    if (!empty($me["ID"])) $me["avatar_url"] = get_avatar_url((int) $me["ID"], ["size" => 96]);
    return $me;
}, 10, 2);
