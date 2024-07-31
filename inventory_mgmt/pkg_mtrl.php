<?php
session_start();
include ("./includes/_dbconnect.php");

if (!isset($_SESSION["username"])) {
    header("location: ./includes/login.php");
    exit();
}

$search_query = '';
if (isset($_GET['searched_product'])) {
    $search_query = $_GET['searched_product'];
}




// Prepare the SQL query with search functionality
$sql = "SELECT * FROM packaging_material";
if ($search_query) {
    $search_query = $conn->real_escape_string($search_query);
    $sql .= " WHERE pkg_name LIKE '%$search_query%'";
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
    <title>Inventory | Packaging Materials</title>
</head>

<body>
    <div class="d-flex">
        <?php include ("./includes/sidebar.php") ?>
        <div class="container-fluid ">
            <div class="row">
                <form class="form-inline my-2 my-lg-0 col-10 d-flex " action="./includes/pkg_mtrl.php">
                    <input class="form-control mr-sm-2 my-4 " type="search" placeholder="Search" name="searched_product"
                        aria-label="Search">
                    <button class="btn btn-outline-success mx-4 my-4 " type="submit">Search</button>
                </form>

                <div class="add-remove-btns col">
                    <button type="button" class="btn btn-success px-5 my-4" data-bs-toggle="modal"
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
                                    <form class="row g-3 needs-validation" name="form_id" value="add_pkg_mtrl_form"
                                        action="./includes/add.php" method="post" enctype="multipart/form-data" novalidate>
                                        <input type="hidden" name="form_id" value="add_pkg_mtrl_form">
                                        <div class="col-md-12">
                                            <label for="pkg_name" class="form-label">Package Name</label>
                                            <input type="text" class="form-control" id="pkg_name" name="pkg_name"
                                                placeholder="ex: box, tape" required>
                                            <div class="invalid-feedback">
                                                Enter a valid package name (Box, Tape, Bubble wrap ...)
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="pkg_size" class="form-label">Package Size</label>
                                            <input type="text" class="form-control" id="pkg_size" name="pkg_size"
                                                placeholder="ex: 12inch, 20m, 12*13" required>
                                            <div class="invalid-feedback">
                                                Enter a Package Size (12 * 3, 20 m, 12 in ...)
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="no_of_pkg_units" class="form-label">Number Of Units
                                            </label>
                                            <div class="input-group has-validation">
                                                <input type="number" class="form-control" id="no_of_pkg_units"
                                                    name="no_of_pkg_units" aria-describedby="inputGroupPrepend"
                                                    required>
                                                <div class="invalid-feedback">
                                                    Enter Number of units added
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="pkg_price_per_unit" class="form-label">Price Per Unit (in
                                                rupees)</label>
                                            <input type="text" class="form-control" id="pkg_price_per_unit"
                                                name="pkg_price_per_unit" placeholder="ex: 200.00" required>
                                            <div class="invalid-feedback">
                                                Enter a valid Price (in rupees)
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="pkg_image" class="form-label">Upload Image </label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="pkg_image" name="pkg_image"
                                                    required>
                                                <div class="invalid-feedback">
                                                    Choose a valid image file
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="pkg_supplier" class="form-label">Supplier </label>
                                            <select class="form-select" id="pkg_supplier" name="pkg_supplier" required>
                                                <option disabled placeholder="">Choose...</option>
                                                <?php
                                                $get_suppliers_query = "select supplier_name from suppliers";
                                                $suppliers_query_result = $conn->query($get_suppliers_query);
                                                while ($row = $suppliers_query_result->fetch_assoc()): ?>
                                                    <option><?php echo $row["supplier_name"]; ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Select a valid Supplier
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Add Supplier</button>
                                        </div>
                                    </form>
                                    <script src="./javascript/form_control.js"></script>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
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
                    <table class="table">
                        <table class="table caption-top">
                            <thead>
                                <tr>
                                    <th class="show-on-remove-btn-click" style="display:none"></th>
                                    <th scope="col">Photo</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Size</th>
                                    <th scope="col">Stock Units</th>
                                    <th scope="col">Price Per Unit</th>
                                    <th scope="col">Total Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="show-on-remove-btn-click" id="delete_row" style="display:none">
                                                <div>
                                                    <input class="form-check-input check" type="checkbox" name="check"
                                                        value="<?php echo $content = $row["pkg_name"]; ?>" aria-label="...">
                                                </div>
                                            </td>
                                            <td><img src="<?php echo $row["pkg_image"] ?>" alt="package-image"
                                                    class="product-image">
                                            </td>
                                            <td><?php echo $row["pkg_name"]; ?></td>
                                            <td><?php echo $row["pkg_size"]; ?></td>
                                            <td><?php echo $row["no_of_units"]; ?></td>
                                            <td><?php echo $row["price_per_unit"] . "/-"; ?></td>
                                            <td><?php echo $row["total_price"] . "/-"; ?></td>

                                            <td>
                                                <!-- Button trigger modal -->
                                                <button type="button" class="btn btn-primary edit-btn" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?php echo $row['pkg_id']; ?>">
                                                    Edit
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="editModal<?php echo $row['pkg_id']; ?>"
                                                    tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['pkg_id']; ?>"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5"
                                                                    id="editModalLabel<?php echo $row['pkg_id']; ?>">Update The
                                                                    Details</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form class="row g-3 needs-validation" name="form_id"
                                                                    value="edit_pkg_mtrl_form" enctype="multipart/form-data"
                                                                    action="./includes/edit.php" method="post" novalidate>
                                                                    <input type="hidden" name="pkg_id"
                                                                        value="<?php echo $row['pkg_id']; ?>">
                                                                    <input type="hidden" name="form_id"
                                                                        value="edit_pkg_mtrl_form">
                                                                    <div class="col-md-12">
                                                                        <label for="pkg_name" class="form-label">Package
                                                                            Name</label>
                                                                        <input type="text" class="form-control" id="pkg_name"
                                                                            name="pkg_name"
                                                                            value="<?php echo $row['pkg_name']; ?>"
                                                                            placeholder="ex: box, tape" required>
                                                                        <div class="invalid-feedback">
                                                                            Enter a valid package name (Box, Tape, Bubble wrap
                                                                            ...)
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="pkg_size" class="form-label">Package
                                                                            Size</label>
                                                                        <input type="text" class="form-control" id="pkg_size"
                                                                            name="pkg_size"
                                                                            value="<?php echo $row['pkg_size']; ?>"
                                                                            placeholder="ex: 12inch, 20m, 12*13" required>
                                                                        <div class="invalid-feedback">
                                                                            Enter a Package Size (12*3, 20 m, 12 in ...)
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="no_of_pkg_units" class="form-label">Number
                                                                            Of Units
                                                                        </label>
                                                                        <div class="input-group has-validation">
                                                                            <input type="number" class="form-control"
                                                                                id="no_of_pkg_units" name="no_of_pkg_units"
                                                                                value="<?php echo $row['no_of_units']; ?>"
                                                                                aria-describedby="inputGroupPrepend" required>
                                                                            <div class="invalid-feedback">
                                                                                Enter Number of units added
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="pkg_price_per_unit" class="form-label">Price
                                                                            Per Unit (in
                                                                            rupees)</label>
                                                                        <input type="text" class="form-control"
                                                                            id="pkg_price_per_unit" name="pkg_price_per_unit"
                                                                            value="<?php echo $row['price_per_unit']; ?>"
                                                                            placeholder="ex: 200.00" required>
                                                                        <div class="invalid-feedback">
                                                                            Enter a valid Price (in rupees)
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="pkg_image" class="form-label">Upload Image
                                                                        </label>
                                                                        <div class="input-group">
                                                                            <input type="file" class="form-control"
                                                                                id="pkg_image" name="pkg_image">
                                                                            <input type="hidden" name="current_image"
                                                                                value="<?php echo $row['pkg_image']; ?>">

                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="pkg_supplier" class="form-label">Supplier
                                                                        </label>
                                                                        <select class="form-select" id="pkg_supplier"
                                                                            name="pkg_supplier" required>
                                                                            <option selected disabled placeholder="">
                                                                            </option>
                                                                            <?php
                                                                            $get_suppliers_query = "select supplier_name from suppliers";
                                                                            $suppliers_query_result = $conn->query($get_suppliers_query);
                                                                            while ($row = $suppliers_query_result->fetch_assoc()): ?>
                                                                                <option selected>
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
                                                                        <button type="submit" class="btn btn-primary">Update
                                                                            Package Material</button>
                                                                    </div>
                                                                </form>
                                                                <script src="./javascript/form_control.js"></script>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>

                                    <div class="d-flex flex-fill pb-3 justify-content-between show-on-remove-btn-click">
                                        <div class="heading">

                                            <h2 class="show-on-remove-btn-click" style="display:none">Select Packaging
                                                Materials to
                                                remove </h2>
                                        </div>
                                        <div class="btns mr-5">
                                            <button type="button" id="cancel_delete"
                                                class="btn btn-secondary show-on-remove-btn-click "
                                                style="display:none">Cancel</button>
                                            <button type="button" id="final_delete_btn"
                                                class="btn btn-danger mx-3 show-on-remove-btn-click"
                                                style="display:none">Delete Selected </button>
                                        </div>
                                    </div>
                                    <form id="delete-form" action="./includes/remove.php" method="post" style="display: none;">
                                        <input type="hidden" name="items_array" id="product-ids">
                                        <input type="hidden" name="delete_form_id" value="delete_pkg_mtrl"
                                            id="delete_pkg_mtrl">
                                    </form>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9">No Packaging Material found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="./javascript/remove.js"></script> -->
    <!-- <script src="./javascript/edit.js"></script> -->
</body>

</html>