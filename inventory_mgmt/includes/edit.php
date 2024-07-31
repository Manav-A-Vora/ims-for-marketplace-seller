<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit();
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> ' . htmlspecialchars($_SESSION['error']) . '
    					  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

    </div>';
    unset($_SESSION['error']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> ' . htmlspecialchars($_SESSION['success']) . '
    					  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

    </div>';
    unset($_SESSION['success']);
}

include ("_dbconnect.php");
include ("functions.php");

// Function to upload image and return the path
function handleImageUpload($fileInput, $targetDir) {
    if (!empty($fileInput["name"])) {
        $uploadedImagePath = uploadImage($fileInput, $targetDir);
        if ($uploadedImagePath !== false) {
            return $uploadedImagePath;
        } else {
            $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            header("Location: edit.php");
            exit();
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['form_id'] == 'edit_product_form') {
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['product_price'];
        $total_stock = $_POST['no_of_product_units'];
        $packed_stock = $_POST['no_of_product_units_packed'];
        $product_package = $_POST['product_package'];
        $product_image = $_POST['current_image']; // Store current image path

        // Check if a new image is uploaded
        $uploadedImagePath = handleImageUpload($_FILES["product_image"], "../assets/");
        if ($uploadedImagePath !== false) {
            $product_image = $uploadedImagePath; // Use new image path
        }

        // Update product information
        $sql = "UPDATE products SET
                product_name=?,
                product_price=?,
                total_stock=?,
                packed_stock=?,
                product_image=?
                WHERE product_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdissi", $product_name, $product_price, $total_stock, $packed_stock, $product_image, $product_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Product updated successfully.";
            header("Location: products.php");
        } else {
            $_SESSION['error'] = "Error updating record: " . $stmt->error;
            header("Location: edit.php");
        }
        $stmt->close();

    } elseif ($_POST['form_id'] == 'edit_supplier_form') {
        $supplier_id = $_POST["supplier_id"];
        $supplier_name = $_POST["supplier_name"];
        $supplier_email = $_POST["supplier_email"];
        $supplier_phone = $_POST["supplier_phone"];
        $payment_status = $_POST["payment_status"] == "Done" ? 1 : 0;

        $sql = "UPDATE suppliers SET
                supplier_name=?,
                supplier_email=?,
                supplier_phone=?,
                payment_status=?
                WHERE supplier_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $supplier_name, $supplier_email, $supplier_phone, $payment_status, $supplier_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Supplier updated successfully.";
            header("Location: suppliers.php");
        } else {
            $_SESSION['error'] = "Error updating record: " . $stmt->error;
            header("Location: edit.php");
        }
        $stmt->close();

    } elseif ($_POST['form_id'] == 'edit_pkg_mtrl_form') {
        $pkg_id = $_POST["pkg_id"];
        $pkg_name = $_POST["pkg_name"];
        $pkg_size = $_POST["pkg_size"];
        $no_of_units = $_POST["no_of_pkg_units"];
        $price_per_unit = $_POST["pkg_price_per_unit"];
        $total_price = $no_of_units * $price_per_unit;
        $pkg_image = $_POST['current_image']; // Store current image path

        // Check if a new image is uploaded
        $uploadedImagePath = handleImageUpload($_FILES["pkg_image"], "../assets/");
        if ($uploadedImagePath !== false) {
            $pkg_image = $uploadedImagePath; // Use new image path
        }

        $sql = "UPDATE packaging_material SET
                pkg_name=?,
                pkg_size=?,
                no_of_units=?,
                price_per_unit=?,
                pkg_image=?,
                total_price=?
                WHERE pkg_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidsdi", $pkg_name, $pkg_size, $no_of_units, $price_per_unit, $pkg_image, $total_price, $pkg_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Packaging material updated successfully.";
            header("Location: pkg_mtrl.php");
        } else {
            $_SESSION['error'] = "Error updating record: " . $stmt->error;
            header("Location: edit.php");
        }
        $stmt->close();

    } elseif ($_POST['form_id'] == 'edit_order_form') {
        $order_id = $_POST["order_id"];
        $order_date = $_POST["edit_order_date"];
        $order_product = $_POST["edit_order_product"];
        $no_of_units = $_POST["edit_no_of_order_product_units"];
        $order_status = $_POST["edit_order_status"];

        // Fetch product price and stock information
        $product_price_sql = "SELECT product_price, packed_stock, total_stock FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($product_price_sql);
        $stmt->bind_param("i", $order_product);
        $stmt->execute();
        $stmt->bind_result($product_price, $packed_units, $total_stock);
        $stmt->fetch();
        $stmt->close();

        // Check if there are enough units in stock
        if ($no_of_units > $total_stock) {
            $_SESSION['error'] = "Not enough units in stock to update the order.";
            header("Location: orders.php");
            exit();
        }

        $order_total = $no_of_units * $product_price;

        $sql = "UPDATE orders SET
                order_date=?,
                product_id=?,
                no_of_units=?,
                order_total=?,
                order_status=?
                WHERE order_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiisi", $order_date, $order_product, $no_of_units, $order_total, $order_status, $order_id);

        if ($stmt->execute()) {
            // Update product stock
            $final_packed_stock = $packed_units - $no_of_units;
            $final_total_stock = $total_stock - $no_of_units;
            $update_stock_sql = "UPDATE products SET packed_stock=?, total_stock=? WHERE product_id=?";
            $stmt = $conn->prepare($update_stock_sql);
            $stmt->bind_param("iii", $final_packed_stock, $final_total_stock, $order_product);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Order updated and product stock updated.";
                header("Location: orders.php");
            } else {
                $_SESSION['error'] = "Error updating product stock: " . $stmt->error;
                header("Location: orders.php");
            }
        } else {
            $_SESSION['error'] = "Error updating order: " . $stmt->error;
            header("Location: orders.php");
        }
        $stmt->close();
    }

    $conn->close();
}
?>
