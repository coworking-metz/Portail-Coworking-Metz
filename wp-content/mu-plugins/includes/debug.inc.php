<?php


function me()
{
    m(...func_get_args());
    exit;
}
function mse()
{
    print_r(func_get_args());
    exit;
}

function m()
{
    echo '<div style="display:flex;gap:1em;width:100%">';
    foreach (func_get_args() as $arg) {
        echo '<pre style="flex:1;background:#fee100;color:black;padding:1rem">';
        // print_r(debug_backtrace());
        echo    htmlspecialchars(print_r($arg, true));
        echo '</pre>';
    }
    echo '</div>';
}