<?php



function get_field_raw($field_name, $key)
{

    remove_filter('acf_the_content', 'wpautop');

    $ret = get_field($field_name, $key);

    add_filter('acf_the_content', 'wpautop');
    return remove_double_line_breaks($ret);
}

function remove_double_line_breaks($str)
{
    if (is_array($str)) {
        foreach ($str as &$item) {
            $item = remove_double_line_breaks($item);
        }
    } else if (is_string($str)) {
        while (strstr($str, "\r\n\r\n")) {
            $str = str_replace("\r\n\r\n", "\r\n", $str);
        }
    }
    return $str;
}
