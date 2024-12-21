<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pastrycorner';

$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all products from the database
$products = [];
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addProductBtn'])) {
    $productName = $conn->real_escape_string($_POST['product_name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // File upload
    $uploadDir = '../PASTRY CR/images/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imagePath = '';

    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        $imagePath = $uploadFile; // Save the path relative to your application
    } else {
        echo "Error uploading the image. Please try again.";
        exit();
    }

    // Insert into database
    $insertQuery = "INSERT INTO products (product_name, description, price, stock, image_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssdss", $productName, $description, $price, $stock, $imagePath);

    if ($stmt->execute()) {
        echo "Product added successfully!";
    } else {
        echo "Error adding product: " . $conn->error;
    }

    $stmt->close();
    header("Location: store.php"); // Redirect back to the store page
    exit();
}




// Close the database connection
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

    <title>Store</title>
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
        <li><a href="./index.php"><i class='bx bxs-dashboard'></i><span class="text">Dashboard</span></a></li>
        <li class="active"><a href="./store.php"><i class='bx bxs-shopping-bag-alt'></i><span class="text">Store</span></a></li>
        <li><a href="./orders.php"><i class='bx bxs-doughnut-chart'></i><span class="text">Orders</span></a></li>
    </ul>
    <ul class="side-menu">
    <form action="../login-process/process.php" method="POST">
					
					<button type="submit" name="logoutBtn" style="border: none; background-color: #f8f9f8;">
                        <li><a href="#" class="logout"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
					</button>
				</form>
    </ul>
</section>
<!-- SIDEBAR -->

<!-- CONTENT -->
<section id="content">
    <nav>
        <i class='bx bx-menu'></i>
        <a href="#" class="nav-link">Pastry Corner</a>
        <form action="#">
            <div class="form-input">
                <input type="search" placeholder="Search...">
                <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
            </div>
        </form>
        <a href="#" class="profile">
            <i class='bx bxs-log-out' style="font-size: 1.75rem;color: lightblue;"></i>
        </a>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Products</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>

        <table class="table table-bordered table-hover">
            <thead>
                <tr class="text-center">
                    <th >Image</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr class="text-center">
                        <td ><img src="../PASTRY CR/<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" style="width: 8rem; height: 8rem;"></td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['description']); ?></td>
                        <td><?php echo htmlspecialchars($product['price']); ?></td>
                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" required>
                    </div>
                    <button type="submit" name="addProductBtn" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
