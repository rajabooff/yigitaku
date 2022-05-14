<?php
require_once "../../control/access.php";
require_once "../../control/check-validate.php";
require_once "../../control/connect-db.php";
require_once "../../control/return-info.php";

if (originAccess()) {
    $productName = checkValidate($_POST['product_name']);
    $productDescription = checkValidate($_POST['product_description']);

    $allowed = array('jpg', 'jpeg', 'png');
    $return = array();
    if (isset($_FILES['product_file']) and $productName !== '' and $productDescription !== '') {
        $file = $_FILES['product_file'];
        $fileName = $file['name'];
        $fileType = $file['type'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        if (in_array($fileActualExt, $allowed)) {
            if ($file['error'] === 0) {
                if ((($fileSize / 1024) / 1024) <= 5) {
                    $fileNewName = uniqid('', true);
                    $fileD = "../../uploads/products/" . $fileNewName . "." . $fileActualExt;
                    move_uploaded_file($fileTmp, $fileD);
                    $con = connect('yigitaku');
                    $sql = "INSERT INTO products (id, photo, photo_type, name, about) 
                VALUES ('', '$fileNewName', '$fileActualExt', '$productName', '$productDescription')";
                    $con->query($sql);
                    $return['info'] = returnInfo('Yuklandi!', 'success');
                } else {
                    $return['info'] = returnInfo("5 MB dan ortiq rasm yuklab bo'lmaydi!", 'warn');
                }
            } else {
                $return['info'] = returnInfo("Xatolik mavjud! Iltimos boshqatdan urunib ko'ring", "warn");
            }
        } else {
            $return['info'] = returnInfo("Bunday tipdagi rasmni yuklab bo'lmaydi!", 'warn');
        }
    } else {
        $return['info'] = returnInfo("Ma'lumotlar to'ldirilmad!", "warn");
    }
    echo json_encode($return);
}
