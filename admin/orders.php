<?php


// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pastrycorner';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Check if purchase_id is sent via POST for "Set to Ship"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setToShipBtn'])) {
    $purchase_id = intval($_POST['purchase_id']); // Sanitize input

    // Retrieve the product_id and quantity from the purchase table to update the stock
    $stmt = $conn->prepare("SELECT product_id, quantity FROM purchase WHERE purchase_id = ?");
    $stmt->bind_param("i", $purchase_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $purchase = $result->fetch_assoc();
        $product_id = $purchase['product_id'];
        $quantity_ordered = $purchase['quantity'];

        // Check the current stock of the product
        $stockQuery = $conn->prepare("SELECT stock FROM products WHERE product_id = ?");
        $stockQuery->bind_param("i", $product_id);
        $stockQuery->execute();
        $stockResult = $stockQuery->get_result();

        if ($stockResult->num_rows > 0) {
            $stock = $stockResult->fetch_assoc()['stock'];

            // Check if enough stock is available
            if ($stock >= $quantity_ordered) {
                // Update the product stock by deducting the ordered quantity
                $newStock = $stock - $quantity_ordered;
                $updateStockStmt = $conn->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
                $updateStockStmt->bind_param("ii", $newStock, $product_id);
                $updateStockStmt->execute();
                $updateStockStmt->close();

                // Now update the purchase status to "Set to Ship"
                $updateStatusStmt = $conn->prepare("UPDATE purchase SET status = 'Set to Ship' WHERE purchase_id = ?");
                $updateStatusStmt->bind_param("i", $purchase_id);
                
                if ($updateStatusStmt->execute()) {
                    // Redirect back to the main page
                    header("Location: orders.php");
                    exit();
                } else {
                    echo "Error updating record: " . $conn->error;
                }
                
                $updateStatusStmt->close();
            } else {
                // Insufficient stock: Show an alert and delete the order

                // Delete the purchase record since stock is insufficient
                $deleteStmt = $conn->prepare("DELETE FROM purchase WHERE purchase_id = ?");
                $deleteStmt->bind_param("i", $purchase_id);
                if ($deleteStmt->execute()) {
                    echo "<script>window.location.href = 'orders.php?deleted=Insufficient stock available for this order. The order will be automatically deleted.';</script>";  // Redirect to orders page after deletion
                } else {
                    echo "Error deleting record: " . $conn->error;
                }
                $deleteStmt->close();
            }
        } else {

            // If product is not found, delete the order as well
            $deleteStmt = $conn->prepare("DELETE FROM purchase WHERE purchase_id = ?");
            $deleteStmt->bind_param("i", $purchase_id);
            if ($deleteStmt->execute()) {
                echo "<script>window.location.href = 'orders.phpdeleted=Product not found in the inventory. The order will be automatically deleted.';</script>";  // Redirect to orders page after deletion
            } else {
                echo "Error deleting record: " . $conn->error;
            }
            $deleteStmt->close();
        }
    } else {

        // If purchase not found, delete the order
        $deleteStmt = $conn->prepare("DELETE FROM purchase WHERE purchase_id = ?");
        $deleteStmt->bind_param("i", $purchase_id);
        if ($deleteStmt->execute()) {
            echo "<script>window.location.href = 'orders.php?deleted=Purchase not found. The order will be automatically deleted.';</script>";  // Redirect to orders page after deletion
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $deleteStmt->close();
    }

    $stmt->close();
}

