<?php
function originAccess($host = "http://localhost:3000"): bool
{
    if ($host === $_SERVER['HTTP_ORIGIN']) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header("Access-Control-Allow-Headers: *");
        return true;
    }
    return false;
}
