<?php
include ("_dbconnect.php");
function insertData($conn, $table, $data){
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $values = array_values($data);

    $sql = "insert into $table ($columns) values ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $type = "";
    foreach ($values as $value) {
        if (is_int($value)) {
            $type .= "i";
        } else if (is_double($value)) {
            $type .= "d";
        } elseif (is_string($value)) {
            $type .= "s";
        } else {
            $type .= "b";
        }
    }

    $stmt->bind_param($type, ...$values);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
function removeData($conn, $table, $field_name, $field_value) {
    $sql = "DELETE FROM `$table` WHERE `$field_name` = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $field_value);

    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $stmt->close();

    return true;
}

function uploadImage($file, $target_dir) {
    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $uploadOk = 1;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return false;
    } else {
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            return false;
        }
    }
}

?>