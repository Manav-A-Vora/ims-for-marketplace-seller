<?php
session_start();
if (!isset($_SESSION["username"])) {
	header("location: ./includes/login.php");
	exit();
}


include ("./includes/_dbconnect.php");
$sql = "SELECT * FROM orders";
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
	<title>Inventory | Orders</title>
</head>

<body>
	<div class="d-flex">

		<?php include ("./includes/sidebar.php") ?>


		<div class="container-fluid">
			<div class="row top-panel">
				<form class="form-inline my-2 my-lg-0 col-10 d-flex ">
					<input class="form-control mr-sm-2 my-4 " type="search" placeholder="Search" aria-label="Search">
					<button class="btn btn-outline-success mx-4 my-4 " type="submit">Search</button>
				</form>
				<div class="add-remove-btns col">
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
									<h1 class="modal-title fs-5" id="exampleModalLabel">Add Order</h1>
									<button type="button" class="btn-close" data-bs-dismiss="modal"
										aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<form class="row g-3 needs-validation" name="form_id" value="add_product_form"
										action="./includes/add_order.php" method="post" novalidate>
										<input type="hidden" name="form_id" value="add_order_form">
										<div class="col-md-12">
											<label for="order_date" class="form-label">Order Date</label>
											<input type="date" class="form-control" id="order_date" name="order_date"
												placeholder="" required>
											<div class="invalid-feedback">
												Enter a valid Date
											</div>
										</div>
										<div class="col-md">
											<label for="order_product" class="form-label">Product </label>
											<select class="form-select" id="order_product" name="order_product"
												required>
												<option disabled placeholder="">...</option>
												<?php
												$get_products_query = "SELECT product_id, product_name, product_image FROM products";
												$get_products_query_result = $conn->query($get_products_query);
												if ($get_products_query_result->num_rows > 0): ?>
													<?php while ($row = $get_products_query_result->fetch_assoc()): ?>
														<option value="<?php echo $row["product_id"]; ?>">
															<?php echo $row["product_name"]; ?>
														</option>
													<?php endwhile; ?>
												<?php else: ?>
													<option disabled>Please add Products first.</option>
												<?php endif; ?>
											</select>
											<div class="invalid-feedback">
												Select a valid Product .
											</div>
										</div>
										<div class="col-md-12">
											<label for="no_of_order_product_units" class="form-label">Total Units
											</label>
											<div class="input-group has-validation">
												<input type="number" class="form-control" id="no_of_order_product_units"
													name="no_of_order_product_units"
													aria-describedby="inputGroupPrepend" placeholder="ex: 5" required>
												<div class="invalid-feedback">
													Enter Number of units ordered .
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<label for="order_status" class="form-label">Order Status </label>
											<select class="form-select" id="order_status" name="order_status" value=""
												required>
												<option selected disabled placeholder="">...</option>
												<option value="Processing">Processing</option>
												<option value="Packed">Packed</option>
												<option value="Shipped">Shipped</option>
												<option value="Delivered">Delivered</option>
											</select>
											<div class="invalid-feedback">
												Select a valid order status.
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary"
												data-bs-dismiss="modal">Close
											</button>
											<button class="btn btn-primary" type="submit">Add order</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Display error messages -->
			<?php if (isset($_SESSION['error_message'])): ?>
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<?php
					echo $_SESSION['error_message'];
					unset($_SESSION['error_message']);
					?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			<?php endif; ?>

			<?php if (isset($_SESSION['success_message'])): ?>
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<?php
					echo $_SESSION['success_message'];
					unset($_SESSION['success_message']);
					?>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			<?php endif; ?>

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

			<div class="row mx-4 table-container my-4">
				<table class="table table-responsive">
					<thead>
						<tr>
							<th scope="col">Order Date</th>
							<th scope="col">Product</th>
							<th scope="col">Units</th>
							<th scope="col">Total</th>
							<th scope="col">Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>

						<?php if ($result->num_rows > 0): ?>
							<?php while ($row = $result->fetch_assoc()): ?>
								<tr>
									<td><?php echo $row["order_date"]; ?></td>
									<td>
										<?php
										// Prepare and execute SQL query to fetch product image
										$product_image_sql = "SELECT product_image FROM products WHERE product_id = ?";
										$product_image_stmt = $conn->prepare($product_image_sql);
										$product_image_stmt->bind_param("i", $row["product_id"]);
										$product_image_stmt->execute();
										$product_image_stmt->bind_result($product_image);
										$product_image_stmt->fetch();
										$product_image_stmt->close();
										?>
										<!-- Display the product image -->
										<img src="<?php echo "./assets/" . htmlspecialchars($product_image); ?>"
											alt="product_image" class="product-image">
									</td>
									<td><?php echo $row["no_of_units"]; ?></td>
									<td><?php echo $row["order_total"]; ?></td>
									<td><?php echo $row["order_status"]; ?></td>
									<td>
										<!-- Button trigger modal -->
										<button type="button" class="btn btn-primary edit-btn" data-bs-toggle="modal"
											data-bs-target="#editModal<?php echo $row['order_id']; ?>">
											Edit
										</button>

										<!-- Modal -->
										<div class="modal fade" id="editModal<?php echo $row['order_id']; ?>" tabindex="-1"
											aria-labelledby="editModalLabel<?php echo $row['order_id']; ?>" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h1 class="modal-title fs-5"
															id="editModalLabel<?php echo $row['order_id']; ?>">Edit
															Order</h1>
														<button type="button" class="btn-close" data-bs-dismiss="modal"
															aria-label="Close"></button>
													</div>
													<div class="modal-body">
														<form class="row g-3 needs-validation" name="form_id"
															value="edit_order_form" action="./includes/edit.php" method="post"
															enctype="multipart/form-data" novalidate>
															<input type="hidden" name="order_id"
																value="<?php echo $row['order_id']; ?>">
															<input type="hidden" name="form_id" value="edit_order_form">
															<!-- Your other form fields here, pre-fill with existing data -->
															<div class="col-md-12">
																<label for="edit_order_date" class="form-label">Order
																	Date</label>
																<input type="date" class="form-control" id="edit_order_date"
																	name="edit_order_date"
																	value="<?php echo $row['order_date']; ?>" required>
																<div class="invalid-feedback">
																	Enter a valid date of Order
																</div>
															</div>
															<div class="col-md">
																<label for="edit_order_product" class="form-label">Product
																</label>
																<select class="form-select" id="edit_order_product"
																	name="edit_order_product" required>
																	<?php
																	$product_name_sql = 'select product_name from products where product_id = ?';
																	$stmt = $conn->prepare($product_name_sql);
																	$stmt->bind_param('i', $row['product_id']);
																	$stmt->execute();
																	$stmt->bind_result($product_name);
																	$stmt->fetch();
																	$stmt->close();
																	?>
																	<option selected value="<?php echo $row['product_id'] ?>"
																		placeholder="">
																		<?php echo $product_name; ?>
																	</option>
																	<?php
																	$get_products_query = "select product_id, product_name, product_image from products";
																	$get_products_query_result = $conn->query($get_products_query);
																	while ($row_sub = $get_products_query_result->fetch_assoc()): ?>
																		<option value="<?php echo $row_sub["product_id"]; ?>">
																			<?php echo $row_sub["product_name"]; ?>
																		</option>
																	<?php endwhile; ?>
																</select>
																<div class="invalid-feedback">
																	Select a valid Product .
																</div>
															</div>

															<div class="col-md-12">
																<label for="edit_no_of_order_product_units"
																	class="form-label">Total
																	Units
																</label>
																<div class="input-group has-validation">
																	<input type="number" class="form-control"
																		id="edit_no_of_order_product_units"
																		name="edit_no_of_order_product_units"
																		value="<?php echo $row["no_of_units"]; ?>"
																		aria-describedby="inputGroupPrepend"
																		placeholder="<?php echo $row["no_of_units"]; ?>"
																		required>
																	<div class="invalid-feedback">
																		Enter Number of units ordered
																	</div>
																</div>
															</div>


															<div class="col-md">
																<label for="edit_order_status" class="form-label">Order
																	status
																</label>
																<select class="form-select" id="edit_order_status"
																	name="edit_order_status" required>
																	<option selected value="<?php echo $row["order_status"]; ?>"
																		placeholder=""><?php echo $row["order_status"]; ?>
																	</option>
																	<option value="ordered">Ordered</option>
																	<option value="packing">Packing</option>
																	<option value="delivered">Delivered</option>
																	<option value="cancelled">Cancelled</option>
																</select>
																<div class="invalid-feedback">
																	Select a valid order ststus
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
								<td colspan="6">No orders found</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</body>

</html>