<?php


function me()
{
    m(...func_get_args());
    exit;
}

function m()
{
    foreach (func_get_args() as $arg) {
        echo '<pre style="background:#fee100;color:black;padding:1rem">';
        // print_r(debug_backtrace());
        echo    htmlspecialchars(print_r($arg, true));
        echo '</pre>';
    }
}