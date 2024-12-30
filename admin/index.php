<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Add your database password
$dbname = "pastrycorner"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Total Orders
$sql_total_orders = "SELECT COUNT(*) as total_orders FROM purchase";
$result_orders = $conn->query($sql_total_orders);
$total_orders = $result_orders->fetch_assoc()['total_orders'] ?? 0;

// Fetch Total Customers
$sql_total_customers = "SELECT COUNT(DISTINCT user_id) as total_customers FROM purchase";
$result_customers = $conn->query($sql_total_customers);
$total_customers = $result_customers->fetch_assoc()['total_customers'] ?? 0;

// Fetch Total Sales
$sql_total_sales = "SELECT SUM(total_amount) as total_sales FROM purchase";
$result_sales = $conn->query($sql_total_sales);
$total_sales = $result_sales->fetch_assoc()['total_sales'] ?? 0;



// Fetch Recent Orders
$sql_recent_orders = "
    SELECT 
        p.purchase_id,
        u.fullname AS user_name,
        p.date AS order_date,
        p.status
    FROM purchase p
    JOIN users u ON p.user_id = u.user_id
    ORDER BY p.date DESC
    LIMIT 5
";

$result_recent_orders = $conn->query($sql_recent_orders);
$recent_orders = [];
if ($result_recent_orders && $result_recent_orders->num_rows > 0) {
    while ($row = $result_recent_orders->fetch_assoc()) {
        $recent_orders[] = $row;
    }
}




// Close connection
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
			<li class="active">
				<a href="./index.php">
					<i class='bx bxs-dashboard'></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="./store.php">
					<i class='bx bxs-shopping-bag-alt'></i>
					<span class="text">Store</span>
				</a>
			</li>
			<li>
				<a href="./orders.php">
					<i class='bx bxs-doughnut-chart'></i>
					<span class="text">Orders</span>
				</a>
			</li>

		</ul>
		<ul class="side-menu">
			<li>

				<form action="../login-process/process.php" method="POST">

					<button type="submit" name="logoutBtn" style="border: none; background-color: #f8f9f8;">
			<li><a href="#" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a>
			</li>
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
			<i class='bx bx-menu'></i>
			<a href="#" class="nav-link">Pastry Corner</a>
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
				</div>
			</form>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>

			<a href="#" class="profile">
				<i class='bx bxs-log-out' style="font-size: 1.75rem;color: lightblue;"></i>
			</a>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="./index.html">Home</a>
						</li>
					</ul>
				</div>
				<a href="#" class="btn-download">
					<i class='bx bxs-cloud-download'></i>
					<span class="text">Download PDF</span>
				</a>
			</div>

			<ul class="box-info">
				<li>
                    <i class='bx bxs-calendar-check'></i>
                    <span class="text">
                        <h3><?= $total_orders; ?></h3>
                        <p>Total Orders</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-group'></i>
                    <span class="text">
                        <h3><?= $total_customers; ?></h3>
                        <p>Total Customers</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-dollar-circle'></i>
                    <span class="text">
                        <h3>$<?= number_format($total_sales, 2); ?></h3>
                        <p>Total Sales</p>
                    </span>
                </li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Recent Orders</h3>
						<i class='bx bx-search'></i>
						<i class='bx bx-filter'></i>
					</div>
					<table>
						<thead>
							<tr >
								<th class="text-center">User</th>
								<th class="text-center">Date Order</th>
								<th class="text-center">Status</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($recent_orders as $order): ?>
								<tr class="text-center">
									<td class="d-flex justify-content-center">
										<img src="img/people.png" alt="User Image">
										<p><?= htmlspecialchars($order['user_name']); ?></p>
									</td>
									<td><?= date('d-m-Y', strtotime($order['order_date'])); ?></td>
									<td><span class="status <?php
										if(strtolower($order['status']) === 'delivered/paid'){
											echo 'completed';
										}else if( strtolower($order['status']) === 'set to ship'){
											echo 'process';
										}else{
											echo 'pending';
										}

									?>"><?= htmlspecialchars($order['status']); ?></span></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->


	<script src="script.js"></script>
</body>

</html>