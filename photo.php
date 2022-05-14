<?php
if (isset($_GET)) {
    $image = array();
    foreach ($_GET as $key => $val) {
        $image['url'] = str_replace('/photo/', '', $key);
        $image['url'] = str_replace('_', '.', $image['url']);
    }
    $con  = connect('yigitaku');
    $sql = "select * from products";
    $res = $con->query($sql);
    while ($row = $res->fetch_assoc()) {
        if (file_exists("uploads/products/" . $image['url'] . "."   . $row['photo_type'])) {
            header('Content-Type: image/' . $row['photo_type']);
            readfile("uploads/products/" . $image['url'] . "." . $row['photo_type']);
        }
    }
}
