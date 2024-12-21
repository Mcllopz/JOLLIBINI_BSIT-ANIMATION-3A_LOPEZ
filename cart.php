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
              c.product_id, 
              c.total_stock AS quantity, 
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
          // Prepare the insert statement for the purchase table
          $purchaseInsertQuery = "
              INSERT INTO purchase (user_id, product_id, quantity, total_amount, date, status)
              VALUES (?, ?, ?, ?, ?, 'Pending')";
          
          $purchaseStmt = $conn->prepare($purchaseInsertQuery);
          if (!$purchaseStmt) {
              die("Error preparing purchase statement: " . $conn->error);
          }
  
          // Process each cart item and insert it into the purchase table
          while ($item = $result->fetch_assoc()) {
              $product_id = $item['product_id'];
              $quantity = $item['quantity'];
              $total_amount = $item['price'] * $quantity;
              $date = date("Y-m-d H:i:s"); // Get the current date and time
  
              // Bind parameters and execute the insert query
              $purchaseStmt->bind_param("iiids", $user_id, $product_id, $quantity, $total_amount, $date);
              if (!$purchaseStmt->execute()) {
                  die("Error inserting purchase: " . $purchaseStmt->error);
              }
          }
  
          // Clear the cart after purchase
          $clearCartQuery = "DELETE FROM cart WHERE user_id = ?";
          $clearCartStmt = $conn->prepare($clearCartQuery);
          if (!$clearCartStmt) {
              die("Error preparing clear cart statement: " . $conn->error);
          }
  
          $clearCartStmt->bind_param("i", $user_id);
          if (!$clearCartStmt->execute()) {
              die("Error clearing cart: " . $clearCartStmt->error);
          }
  
          echo "<script>alert('Purchase completed successfully')</script>!";
      } else {
        echo "<script>alert('Your cart is empty')</script>!";
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
      
      $stock_prod = $_POST['stock_prod'];
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
          if($new_qty <= $stock_prod ){  
            $update_query = "UPDATE cart SET total_stock = ? WHERE cart_id = ? AND user_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("iii", $new_qty, $cart_id, $_SESSION['user_id']);
            $stmt->execute();
          }else{ 

            header('Location: ./cart.php?stock=exceed');

            exit();
          }
        }
        
        header("Location: cart.php");
        exit();
    }

    // Fetch cart items for the logged in user
    $user_id = $_SESSION['user_id'];
    $query = "SELECT cart.cart_id, cart.user_id, cart.product_id, cart.total_stock, 
                    products.product_name, products.price, products.image_path, products.stock
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
                  <a class="nav-link nav-links" ria-current="page" href="./index.php">
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
            </div>
          </div>
        </nav>
      </div>
    </nav>
     



    <section class="bg-light">
        <div class="container py-5">
            <h2 class="text-success mb-4"><a href="cart.php" class="text-decoration-none">Your Shopping Cart</a> | <a href="purchase.php" class="text-decoration-none text-success">Purchase History </a></h2>
            <?php
              if(isset($_GET['stock'])){
                ?>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Warning:"><use xlink:href="#exclamation-triangle-fill"/></svg>
                  <div>
                    Stock exceeds the limit
                  </div>
                </div>    
            <?php
              }
            
            ?>
            <?php if ($result->num_rows > 0): ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <?php while ($item = $result->fetch_assoc()): 
                                    $subtotal = $item['price'] * $item['total_stock'];
                                    $total += $subtotal;
                                ?>
                                    <div class="row mb-4 border-bottom pb-4">
                                        <div class="col-md-3">
                                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                            class="img-fluid rounded" 
                                            alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="text-dark"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                                            <p class="mb-2">Price: ₱<?php echo number_format($item['price'], 2); ?></p>
                                            
                                            <!-- Quantity Update Form -->
                                            <div class="d-flex align-items-center mb-3">
                                                <form method="POST" class="d-inline-block me-2">
                                                    <input type="hidden" name="stock_prod" value="<?php echo $item['stock']; ?>">
                                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                    <input type="hidden" name="action" value="decrease">
                                                    <button type="submit" name="update_quantity" class="btn btn-outline-secondary btn-sm">-</button>
                                                </form>
                                                
                                                <span class="px-3 border"><?php echo $item['total_stock']; ?></span>
                                                
                                                <form method="POST" class="d-inline-block ms-2">
                                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                    <input type="hidden" name="stock_prod" value="<?php echo $item['stock']; ?>">
                                                    <input type="hidden" name="action" value="increase">
                                                    <button type="submit" name="update_quantity" class="btn btn-outline-secondary btn-sm">+</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <p class="h5 mb-3">₱<?php echo number_format($subtotal, 2); ?></p>
                                            
                                            <!-- Remove Item Form -->
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to remove this item?');">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                <button type="submit" name="remove_item" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Order Summary</h5>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Subtotal</span>
                                    <span>₱<?php echo number_format($total, 2); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Shipping</span>
                                    <span>₱<?php echo number_format($shipping, 2); ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4">
                                    <strong>Total</strong>
                                    <strong>₱<?php echo number_format($total + $shipping, 2); ?></strong>
                                </div>
                                <form method="POST" action="">
                                    <button type="submit" name="checkout" class="btn btn-success w-100">Proceed to Checkout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Your cart is empty</h3>
                    <p class="mb-4">Looks like you haven't added any items to your cart yet.</p>
                    <a href="shop.php" class="btn btn-success">Continue Shopping</a>
                </div>
            <?php endif; ?>
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