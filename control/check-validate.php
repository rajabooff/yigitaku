<?php
function checkValidate($data): string
{
    $data = strip_tags($data);
    $data = htmlentities($data, ENT_QUOTES, "UTF-8");
    return htmlspecialchars($data, ENT_QUOTES);
}