<?php
    session_start();

    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pastrycorner";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle Add to Cart action
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
        $user_id = $_POST['user_id'];
        $product_id = $_POST['product_id'];
        $total_stock = $_POST['total_stock'];
        
        // Check if the item is already in cart
        $check_cart = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($check_cart);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing cart item
            $update_cart = "UPDATE cart SET total_stock = total_stock + ? WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($update_cart);
            $stmt->bind_param("iii", $total_stock, $user_id, $product_id);
        } else {
            // Insert new cart item
            $insert_cart = "INSERT INTO cart (user_id, product_id, total_stock) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_cart);
            $stmt->bind_param("iii", $user_id, $product_id, $total_stock);
        }
        
        if ($stmt->execute()) {
            echo "<script>alert('Product added to cart successfully!');</script>";
        } else {
            echo "<script>alert('Error adding product to cart.');</script>";
        }
    }
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
            <div class="row text-center py-3">
                <div class="col m-auto">
                    <h1>Here is our selection of Products</h1>
                    <p class="fw-bold">Discover our featured pastries, each crafted with premium ingredients and unparalleled attention to detail. From indulgent treats to light, flaky delights, these favorites showcase the perfect blend of flavor, texture, and artistry. Whether you're craving something sweet or simply irresistible, our featured products are sure to satisfy.</p>
                </div>
            </div>
        <div class="row d-flex justify-content-center">
<?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pastrycorner";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to fetch all products
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        ?>
            <div class="col-12 col-md-4 mb-4 ">
                <div class="card h-100">
                    <?php
                    echo "<img src='" . $row["image_path"] . "' alt='" . $row["product_name"] . "' width='200' class='card-img-top'>";
                    ?>
                    <div class="card-body">
                        <ul class="list-unstyled d-flex justify-content-between">
                        <li>
                            <i class="text-warning fa-solid fa-star"></i>
                            <i class="text-warning fa-solid fa-star"></i>
                            <i class="text-warning fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </li>
                        <li class="text-muted text-right">₱<?=$row["price"]?></li>
                        </ul>
                        <a href="#" class="h2 text-decoration-none text-dark">
                        <?=$row["product_name"]?>
                        </a>
                        <p class="card-text"><?=$row["description"]?></p>
                        <p class="text-muted">In Stock: <?=$row["stock"]?></p>    
                        <p class="text-muted">Reviews (26)</p>    
                    </div>
                    <div class="d-flex d-flex justify-content-center mb-3">
                    <form action="" method="POST" class="col-4">
                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                        <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                        <div class="form-group mb-3">
                            <label for="stock_<?= $row['product_id'] ?>" class="form-label">Select Quantity:</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="stock_<?= $row['product_id'] ?>" 
                                   name="total_stock" 
                                   min="1" 
                                   max="<?= $row['stock'] ?>" 
                                   value="1"
                                   required>
                        </div>
                        <button type="submit" 
                                name="add_to_cart" 
                                class="btn btn-warning w-100"
                                <?= ($row['stock'] <= 0) ? 'disabled' : '' ?>>
                            <?= ($row['stock'] <= 0) ? 'Out of Stock' : 'Add to Cart' ?>
                        </button>
                    </form>  
                    </div>
                </div>
            </div>
<?php
            }
        } else {
            echo "0 results";
        }

// Close connection
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
