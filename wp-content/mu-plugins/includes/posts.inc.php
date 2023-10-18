<?php


function get_post_id($data)
{
    $id = is_numeric($data) ? $data : si($data, ['id', 'ID', 'pid', 'term_id']);
    return $id;
}
