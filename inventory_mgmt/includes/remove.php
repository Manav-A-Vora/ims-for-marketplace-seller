<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit();
}

include("_dbconnect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_form_id"])) {
        $delete_form_id = $_POST["delete_form_id"];
        $items_to_delete = NULL;
        $table = "";
        $attribute = "";
        $location = "";

        if ($delete_form_id == "delete_product") {
            $items_to_delete = $_POST["items_array"];
            $table = "products";
            $attribute = "product_name";
            $location = "products";
        } elseif ($delete_form_id == "delete_pkg_mtrl") {
            $items_to_delete = $_POST["items_array"];
            $table = "packaging_material";
            $attribute = "pkg_name";
            $location = "pkg_mtrl";
        } elseif ($delete_form_id == "delete_supplier") {
            $items_to_delete = $_POST["items_array"];
            $table = "suppliers";
            $attribute = "supplier_name";
            $location = "suppliers";
        } else {
            echo "Unknown form id.";
            exit();
        }

        // Convert comma-separated string to array
        $names_array = explode(',', $items_to_delete);

        // Prepare placeholders for the query
        $placeholders = implode(',', array_fill(0, count($names_array), '?'));

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("DELETE FROM $table WHERE $attribute IN ($placeholders)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param(str_repeat('s', count($names_array)), ...$names_array);

        if ($stmt->execute()) {
            // Redirect to appropriate page with success message
            header("location: $location.php?deletion=success");
        } else {
            // Redirect to appropriate page with error message
            header("location: $location.php?deletion=error");
        }

        $stmt->close();
    } else {
        echo "Form id not set";
        exit();
    }
} else {
    echo "Please enter data";
}

$conn->close();
?>
