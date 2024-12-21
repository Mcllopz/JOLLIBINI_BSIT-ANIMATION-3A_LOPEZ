<?php
        session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the "Sign Up" button was clicked
    if (isset($_POST['signUp'])) {
        header('Location: ../signup.php');
        exit;
    }
    if (isset($_POST['GoBackLogin'])) {
        header('Location: ../index.php');
        exit;
    }

    if(isset($_POST['logoutBtn'])){



        if ($_SESSION['user_id']) {
            
                    session_destroy();
                    session_unset();
                    header("Location: ../index.php");
                    exit(); 
                }   else{
                    echo "failed to log in: <a href='../index.php'>Go back to home page</a>";
                }
    }


    // Check if the "Login" button was clicked
    if (isset($_POST['login'])) {
        // Capture form data
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Database connection details
        $servername = "localhost";  // Replace with your database server name
        $username = "root";         // Replace with your database username
        $dbpassword = "";           // Replace with your database password
        $dbname = "pastrycorner";   // Database name
    
        // Create a connection to the database
        $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    
        // Check for connection errors
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if($email === 'gerund@admin.com'){
            $query = "SELECT * FROM admin WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc(); // Fetch user data
                
                // Check if the password is correct
                if ($password === $user['password']) {
                    // Password is correct, log the user in and redirect
                    session_start();
                    $_SESSION['user_id'] = $user['admin_id']; // Store user id in session
                    $_SESSION['email'] = $user['email']; // Store email in session
                    header("Location: ../admin/");
                    exit();
                } else {
                    // Invalid password
                    header("Location: ../index.php?error=invalid_password");
                    exit();
                }
            }
        }
        // Prepare the SQL query to check if the email exists
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Check if the email exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc(); // Fetch user data
            
            // Check if the password is correct
            if ($password === $user['password']) {
                // Password is correct, log the user in and redirect
                session_start();
                $_SESSION['user_id'] = $user['user_id']; // Store user id in session
                $_SESSION['email'] = $user['email']; // Store email in session
                header("Location: ../PASTRY CR/");
                exit();
            } else {
                // Invalid password
                header("Location: ../index.php?error=invalid_password");
                exit();
            }
        } else {
            // Email not found
            header("Location: ../index.php?error=account_invalid");
            exit();
        }
    
        // Close the connection
        $stmt->close();
        $conn->close();
    }

    //register ini
    if (isset($_POST['submitReg'])) {
        // Capture form data
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $contact_number = $_POST['contact_number'];
        $address = $_POST['address'];
    
        // Get the current date and time
        $register_date = date("Y-m-d H:i:s");
    
        // Database connection details
        $servername = "localhost"; // Replace with your database server name
        $username = "root";        // Replace with your database username
        $dbpassword = "";          // Replace with your database password
        $dbname = "pastrycorner";  // Database name
    
        // Create connection
        $conn = new mysqli($servername, $username, $dbpassword, $dbname);
    
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        // Check for duplicate email
        $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0 || $email === 'gerund@admin.com') {
            // Duplicate email found, redirect to index.php
            header("Location: ../signup.php?error=duplicate_email");
            exit();
        }
    
        // Insert data into database with current date and time
        $insertQuery = "INSERT INTO users (fullname, email, password, contact_number, address, register_date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssss", $fullname, $email, $password, $contact_number, $address, $register_date);
    
        if ($stmt->execute()) {
            // Successful insertion
            header("Location: ../index.php?success=registered");
        } else {
            // Error during insertion
            echo "Error: " . $stmt->error;
        }
    
        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
}

?>