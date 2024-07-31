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
$sql = "SELECT * FROM suppliers";
if ($search_query) {
    $search_query = $conn->real_escape_string($search_query);
    $sql .= "WHERE supplier_name LIKE '%$search_query%'";
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
    <title>Inventory | Suppliers</title>
</head>

<body>
    <div class="d-flex">
        <?php include ("./includes/sidebar.php") ?>
        <div class="container-fluid">
            <div class="row">


                <div class="row">
                    <form class="form-inline my-2 my-lg-0 col-10 d-flex " action="suppliers.php">
                        <input class="form-control mr-sm-2 my-4 " type="search" placeholder="Search"
                            name="searched_product" aria-label="Search">
                        <button class="btn btn-outline-success mx-4 my-4 " type="submit">Search</button>
                    </form>

                    <div class="add-remove-btns col-2 align-right">
                        <button type="button" class="btn btn-success my-4 px-5" data-bs-toggle="modal"
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
                                        <form class="row g-3 needs-validation" id="add_supplier_form" name="form_id"
                                            placeholder="add_supplier_form" action="./includes/add.php" method="post" novalidate>
                                            <input type="hidden" name="form_id" value="add_supplier_form">
                                            <div class="col-md-12">
                                                <label for="supplier_name" class="form-label">Supplier's Name</label>
                                                <input type="text" class="form-control" id="supplier_name"
                                                    name="supplier_name" placeholder="ex: raman lal" required>
                                                <div class="valid-feedback">
                                                    Looks good!
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="supplier_email" class="form-label">Supplier's Email</label>
                                                <input type="email" class="form-control" id="supplier_email"
                                                    name="supplier_email" placeholder="ex: raman@gmail.com" required>
                                                <div class="invalid-feedback">
                                                    Please Enter a valid email
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="supplier_phone" class="form-label">Phone
                                                    Number</label>
                                                <div class="input-group has-validation">
                                                    <input type="tel" class="form-control" id="supplier_phone"
                                                        name="supplier_phone" aria-describedby="inputGroupPrepend"
                                                        required>
                                                    <div class="invalid-feedback">
                                                        Please Enter a phone Number
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="payment_status" class="form-label">Payment Status</label>
                                                <select class="form-select" id="payment_status" name="payment_status"
                                                    required>
                                                    <option selected disabled placeholder="">Choose...</option>
                                                    <option>Done</option>
                                                    <option>Pending</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select a valid status.
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
                                        <th scope="col">Name</th>
                                        <th scope="col">E-mail</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Payment Status</th>
                                        <th>Actions</th>

                                    </tr>
                                </  thead>
                                <tbody>

                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="show-on-remove-btn-click" id="delete_row" style="display:none">
                                                    <div>
                                                        <input class="form-check-input check" name="check" type="checkbox"
                                                            value="<?php echo $content = $row["supplier_name"]; ?>"
                                                            aria-label="...">
                                                    </div>
                                                </td>
                                                <td><?php echo $row["supplier_name"]; ?></td>
                                                <td><?php echo $row["supplier_email"]; ?></td>
                                                <td><?php echo $row["supplier_phone"]; ?></td>
                                                <td>
                                                    <?php
                                                    $status = $row["payment_status"] ? '<span class = "badge text-bg-success">Done</span>' : '<span class = "badge text-bg-danger">Pending</span>';
                                                    echo $status;
                                                    ?>
                                                </td>
                                                <td>


                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="btn btn-primary edit-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editModal<?php echo $row['supplier_id']; ?>">
                                                        Edit
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="editModal<?php echo $row['supplier_id']; ?>"
                                                        tabindex="-1"
                                                        aria-labelledby="editModalLabel<?php echo $row['supplier_id']; ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h1 class="modal-title fs-5"
                                                                        id="editModalLabel<?php echo $row['supplier_id']; ?>">
                                                                        Edit
                                                                        supplier</h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form class="row g-3 needs-validation" name="form_id"
                                                                        value="edit_supplier_form" action="./includes/edit.php"
                                                                        method="post" novalidate>
                                                                        <input type="hidden" name="supplier_id"
                                                                            value="<?php echo $row['supplier_id']; ?>">
                                                                        <input type="hidden" name="form_id"
                                                                            value="edit_supplier_form">
                                                                        <!-- Your other form fields here, pre-fill with existing data -->
                                                                        <div class="col-md-12">
                                                                            <label for="supplier_name"
                                                                                class="form-label">supplier
                                                                                Name</label>
                                                                            <input type="text" class="form-control"
                                                                                id="supplier_name" name="supplier_name"
                                                                                value="<?php echo $row['supplier_name']; ?>"
                                                                                required>
                                                                            <div class="invalid-feedback">
                                                                                Enter a valid supplier name (masaager, lint
                                                                                remover
                                                                                ...)
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <label for="supplier_email"
                                                                                class="form-label">Supplier's Email</label>
                                                                            <input type="email" class="form-control"
                                                                                id="supplier_email" name="supplier_email"
                                                                                placeholder="ex: raman@gmail.com"
                                                                                value="<?php echo $row['supplier_email']; ?>"
                                                                                required>
                                                                            <div class="invalid-feedback">
                                                                                Please Enter a valid email
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <label for="supplier_phone" class="form-label">Phone
                                                                                Number</label>
                                                                            <div class="input-group has-validation">
                                                                                <input type="tel" class="form-control"
                                                                                    id="supplier_phone" name="supplier_phone"
                                                                                    aria-describedby="inputGroupPrepend"
                                                                                    value="<?php echo $row['supplier_phone']; ?>"
                                                                                    required>
                                                                                <div class="invalid-feedback">
                                                                                    Please Enter a phone Number
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label for="payment_status"
                                                                                class="form-label">Payment
                                                                                Status</label>
                                                                            <select class="form-select" id="payment_status"
                                                                                name="payment_status"
                                                                                value="<?php echo $row['payment_status']; ?>"
                                                                                required>
                                                                                <option selected disabled placeholder="">
                                                                                    <?php echo $row['payment_status']; ?>
                                                                                </option>
                                                                                <option>Done</option>
                                                                                <option>Pending</option>
                                                                            </select>
                                                                            <div class="invalid-feedback">
                                                                                Please select a valid status.
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
                                        <div class="d-flex flex-fill pb-3 justify-content-between show-on-remove-btn-click">
                                            <div class="heading">

                                                <h2 class="show-on-remove-btn-click" style="display:none">Select Suppliers
                                                    to
                                                    remove </h2>
                                            </div>
                                            <div class="btns mr-5">

                                                <button type="button" id="cancel_delete"
                                                    class="btn btn-secondary show-on-remove-btn-click "
                                                    style="display:none">Cancel</button>
                                                <button type="button" class="btn btn-danger mx-3 show-on-remove-btn-click"
                                                    style="display:none" id="final_delete_btn">Delete Selected
                                                    Suppliers</button>
                                            </div>

                                        </div>
                                        <form id="delete-form" action="./includes/remove.php" method="post" style="display: none;">
                                            <input type="hidden" name="items_array" id="product-ids">
                                            <input type="hidden" name="delete_form_id" value="delete_supplier"
                                                id="delete_supplier">
                                        </form>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9">No Suppliers found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                            </table>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="./javascript/remove.js"></script>
    <script src="./javascript/edit.js"></script>
</body>

</html>