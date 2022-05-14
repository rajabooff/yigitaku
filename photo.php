<?php
require_once 'func.php';

if (isset($_GET)) {
    $image = array();
    foreach ($_GET as $key => $val) {
        $image['url'] = str_replace('/photo/', '', $key);
        $image['url'] = str_replace('_', '.', $image['url']);
    }
    $con  = connect('maksudov');
    $sql = "select * from blogs";
    $res = $con->query($sql);
    while ($row = $res->fetch_assoc()) {
        if (file_exists("uploads/blogs/" . $image['url'] . "."   . $row['image_type'])) {
            header('Content-Type: image/' . $row['image_type']);
            readfile("uploads/blogs/" . $image['url'] . "." . $row['image_type']);
        }else{
        }
    }
} else {
    header("Location: index.php");
}