// Other code for handling "Delivered/Paid" and other actions...




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmBtn'])) {
    $purchase_id = intval($_POST['purchase_id']); // Sanitize input
    $update_status = 'Delivered/Paid';

    // Update the status based on the input
    $stmt = $conn->prepare("UPDATE purchase SET status = 'Delivered/Paid' WHERE purchase_id = ?");
    $stmt->bind_param("i", $purchase_id);

    if ($stmt->execute()) {
        // Redirect back to the orders page
        header("Location: orders.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
} 



$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

	<title>AdminHub</title>
    <style>
		a {
			text-decoration: none;
		}
	</style>
</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxs-smile'></i>
			<span class="text">AdminHub</span>
		</a>
		<ul class="side-menu top">
			<li >
				<a href="./index.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
                <a href="./store.php">
					<i class='bx bxs-shopping-bag-alt' ></i>
					<span class="text">Store</span>
				</a>
			</li>
			<li class="active">
				<a href="#">
					<i class='bx bxs-doughnut-chart' ></i>
					<span class="text">Orders</span>
				</a>
			</li>
		
		</ul>
		<ul class="side-menu">
			<li>
            <form action="../login-process/process.php" method="POST">
					
                     <button type="submit" name="logoutBtn" style="border: none; background-color: #f8f9f8;">
                        <li><a href="#" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
					</button>
				</form>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->

	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<a href="#" class="nav-link">Pastry Corner</a>
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
			
			
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
        <div class="container mt-5">
            <h1 class="mb-4">Orders </h1>
            <?php
            if(isset($_GET['deleted'])){
                ?>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                  <div>
                    <?=$_GET['deleted']?>
                  </div>
                </div>    
            <?php
              }
            
            ?>

            <?php
            // Database connection (replace with your database credentials)
            $host = 'localhost';
            $username = 'root';
            $password = '';
            $database = 'pastrycorner';

            $conn = new mysqli($host, $username, $password, $database);

            // Check the connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Pending Purchases
            echo "<h2 class='mt-4'>Pending</h2>";
            echo "<table class='table table-bordered'>
                    <thead class='table-dark'>
                        <tr>
                            <th>Product</th>
                            <th>Buyer</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>";

            $pendingQuery = "SELECT purchase.*, products.product_name, users.fullname 
                            FROM purchase 
                            INNER JOIN products ON purchase.product_id = products.product_id 
                            INNER JOIN users ON purchase.user_id = users.user_id 
                            WHERE purchase.status = 'Pending'";
            $pendingResult = $conn->query($pendingQuery);

            if ($pendingResult->num_rows > 0) {
                while ($row = $pendingResult->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['product_name']) . "</td>
                            <td>" . htmlspecialchars($row['fullname']) . "</td>
                            <td>" . htmlspecialchars($row['quantity']) . "</td>
                            <td>₱" . htmlspecialchars($row['total_amount']) . "</td>
                            <td>" . htmlspecialchars($row['date']) . "</td>
                            <td>
                                <form action='' method='POST'>
                                    <input type='hidden' name='purchase_id' value='" . htmlspecialchars($row['purchase_id']) . "'>
                                    <button type='submit' name='setToShipBtn' class='btn btn-primary'>Set to Ship</button>
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No pending purchases found.</td></tr>";
            }

            echo "</tbody></table>";

            // Other statuses
            $statuses = [ 'Set to Ship', 'Delivered/Paid'];
            foreach ($statuses as $status) {
                echo "<h2 class='mt-4'>" . htmlspecialchars($status) . "</h2>";
                echo "<table class='table table-bordered'>
                        <thead class='table-dark'>
                            <tr>
                                <th>Product</th>
                                <th>Buyer</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Date</th>";
                if ($status === 'Set to Ship') {
                    echo "<th>Action</th>"; // Add an Action column for Set to Ship
                }
                echo "</tr>
                        </thead>
                        <tbody>";
            
                $stmt = $conn->prepare("SELECT purchase.*, products.product_name, users.fullname 
                                        FROM purchase 
                                        INNER JOIN products ON purchase.product_id = products.product_id 
                                        INNER JOIN users ON purchase.user_id = users.user_id 
                                        WHERE purchase.status = ?");
                $stmt->bind_param("s", $status);
                $stmt->execute();
                $result = $stmt->get_result();
            
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($row['product_name']) . "</td>
                                <td>" . htmlspecialchars($row['fullname']) . "</td>
                                <td>" . htmlspecialchars($row['quantity']) . "</td>
                                <td>₱" . htmlspecialchars($row['total_amount']) . "</td>
                                <td>" . htmlspecialchars($row['date']) . "</td>";
            
                        if ($status === 'Set to Ship') {
                            echo "<td>
                                    <form action='' method='POST'>
                                        <input type='hidden' name='purchase_id' value='" . htmlspecialchars($row['purchase_id']) . "'>
                                        <button type='submit' name='confirmBtn' class='btn btn-success'>Confirm</button>
                                    </form>
                                  </td>";
                        }
            
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='" . ($status === 'Set to Ship' ? '6' : '5') . "'>No purchases found for this status.</td></tr>";
                }
            
                echo "</tbody></table>";
                $stmt->close();
            }
            

            $conn->close();
            ?>
        </div>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

	<script src="script.js"></script>
</body>
</html>
