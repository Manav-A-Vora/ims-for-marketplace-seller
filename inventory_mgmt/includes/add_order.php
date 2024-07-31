<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include ("_dbconnect.php");
include ("functions.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_price;
    $table = "";
    $data = [];

    if (isset($_POST["form_id"])) {
        if ($_POST["form_id"] == "add_order_form") {
            $table = "orders";
            $data = [
                "order_id" => NULL,
                "order_date" => $_POST["order_date"],
                "product_id" => $_POST["order_product"],
                "no_of_units" => $_POST["no_of_order_product_units"],
                "order_total" => 0,
                "order_status" => $_POST["order_status"]
            ];

            // Fetch product price
            $product_price_sql = "SELECT product_price FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($product_price_sql);
            if ($stmt === false) {
                $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
                header("Location: orders.php");
                exit();
            }

            $stmt->bind_param("i", $data["product_id"]);
            $stmt->execute();
            $stmt->bind_result($product_price);
            $stmt->fetch();
            $stmt->close();

            if ($product_price) {
                $data["order_total"] = $product_price * $data["no_of_units"];
            } else {
                $_SESSION['error_message'] = "Error fetching product price";
                header("Location: orders.php");
                exit();
            }
        }

        // Insert data into orders table
        if (insertData($conn, $table, $data)) {
            // Fetch packed units and total stock
            $sql = 'SELECT packed_stock, total_stock FROM products WHERE product_id = ?';
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
                header("Location: orders.php");
                exit();
            }
            $stmt->bind_param('i', $data['product_id']);
            $stmt->execute();
            $stmt->bind_result($packed_units, $total_stock);
            $stmt->fetch();
            $stmt->close();

            // Update packed units and total stock
            $sql = "UPDATE products SET packed_stock = ?, total_stock = ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $_SESSION['error_message'] = "Error preparing statement: " . $conn->error;
                header("Location: orders.php");
                exit();
            }

            $final_packed_stock = $packed_units - $data['no_of_units'];
            $final_total_stock = $total_stock - $data['no_of_units'];
            if ($final_total_stock >= 0) {
                $stmt->bind_param('iii', $final_packed_stock, $final_total_stock, $data['product_id']);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Order added and product stock updated";

                } else {
                    $_SESSION['error_message'] = "Error updating product stock: " . $stmt->error;

                }
            } else {
                $_SESSION['error_message'] = "Insufficient stocks cannot add order";
                removeData($conn, $table, "order_date", $data['order_date']);
                header("Location: orders.php");
                exit();
            }

        } else {
            $_SESSION['error_message'] = "Error adding order";
            header("Location: orders.php");
            exit();
        }

        header("Location: ../orders.php");
        exit();
    }
}
?>