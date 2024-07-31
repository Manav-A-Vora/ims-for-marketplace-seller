    <?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location: ./includes/login.php");
    exit();
}

include ("./includes/_dbconnect.php");

$search_query = '';
if (isset($_GET['searched_product'])) {
    $search_query = $_GET['searched_product'];
}



// Prepare the SQL query with search functionality
$sql = "SELECT * FROM products";
if ($search_query) {
    $search_query = $conn->real_escape_string($search_query);
    $sql .= " WHERE product_name LIKE '%$search_query%'";
}

$result = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <link rel="stylesheet" href="./css/products.css">
    <title>Inventory | Products</title>
</head>

<body>
    <div class="d-flex">
        <?php include ("./includes/sidebar.php") ?>
        <div class="container-fluid ">

            <div class="row">
                <form class="form-inline my-2 my-lg-0 col-10 d-flex " action="products.php">
                    <input class="form-control mr-sm-2 my-4 " type="search" placeholder="Search Product"
                        name="searched_product" aria-label="Search">
                    <button class="btn btn-outline-success mx-4 my-4 " type="submit">Search</button>
                </form>

                <div class="add-remove-btns col-2">
                    <button type="button" class="btn btn-success px-5" data-bs-toggle="modal"
                        data-bs-target="#exampleModal">
                        Add
                    </button>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Fill The Details</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form class="row g-3 needs-validation" name="form_id" value="add_product_form"
                                        action="./includes/add.php" method="post" enctype="multipart/form-data" novalidate>
                                        <input type="hidden" name="form_id" value="add_product_form">
                                        <div class="col-md-12">
                                            <label for="product_name" class="form-label">Product Name</label>
                                            <input type="text" class="form-control" id="product_name"
                                                name="product_name" placeholder="ex: masaager" required>
                                            <div class="invalid-feedback">
                                                Enter a valid product name (masaager, lint remover ...)
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="product_price" class="form-label">Product Price</label>
                                            <input type="text" class="form-control" id="product_price"
                                                name="product_price" placeholder="ex: 200.00" required>
                                            <div class="invalid-feedback">
                                                Enter a product price (ex: 200.00)
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="no_of_product_units" class="form-label">Total Units
                                            </label>
                                            <div class="input-group has-validation">
                                                <input type="number" class="form-control" id="no_of_product_units"
                                                    name="no_of_product_units" aria-describedby="inputGroupPrepend"
                                                    placeholder="ex: 5000" required>
                                                <div class="invalid-feedback">
                                                    Enter Number of units added
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="no_of_product_units_packed" class="form-label">Packed Units
                                            </label>
                                            <div class="input-group has-validation">
                                                <input type="number" class="form-control"
                                                    id="no_of_product_units_packed" name="no_of_product_units_packed"
                                                    aria-describedby="inputGroupPrepend" placeholder="ex: 1000"
                                                    required>
                                                <div class="invalid-feedback">
                                                    Enter Number of units packed
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="product_image" class="form-label">Upload Product Image </label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="product_image"
                                                    name="product_image" required>
                                            </div>
                                            <div class="invalid-feedback">
                                                Upload a valid image
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="product_package" class="form-label">Package Used </label>
                                            <select class="form-select" id="product_package" name="product_package"
                                                required>
                                                <option disabled placeholder="">Choose Package</option>
                                                <?php
                                                $get_pkg_mtrl_query = "select pkg_name, pkg_id from packaging_material";
                                                $pkg_mtrl_query_result = $conn->query($get_pkg_mtrl_query);
                                                while ($row = $pkg_mtrl_query_result->fetch_assoc()): ?>
                                                    <option value="<?php echo $row["pkg_id"]; ?>" required>
                                                        <?php echo $row["pkg_name"]; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Select a valid Package Material
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="product_supplier" class="form-label">Supplier </label>
                                            <select class="form-select" id="product_supplier" name="product_supplier"
                                                required>
                                                <option disabled placeholder="">Choose...</option>
                                                <?php
                                                $get_suppliers_query = "select supplier_name, supplier_id from suppliers";
                                                $suppliers_query_result = $conn->query($get_suppliers_query);
                                                while ($row = $suppliers_query_result->fetch_assoc()): ?>
                                                    <option value="<?php echo $row["supplier_id"]; ?>">
                                                        <?php echo $row["supplier_name"]; ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Select a valid Supplier
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Add Product</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-danger btn-md my-4 px-3" id="remove-btn">Remove</button>

                </div>
            </div>
            <div class="row">
                <div class="d-flex flex-fill pb-3 justify-content-between show-on-remove-btn-click">
                    <div class="heading">

                        <h2 class="show-on-remove-btn-click" style="display:none">Select Products to
                            remove </h2>
                    </div>
                    <div class="btns mr-5">

                        <button type="button" id="cancel_delete" class="btn btn-secondary show-on-remove-btn-click "
                            style="display:none">Cancel</button>
                        <button type="button" id="final_delete_btn" class="btn btn-danger mx-3 show-on-remove-btn-click"
                            style="display:none">Delete Selected Products</button>
                    </div>
                </div>
                <form id="delete-form" action="./includes/remove.php" method="post" style="display: none;">
                    <input type="hidden" name="items_array" id="product-ids">
                    <input type="hidden" name="delete_form_id" value="delete_product" id="delete_product">
                </form>

                <?php
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
                ?>
                <div class="table-responsive">

                    <table class="table caption-top">
                        <thead>
                            <tr class="my-3">
                                <th class="show-on-remove-btn-click" style="display:none"></th>
                                <th scope="col">Photo</th>
                                <th scope="col">Name</th>
                                <th scope="col">Stock Units</th>
                                <th scope="col">Packed Units</th>
                                <th scope="col">Price Per Unit</th>
                                <th scope="col">Total Price</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="show-on-remove-btn-click" id="delete_row" style="display:none">
                                            <div>
                                                <input class="form-check-input check" name="check" type="checkbox"
                                                    value="<?php echo $content = $row["product_name"]; ?>" aria-label="...">
                                            </div>
                                        </td>
                                        <td>
                                            <img src="<?php echo './assets/' . $row['product_image']; ?>" alt="product"
                                                class="product-image">
                                        </td>
                                        <td><?php $product_name = $row["product_name"];
                                        echo ucfirst($product_name); ?></td>
                                        <td><?php echo $row["total_stock"]; ?></td>
                                        <td><?php echo $row["packed_stock"]; ?></td>
                                        <td><?php echo $row["product_price"] . "/-"; ?></td>
                                        <td><?php echo $row["total_stock_price"] . "/-"; ?></td>
                                        <td>


                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary edit-btn" data-bs-toggle="modal"
                                                data-bs-target="#editModal<?php echo $row['product_id']; ?>">
                                                Edit
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="editModal<?php echo $row['product_id']; ?>"
                                                tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['product_id']; ?>"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5"
                                                                id="editModalLabel<?php echo $row['product_id']; ?>">Edit
                                                                Product</h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form class="row g-3 needs-validation" name="form_id"
                                                                value="edit_product_form" action="./includes/edit.php" method="post"
                                                                enctype="multipart/form-data" novalidate>
                                                                <input type="hidden" name="product_id"
                                                                    value="<?php echo $row['product_id']; ?>">
                                                                <input type="hidden" name="form_id" value="edit_product_form">
                                                                <!-- Your other form fields here, pre-fill with existing data -->
                                                                <div class="col-md-12">
                                                                    <label for="product_name" class="form-label">Product
                                                                        Name</label>
                                                                    <input type="text" class="form-control" id="product_name"
                                                                        name="product_name"
                                                                        value="<?php echo $row['product_name']; ?>" required>
                                                                    <div class="invalid-feedback">
                                                                        Enter a valid product name (masaager, lint remover ...)
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label for="product_price" class="form-label">Product
                                                                        Price</label>
                                                                    <input type="text" class="form-control" id="product_price"
                                                                        name="product_price"
                                                                        value="<?php echo $row["product_price"]; ?>"
                                                                        placeholder="ex: 200.00" required>
                                                                    <div class="invalid-feedback">
                                                                        Enter a product price (ex: 200.00)
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label for="no_of_product_units" class="form-label">Total
                                                                        Units
                                                                    </label>
                                                                    <div class="input-group has-validation">
                                                                        <input type="number" class="form-control"
                                                                            id="no_of_product_units" name="no_of_product_units"
                                                                            value="<?php echo $row["total_stock"]; ?>"
                                                                            aria-describedby="inputGroupPrepend"
                                                                            placeholder="ex: 5000" required>
                                                                        <div class="invalid-feedback">
                                                                            Enter Number of units added
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label for="no_of_product_units_packed"
                                                                        class="form-label">Packed Units
                                                                    </label>
                                                                    <div class="input-group has-validation">
                                                                        <input type="number" class="form-control"
                                                                            id="no_of_product_units_packed"
                                                                            name="no_of_product_units_packed"
                                                                            value="<?php echo $row["packed_stock"]; ?>"
                                                                            aria-describedby="inputGroupPrepend"
                                                                            placeholder="ex: 1000" required>
                                                                        <div class="invalid-feedback">
                                                                            Enter Number of units packed
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <label for="product_image" class="form-label">Upload Product
                                                                        Image </label>
                                                                    <div class="input-group">
                                                                        <input type="file" class="form-control"
                                                                            id="product_image" name="product_image">
                                                                        <input type="hidden" name="current_image"
                                                                            value="<?php echo $row['product_image']; ?>">
                                                                    </div>
                                                                    <div class="invalid-feedback">
                                                                        Upload a valid image
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="product_package" class="form-label">Package Used
                                                                    </label>
                                                                    <select class="form-select" id="product_package"
                                                                        name="product_package" required>
                                                                        <?php

                                                                        $package_name_sql = "SELECT pkg_name FROM packaging_material WHERE pkg_id = ?";
                                                                        $stmt = $conn->prepare($package_name_sql);
                                                                        $stmt->bind_param("i", $row["package_id"]);
                                                                        $stmt->execute();
                                                                        $stmt->bind_result($package_name);
                                                                        $stmt->fetch();
                                                                        $stmt->close();
                                                                        ?>
                                                                        <option selected disabled
                                                                            value="<?php echo $package_name; ?>" placeholder="">
                                                                            <?php echo $package_name; ?>
                                                                        </option>
                                                                        <?php
                                                                        $get_pkg_mtrl_query = "select pkg_name, pkg_id from packaging_material";
                                                                        $pkg_mtrl_query_result = $conn->query($get_pkg_mtrl_query);
                                                                        while ($row = $pkg_mtrl_query_result->fetch_assoc()): ?>
                                                                            <option value="<?php echo $row["pkg_id"]; ?>" required>
                                                                                <?php echo $row["pkg_name"]; ?>
                                                                            </option>
                                                                        <?php endwhile; ?>
                                                                    </select>
                                                                    <div class="invalid-feedback">
                                                                        Select a valid Package Material
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="product_supplier" class="form-label">Supplier
                                                                    </label>
                                                                    <select class="form-select" id="product_supplier"
                                                                        name="product_supplier" required>
                                                                        <option selected disabled placeholder="">Choose...
                                                                        </option>
                                                                        <?php
                                                                        $get_suppliers_query = "select supplier_name, supplier_id from suppliers";
                                                                        $suppliers_query_result = $conn->query($get_suppliers_query);
                                                                        while ($row = $suppliers_query_result->fetch_assoc()): ?>
                                                                            <option value="<?php echo $row["supplier_id"]; ?>">
                                                                                <?php echo $row["supplier_name"]; ?>
                                                                            </option>
                                                                        <?php endwhile; ?>
                                                                    </select>
                                                                    <div class="invalid-feedback">
                                                                        Select a valid Supplier
                                                                    </div>
                                                                </div>
                                                                <!-- Other fields... -->
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Update
                                                                        Data</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <script src="./javascript/form_control.js"></script>
    <script src="./javascript/remove.js"></script>
</body>

</html>