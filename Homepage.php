<?php 
  session_start();

  
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
  <link rel="stylesheet" href="fontawesome-free-6.7.0-web/css/fontawesome.css" />
  <script defer src="js/bootstrap.bundle.min.js"></script>

</head>


<body>

  <div>
    <nav class="navbar navbar-expand-lg bg-dark">
      <div class="container">
        <div class="w-100 d-flex justify-content-between">
          <div>
            <i class="fa-solid fa-envelope text-light contact-info"></i>
            <a href="" class="navbar-sm-brand text-light text-decoration-none contact-info">
              PastryCorner.com
            </a>
            <i class="fa-solid fa-phone contact-info text-light"></i>
            <a href="" class="navbar-sm-brand text-white text-decoration-none contact-info">
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
                <a class="nav-link nav-links" ria-current="page" href="#">
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

  <div id="carouselExampleIndicators" class="carousel slide">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
        aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
        aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
        aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="container d-flex justify-content-between background">
          <div class="d-flex justify-content-center align-items-start flex-column ms-5">
            <span class="text-success title mb-3"> PastryCorner </span>
            <span class="discription">
              Delicious and Mouth-Watering Pastries
            </span>
            <span class="d-info">
              Welcome to Pastry Corner, where every bite is a moment of pure
              delight! We are a dedicated pastry shop passionate about
              creating freshly baked goods that bring joy to your day.</span>
          </div>
          <div>
            <img src="./images/cookies-white.jpg" class="d-block s-image" />
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="container d-flex justify-content-between background2">
          <div class="d-flex justify-content-center align-items-start flex-column ms-5">
            <span class="text-success title mb-3">
              PastryCorner
            </span>
            <span class="discription">
              Delicious and Mouth-Watering Pastries
            </span>
            <span class="d-info">
              Welcome to Pastry Corner, where every bite is a moment of pure
              delight! We are a dedicated pastry shop passionate about
              creating freshly baked goods that bring joy to your day.</span>
          </div>
          <div>
            <img src="./images/croissants delish.jpg" class="d-block s-image" />
          </div>
        </div>
      </div>
      <div class="carousel-item">
        <div class="container d-flex justify-content-between background3">
          <div class="d-flex justify-content-center align-items-start flex-column ms-5">
            <span class="text-success title mb-3">PastryCorner</span>
            <span class="discription">Delicious and Mouth-Watering Pastries
            </span>
            <span class="d-info"></span>
            Welcome to Pastry Corner, where every bite is a moment of pure
            delight! We are a dedicated pastry shop passionate about
            creating freshly baked goods that bring joy to your day.</span>
          </div>
          <div>
            <img src="./images/coloured-macarons-view.jpg" class="d-block s-image" />
          </div>
        </div>
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
      data-bs-slide="prev">
      <span class="carousel-control-prev-icon bg-success" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
      data-bs-slide="next">
      <span class="carousel-control-next-icon bg-success" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  <section class="container">
    <div class="row text-center pt-3">
      <div class="col-lg-6 m-auto">
        <h1 class="h1">Our Bestsellers</h1>
        <p class="h6">At Pastry Corner, our best-selling treats are a true reflection of our passion for creating
          mouth-watering pastries. Each item is crafted with care, using only the finest ingredients to ensure every
          bite is a delightful experience. From our signature creations to seasonal favorites, our best sellers continue
          to win hearts and satisfy cravings.</p>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-4 p-5 mt-3">
        <a href="./shop.php"><img src="./images/cookies-white.jpg" class="rounded-circle border c-image" alt=""></a>
        <h5 class="text-center mt-3 mb-5">Cookies</h5>
        <p class="text-center"><a href="./shop.php" class="btn btn-warning">
            Go Shop</a>
        </p>
      </div>
      <div class="col-12 col-md-4 p-5 mt-3">
        <a href="./shop.php"><img src="./images/croissants delish.jpg" class="rounded-circle border c-image" alt=""></a>
        <h5 class="text-center mt-3 mb-5">Croissant</h5>
        <p class="text-center"><a href="./shop.php" class="btn btn-warning">
            Go Shop</a>
        </p>
      </div>
      <div class="col-12 col-md-4 p-5 mt-3">
        <a href="./shop.php"><img src="./images/coloured-macarons-view.jpg" class="rounded-circle border c-image" alt="">
        </a>
        <h5 class="text-center mt-3 mb-5">Macarons</h5>
        <p class="text-center"><a href="./shop.php" class="btn btn-warning">
            Go Shop</a>
        </p>
      </div>
    </div>
  </section>


  <section class="bg-light">
    <div class="container py-5">
      <div class="row text-center py-3">
        <div class="col-lg-6 m-auto">
          <h1>Featured Products</h1>
          <p class="fw-bold">Discover our featured pastries, each crafted with premium ingredients and unparalleled
            attention to detail. From indulgent treats to light, flaky delights, these favorites showcase the perfect
            blend of flavor, texture, and artistry. Whether you're craving something sweet or simply irresistible, our
            featured products are sure to satisfy.</p>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-4 mb-4">
          <div class="card h-100">
            <a href="">
              <img src="./images/cookies-white.jpg" class="card-img-top">
            </a>
            <div class="card-body">
              <ul class="list-unstyled d-flex justify-content-between">
                <li>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </li>
                <li class="text-muted text-right">₱100.00</li>
              </ul>
              <a href="" class="h2 text-decoration-none text-dark">
                Cookies
              </a>
              <p class="card-text">Our cookies are baked to perfection, crispy on the outside and soft on the inside.
                Made with premium ingredients like rich chocolate chips and fresh butter, these sweet treats are a
                favorite for any occasion!</p>
              <p class="text-muted">Reviews (26)</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4 mb-4">
          <div class="card h-100">
            <a href="">
              <img src="./images/croissants delish.jpg" class="card-img-top">
            </a>
            <div class="card-body">
              <ul class="list-unstyled d-flex justify-content-between">
                <li>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="fa-solid fa-star"></i>
                </li>
                <li class="text-muted text-right">₱130.00</li>
              </ul>
              <a href="" class="h2 text-decoration-none text-dark">
                Croissant
              </a>
              <p class="card-text">Flaky, buttery, and melt-in-your-mouth delicious, our croissants are crafted with a
                delicate, golden-brown crust and a light, airy interior. Perfect for breakfast or a snack, each bite is
                a reminder of French pastry perfection.</p>
              <p class="text-muted">Reviews (26)</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4 mb-4">
          <div class="card h-100">
            <a href="">
              <img src="./images/coloured-macarons-view.jpg" class="card-img-top">
            </a>
            <div class="card-body">
              <ul class="list-unstyled d-flex justify-content-between">
                <li>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                  <i class="text-warning fa-solid fa-star"></i>
                </li>
                <li class="text-muted text-right">₱115.00</li>
              </ul>
              <a href="" class="h2 text-decoration-none text-dark">
                Macarons
              </a>
              <p class="card-text">Delicately crisp on the outside and chewy on the inside, our macarons come in a
                variety of vibrant flavors, each filled with silky buttercream or ganache. These little French delights
                are the perfect balance of sweet and sophisticated.</p>
              <p class="text-muted">Reviews (26)</p>
            </div>
          </div>
        </div>
      </div>
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
            <li><a href="" class="text-decoration-none text-light">
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
                  <a href="http://facebook.com/" target="_blank" class="text-light">
                    <i class="f-links fa-brands fa-facebook-f"></i>
                  </a>
                </li>
                <li class="border border-light rounded-circle text-center
                      list-unstyled ms-2 f-links">
                  <a href="http://instagram.com/" target="_blank" class="text-light">
                    <i class="f-links fa-brands fa-instagram"></i>
                  </a>
                </li>
                <li class="border border-light rounded-circle text-center
                       list-unstyled ms-2 f-links">
                  <a href="http://twitter.com/" target="_blank" class="text-light">
                    <i class="f-links fa-brands fa-twitter"></i>
                  </a>
                </li>
                <li class="border border-light rounded-circle text-center
                       list-unstyled ms-2 f-links">
                  <a href="http://linkedin.com/" target="_blank" class="text-light">
                    <i class="f-links fa-brands fa-linkedin"></i>
                  </a>
                </li>
              </div>
              <div class="col-auto d-flex text-center">
                <input type="text" class="bg-dark border-light rounded" placeholder="Email Address">
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