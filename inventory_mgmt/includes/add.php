<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit();
}

include ("_dbconnect.php");
include ("functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["form_id"])) {
        $form_id = $_POST["form_id"];
        $data = [];
        $table = "";
        $uploadOk = 1;
        $target_dir = "../assets/";

        // Function to handle image upload
        function handleImageUpload($image, $target_dir) {
            $target_file = $target_dir . basename($image["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($image["tmp_name"]);
            if ($check !== false) {
                if (file_exists($target_file)) {
                    return $target_file;
                }
                if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                    return false;
                }
                if (move_uploaded_file($image["tmp_name"], $target_file)) {
                    return $target_file;
                }
            }
            return false;
        }

        // Handle product form
        if ($form_id == "add_product_form") {
            $product_image = handleImageUpload($_FILES["product_image"], $target_dir);
            if (!$product_image) {
                echo "Error uploading product image.";
                exit();
            }

            // Prepare data for insertion
            $data = [
                "product_id" => NULL,
                "product_name" => $_POST["product_name"],
                "product_price" => $_POST["product_price"],
                "product_image" => $product_image,
                "package_id" => $_POST["product_package"],
                "total_stock" => $_POST["no_of_product_units"],
                "packed_stock" => $_POST["no_of_product_units_packed"],
                "total_stock_price" => ($_POST["product_price"] * $_POST["no_of_product_units"]),
            ];
            $table = "products";
            $location = "products.php";

            $supplier = $_POST["product_supplier"];
            $sp_id = NULL;
            $add_supplier_product_sql = "INSERT INTO supplier_products (sp_id, supplier_id, product_id, quantity, price_per_unit, total_price) VALUES (?, ?, ?, ?, ?, ?);";
            $add_supplier_product_stmt = $conn->prepare($add_supplier_product_sql);
            if ($add_supplier_product_stmt === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }

            $add_supplier_product_stmt->bind_param("siiiii", $sp_id, $supplier, $data['product_id'], $data['total_stock'], $data['product_price'], $data['total_stock_price']);

            if ($add_supplier_product_stmt->execute()) {
                echo "Supplier Product Added";
            } else {
                echo "Error Adding supplier product: " . htmlspecialchars($add_supplier_product_stmt->error);
            }

            // Update packaging material stock
            $package_id = $data['package_id'];
            $packed_units = $data['packed_stock'];
            $sql = "UPDATE packaging_material SET no_of_units = no_of_units - ? WHERE pkg_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("ii", $packed_units, $package_id);
            if ($stmt->execute()) {
                echo "Stock updated successfully";
            } else {
                echo "Error updating stock: " . htmlspecialchars($stmt->error);
            }
            $stmt->close();

            $sql = "UPDATE packaging_material SET total_price = no_of_units * price_per_unit WHERE pkg_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Prepare failed: " . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("i", $package_id);
            if ($stmt->execute()) {
                echo "Stock updated successfully";
            } else {
                echo "Error updating stock: " . htmlspecialchars($stmt->error);
            }
        }

        // Handle supplier form
        elseif ($form_id == "add_supplier_form") {
            $data = [
                "supplier_id" => NULL,
                "supplier_name" => $_POST["supplier_name"],
                "supplier_email" => $_POST["supplier_email"],
                "supplier_phone" => $_POST["supplier_phone"],
                "payment_status" => $_POST["payment_status"] == "Done" ? 1 : 0,
            ];
            $table = "suppliers";
            $location = "suppliers.php";
        }

        // Handle packaging material form
        elseif ($form_id == "add_pkg_mtrl_form") {
            $pkg_image = handleImageUpload($_FILES["pkg_image"], $target_dir);
            if (!$pkg_image) {
                echo "Error uploading package image.";
                exit();
            }

            $data = [
                "pkg_id" => NULL,
                "pkg_name" => $_POST["pkg_name"],
                "pkg_size" => $_POST["pkg_size"],
                "no_of_units" => $_POST["no_of_pkg_units"],
                "price_per_unit" => $_POST["pkg_price_per_unit"],
                "pkg_image" => $pkg_image,
                "total_price" => $_POST["no_of_pkg_units"] * $_POST["pkg_price_per_unit"],
            ];
            $table = "packaging_material";
            $location = "pkg_mtrl.php";
        }

        // Handle unknown form id
        else {
            echo "Unknown form id.";
            exit();
        }

        // Insert data into the database
        if (insertData($conn, $table, $data)) {
            header("location: $location");
        } else {
            echo "An error occurred while inserting data.";
        }
    } else {
        echo "Form id not set.";
    }
} else {
    echo "Please enter data.";
}
?>
