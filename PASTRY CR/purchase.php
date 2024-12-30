<?php
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pastrycorner";

    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
      $user_id = intval($_SESSION['user_id']); // Ensure user_id is an integer
  
      // Query to join cart and products tables
      $cartItemsQuery = "
          SELECT 
              c.total_stock, 
              p.product_name, 
              p.price 
          FROM 
              cart AS c
          INNER JOIN 
              products AS p
          ON 
              c.product_id = p.product_id
          WHERE 
              c.user_id = ?";
  
      $stmt = $conn->prepare($cartItemsQuery);
  
      if (!$stmt) {
          die("Error preparing statement: " . $conn->error);
      }
  
      // Use "i" because user_id is an integer
      $stmt->bind_param("i", $user_id);
  
      if (!$stmt->execute()) {
          die("Error executing query: " . $stmt->error);
      }
  
      $result = $stmt->get_result();
  
      if ($result->num_rows > 0) {
          $cartItems = [];
          while ($item = $result->fetch_assoc()) {
              $subtotal = $item['price'] * $item['total_stock'];
              $cartItems[] = [
                  'product_name' => $item['product_name'],
                  'price' => $item['price'],
                  'total_stock' => $item['total_stock'],
                  'subtotal' => $subtotal
              ];
          }
  
          // Generate JavaScript to display cart details
          $cartDetailsJS = '<script>
              function displayCartDetails() {
                  const cartItems = ' . json_encode($cartItems) . ';
                  if (cartItems.length === 0) {
                      alert("Your cart is empty.");
                      return;
                  }
  
                  let cartDetails = "Your Cart:\\n\\n";
                  cartItems.forEach((item, index) => {
                      cartDetails += `${index + 1}. ${item.product_name}\\n`;
                      cartDetails += `   Price: ₱${item.price.toFixed(2)}\\n`;
                      cartDetails += `   Quantity: ${item.total_stock}\\n`;
                      cartDetails += `   Subtotal: ₱${item.subtotal.toFixed(2)}\\n\\n`;
                  });
                  cartDetails += `Shipping: ₱50.00\\n`;
                  cartDetails += `Total: ₱${(cartItems.reduce((sum, item) => sum + item.subtotal, 0) + 50).toFixed(2)}\\n`;
  
                  alert(cartDetails);
              }
              displayCartDetails();
          </script>';
          echo $cartDetailsJS;
      } else {
          echo '<script>alert("Your cart is empty.");</script>';
      }
  }
  
  
    // Handle Remove Item
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
        $cart_id = intval($_POST['cart_id']);
        $user_id = intval($_SESSION['user_id']);
    
        $delete_query = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
        $stmt = $conn->prepare($delete_query);
    
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
    
        $stmt->bind_param("ii", $cart_id, $user_id);
    
        if ($stmt->execute()) {
            echo "Query executed: DELETE FROM cart WHERE cart_id = $cart_id AND user_id = $user_id";
            header("Location: cart.php?success=removed");
            exit();
        } else {
            die("Error executing query: " . $stmt->error);
        }
    }
    
    // Handle Quantity Update
    if (isset($_POST['update_quantity'])) {
        $cart_id = $_POST['cart_id'];
        $action = $_POST['action'];
        
        // Get current quantity
        $qty_query = "SELECT total_stock FROM cart WHERE cart_id = ? AND user_id = ?";
        $stmt = $conn->prepare($qty_query);
        $stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_qty = $result->fetch_assoc()['total_stock'];
        
        // Calculate new quantity
        $new_qty = ($action === 'increase') ? $current_qty + 1 : $current_qty - 1;
        
        // Make sure quantity doesn't go below 1
        if ($new_qty >= 1) {
            $update_query = "UPDATE cart SET total_stock = ? WHERE cart_id = ? AND user_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("iii", $new_qty, $cart_id, $_SESSION['user_id']);
            $stmt->execute();
        }
        
        header("Location: cart.php");
        exit();
    }

    // Fetch cart items for the logged in user
    $user_id = $_SESSION['user_id'];
    $query = "SELECT cart.cart_id, cart.user_id, cart.product_id, cart.total_stock, 
                    products.product_name, products.price, products.image_path
            FROM cart
            JOIN products ON cart.product_id = products.product_id
            WHERE cart.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // while ($item = $result->fetch_assoc()): 
    //     $subtotal = $item['price'] * $item['total_stock'];
    //     $total += $subtotal;
    // Calculate total
    $total = 0;
    $shipping = 50; // Fixed shipping cost
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pastry Corner</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="fontawesome-free-6.7.0-web/css/all.css" />
    <link
      rel="stylesheet"
      href="fontawesome-free-6.7.0-web/css/fontawesome.css"
    />
    <script defer src="js/bootstrap.bundle.min.js"></script>

  </head>


  <body>

    <div>
      <nav class="navbar navbar-expand-lg bg-dark">
        <div class="container">
          <div class="w-100 d-flex justify-content-between">
            <div>
              <i class="fa-solid fa-envelope text-light contact-info"></i>
              <a
                href=""
                class="navbar-sm-brand text-light text-decoration-none contact-info"
              >
                PastryCorner.com
              </a>
              <i class="fa-solid fa-phone contact-info text-light"></i>
              <a
                href=""
                class="navbar-sm-brand text-white text-decoration-none contact-info"
              >
                09568899321
              </a>
            </div>
          </div>
        </div>
      </nav>
    </div>

    <nav class="navbar navbar-expand-lg bg-light">
      <div class="container d-flex justify-content-between">
        <div>
          <h1 class="text-success">PastryCorner</h1>
        </div>
        <nav class="navbar navbar-expand-lg bg-light">
          <div class="container-fluid">
            <div class="collapse navbar-collapse">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item nav-items">
                  <a class="nav-link nav-links" ria-current="page" href="./index.html">
                    Home
                  </a>
                </li>
                <li class="nav-item nav-items">
                  <a class="nav-link nav-links" href="./about.html">About</a>
                </li>
                <li class="nav-item nav-items">
                  <a class="nav-link nav-links" href="./shop.php">Shop</a>
                </li>
                <li class="nav-item nav-items">
                  <a class="nav-link nav-links" href="./contact.html">Contact</a>
                </li>
              </ul>
              <div class="position-relative">
              <form action="../login-process/process.php" method="POST" class="text-decoration-none text-dark">
                <a href="" class="text-decoration-none text-dark">
                  <i class="fa-solid fa-magnifying-glass nav-icon"></i>
                </a>
                <a href="./cart.php" class="text-decoration-none text-dark">
                  <i class="fa-solid fa-cart-arrow-down nav-icon"></i>
                </a>
                  <button type="submit" name="logoutBtn" class="border-0 bg-light">   
                    <i class="fa-solid fa-sign-out nav-icon"></i>
                  </button>
                </form>
              </div>
              <div class="position-absolute rounded-circle cart">
                
              </div>
              <div class="position-absolute rounded-circle user">
                
              </div>
            </div>
          </div>
        </nav>
      </div>
    </nav>
     



    <section class="bg-light">
        <div class="container py-5">
            <h2 class="text-success mb-4"><a href="cart.php" class="text-decoration-none text-success">Your Shopping Cart</a> | <a href="purchase.php" class="text-decoration-none ">Purchase History </a></h2>
                
            <?php

            // Ensure a valid user session exists
            if (!isset($_SESSION['user_id'])) {
                die("You must be logged in to view the purchase cart.");
            }

            $user_id = intval($_SESSION['user_id']); // Ensure user_id is an integer

            // Database connection (replace with your connection details)
            $conn = new mysqli("localhost", "root", "", "pastrycorner");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to retrieve purchase cart data
            $query = "
                SELECT 
                    p.product_name,
                    p.description,
                    p.price,
                    p.image_path,
                    pu.quantity,
                    pu.total_amount,
                    pu.date,
                    pu.status
                FROM 
                    purchase AS pu
                INNER JOIN 
                    products AS p
                ON 
                    pu.product_id = p.product_id
                WHERE 
                    pu.user_id = ?";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                die("Error executing query: " . $stmt->error);
            }

            $result = $stmt->get_result();

            echo '<div class="container mt-4">';
            echo '<h2 class="text-success">Pending Orders</h2>';
            echo '<table class="table table-striped">';
            echo '<thead class="thead-dark">';
            echo '<tr class="text-center">';
            echo '<th>Image</th>';
            echo '<th>Product Name</th>';
            echo '<th>Price</th>';
            echo '<th>Quantity</th>';
            echo '<th>Total Amount</th>';
            echo '<th>Date Ordered</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            // Check if there are items in the cart
                while ($row = $result->fetch_assoc()) {
                    if($row['status'] === 'Pending')
                    {
                        echo '<tr class="text-center">';
                        echo '<td><img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['product_name']) . '" class="img-fluid" style="width: 100px; height: auto;"></td>';
                        echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
                        echo '<td>₱' . number_format($row['price'], 2) . '</td>';
                        echo '<td>' . intval($row['quantity']) . '</td>';
                        echo '<td>₱' . number_format($row['total_amount'], 2) . '</td>';
                        echo '<td>' . htmlspecialchars($row['date']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                        echo '</tr>';
                    }
                }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';

         

            // Close the statement and connection
            $stmt->close();
            $conn->close();
            ?>

            <?php

            // Ensure a valid user session exists
            if (!isset($_SESSION['user_id'])) {
                die("You must be logged in to view the purchase cart.");
            }

            $user_id = intval($_SESSION['user_id']); // Ensure user_id is an integer

            // Database connection (replace with your connection details)
            $conn = new mysqli("localhost", "root", "", "pastrycorner");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to retrieve purchase cart data
            $query = "
                SELECT 
                    p.product_name,
                    p.description,
                    p.price,
                    p.image_path,
                    pu.quantity,
                    pu.total_amount,
                    pu.date,
                    pu.status
                FROM 
                    purchase AS pu
                INNER JOIN 
                    products AS p
                ON 
                    pu.product_id = p.product_id
                WHERE 
                    pu.user_id = ?";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                die("Error executing query: " . $stmt->error);
            }

            $result = $stmt->get_result();

            echo '<div class="container mt-4">';
            echo '<h2 class="text-warning">Set to Ship</h2>';
            echo '<table class="table table-striped">';
            echo '<thead class="thead-dark">';
            echo '<tr class="text-center">';
            echo '<th>Image</th>';
            echo '<th>Product Name</th>';
            echo '<th>Price</th>';
            echo '<th>Quantity</th>';
            echo '<th>Total Amount</th>';
            echo '<th>Date Ordered</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            // Check if there are items in the cart
                while ($row = $result->fetch_assoc()) {
                    if($row['status'] === 'Set to Ship')
                    {
                        echo '<tr class="text-center">';
                        echo '<td><img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['product_name']) . '" class="img-fluid" style="width: 100px; height: auto;"></td>';
                        echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
                        echo '<td>₱' . number_format($row['price'], 2) . '</td>';
                        echo '<td>' . intval($row['quantity']) . '</td>';
                        echo '<td>₱' . number_format($row['total_amount'], 2) . '</td>';
                        echo '<td>' . htmlspecialchars($row['date']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                        echo '</tr>';
                    }
                }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';



            // Close the statement and connection
            $stmt->close();
            $conn->close();
            ?>  

            <?php

            // Ensure a valid user session exists
            if (!isset($_SESSION['user_id'])) {
                die("You must be logged in to view the purchase cart.");
            }

            $user_id = intval($_SESSION['user_id']); // Ensure user_id is an integer

            // Database connection (replace with your connection details)
            $conn = new mysqli("localhost", "root", "", "pastrycorner");

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to retrieve purchase cart data
            $query = "
                SELECT 
                    p.product_name,
                    p.description,
                    p.price,
                    p.image_path,
                    pu.quantity,
                    pu.total_amount,
                    pu.date,
                    pu.status
                FROM 
                    purchase AS pu
                INNER JOIN 
                    products AS p
                ON 
                    pu.product_id = p.product_id
                WHERE 
                    pu.user_id = ?";

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("i", $user_id);
            if (!$stmt->execute()) {
                die("Error executing query: " . $stmt->error);
            }

            $result = $stmt->get_result();

            echo '<div class="container mt-4">';
            echo '<h2 class="text-primary">Completed Orders</h2>';
            echo '<table class="table table-striped">';
            echo '<thead class="thead-dark">';
            echo '<tr class="text-center">';
            echo '<th>Image</th>';
            echo '<th>Product Name</th>';
            echo '<th>Price</th>';
            echo '<th>Quantity</th>';
            echo '<th>Total Amount</th>';
            echo '<th>Date Ordered</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            // Check if there are items in the cart
                while ($row = $result->fetch_assoc()) {
                    if($row['status'] === 'Delivered/Paid')
                    {
                        echo '<tr class="text-center">';
                        echo '<td><img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['product_name']) . '" class="img-fluid" style="width: 100px; height: auto;"></td>';
                        echo '<td>' . htmlspecialchars($row['product_name']) . '</td>';
                        echo '<td>₱' . number_format($row['price'], 2) . '</td>';
                        echo '<td>' . intval($row['quantity']) . '</td>';
                        echo '<td>₱' . number_format($row['total_amount'], 2) . '</td>';
                        echo '<td>' . htmlspecialchars($row['date']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                        echo '</tr>';
                    }
                }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';



            // Close the statement and connection
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </section>
    <footer class="bg-dark">
      <div class="container">
        <div class="row">
          <div class="col-md-4 pt-5 logo">
            <h2 class="text-success h2 border-bottom border-light pb-3">
              PastryCorner
            </h2>
            <ul class="list-unstyled text-light">
              <li>
                <i class="fa-solid fa-phone"></i>
                <a href="" class="text-decoration-none text-light">
                  09568899321
                </a>
              </li>
              <li>
                <i class="fa-solid fa-location-dot"></i>
                <a href="" class="text-decoration-none text-light">
                  143 Rawis Legazpi City 
                </a>
              </li>
              <li>
                <i class="fa-solid fa-envelope"></i>
                <a href="" class="text-decoration-none text-light">
                  PastryCorner.com
                </a>
              </li>
            </ul>
          </div>
          <div class="col-md-4 pt-5">
            <h2 class="text-light h2 border-bottom border-light pb-3">
              Products
            </h2>
            <ul class="list-unstyled text-light footer-link-list list">
              <li><a href="" class="text-decoration-none text-light">
                  Cookies</a></li>
              <li><a href="" class="text-decoration-none text-light">
                  Croissant</a></li>
              <li><a href="" class="text-decoration-none text-light">
                  Macarons</a></li>
             
            </ul>
          </div>
          <div class="col-md-4 pt-5">
            <h2 class="text-light h2 border-bottom border-light pb-3">
              Further Info</h2>
            <ul class="list-unstyled text-light footer-link-list list">
              <li><a href="./index.html" class="text-decoration-none text-light">
                  Home</a></li>
              <li><a href="" class="text-decoration-none text-light">
                  About Us</a></li>
              <li><a href="" class="text-decoration-none text-light">
                  Shop Locations</a></li>
              <li><a href="" class="text-decoration-none text-light">
                  FAQs</a></li>
              <li><a href="" class="text-decoration-none text-light">
                  Contact</a></li>
            </ul>
          </div>
          <div class="row text-light">
            <div class="col-12 mb-3">
              <div class="w-100 my-3 border-top border-light"></div>
            </div>
            <div class="col-auto me-auto w-100">
              <div class="d-flex justify-content-between">
                <div class="d-flex">
                  <li class="border border-light rounded-circle text-center
                      list-unstyled ms-2 f-links">
                    <a href="http://facebook.com/" target="_blank" 
                      class="text-light">
                      <i class="f-links fa-brands fa-facebook-f"></i>
                    </a>
                  </li>
                  <li class="border border-light rounded-circle text-center
                      list-unstyled ms-2 f-links">
                    <a href="http://instagram.com/" target="_blank"
                      class="text-light">
                      <i class="f-links fa-brands fa-instagram"></i>
                    </a>
                  </li>
                  <li class="border border-light rounded-circle text-center
                       list-unstyled ms-2 f-links">
                    <a href="http://twitter.com/" target="_blank"
                      class="text-light">
                      <i class="f-links fa-brands fa-twitter"></i>
                    </a>
                  </li>
                  <li class="border border-light rounded-circle text-center
                       list-unstyled ms-2 f-links">
                    <a href="http://linkedin.com/" target="_blank"
                       class="text-light">
                      <i class="f-links fa-brands fa-linkedin"></i>
                    </a>
                  </li>
                </div>
                <div class="col-auto d-flex text-center">
                  <input type="text" class="bg-dark border-light rounded"
                       placeholder="Email Address">
                  <div class="btn btn-success text-light rounded f-email">
                      Subscribe</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="w-100 bg-dark py-3">
        <div class="container">
          <div class="row pt-2">
            <div class="col-12">
              <div class="text-left text-light">
                Copyright &copy; 2021 PastryCorner
                | Designed by <a href="" class="text-decoration-none
                  text-success">PastryCorner</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>

   
  </body>
</html>