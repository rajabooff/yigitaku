<?php
function connect($database = 'yigitaku'): mysqli
{
    $connect = new mysqli('localhost', 'root', '', $database);
    if ($connect->connect_error) return $connect->connect_error;
    return $connect;
}
