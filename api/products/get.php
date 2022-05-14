<?php
require_once "../../control/access.php";
require_once "../../control/connect-db.php";

if (originAccess()) {
    $return = array();
    $con = connect();
    $sql = "select * from products";
    $result = $con->query($sql);
    while ($row = $result->fetch_assoc()) {
        $return[] = $row;
    }
    echo json_encode($return);
}
