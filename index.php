<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the "Sign Up" button was clicked
    if (isset($_POST['signUp'])) {
        header('Location: ../signup.php');
        exit;
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centered Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100 bg-secondary">
        

        <form action="login-process/process.php" method="POST" class="border border-primary rounded p-4 bg-white">
        <p class="fw-light fs-2 text-center">Pastry Corner</p>
        <p class="fw-light fs-4 text-center">LOGIN</p>
        <div class="row mb-3 align-items-center">
            <div class="col-4 input-group has-validation mb-3">
                <span class="input-group-text">Email &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <div class="form-floating">
                    <input type="text" name="email" class="form-control" id="email" placeholder="Username" required>
                    <label for="email" >Email</label>
                </div>
            </div>
            
            <div class="col-4 input-group has-validation mb-3">
                <span class="input-group-text">Password</span>
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
            </div>
            
            <?php
             if(isset($_GET['success']) && $_GET['success'] == 'registered'){
            echo '<div class="alert alert-success" role="alert">
                      Account Successfully Registered!
                  </div>';
             }
            ?>
             <?php
             if(isset($_GET['error']) && $_GET['error'] == 'account_invalid'){
            echo '<div class="alert alert-danger" role="alert">
                      Account not registered or wrong credentials!
                  </div>';
             }
            ?>

            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
        
            <div class="text-center my-4 position-relative">
                <hr class="w-100" />
                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 fw-bold">OR</span>
            </div>
            <form action="login-process/process.php" method="POST">
                <button type="submit" name="signUp" class="btn btn-primary w-100">Sign Up</button>
            </form>
        </div> 
    </div>
</body>
</html>
