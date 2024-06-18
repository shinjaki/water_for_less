<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database configuration file
    include_once 'config.php';

    // Get form data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate form data
    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm_password)) {
        die("All fields are required.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }
    
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Prepare an insert statement
        $sql = "INSERT INTO admin (fname, lname, email, password) VALUES (:fname, :lname, :email, :password)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters to statement
        $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
        $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            header('Location: login.php');
        } else {
            echo "Error: Could not execute the query.";
        }
    } catch(PDOException $e) {
        die("ERROR: Could not prepare/execute query. " . $e->getMessage());
    }

    // Close the connection
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/x-icon" href="/assets/logo-vt.svg" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Registration Page</title>
    <link rel="stylesheet" href="./assets/css/demo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMs9pL1Asl5kW7I2MtV2mZf+JH8/hU8k1FF6tZ1" crossorigin="anonymous">
</head>
<body class="bg-primary d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4 rounded-4" style="width: 28rem;">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="fas fa-user-circle fa-4x text-info"></i>
                <h3 class="mt-3">Create an Account</h3>
            </div>
            <form action="register.php" method="post">
                <div class="row mb-3">
                    <div class="col">
                        <label for="fname" class="form-label">First Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required />
                        </div>
                    </div>
                    <div class="col">
                        <label for="lname" class="form-label">Last Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" required />
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required />
                    </div>
                </div>
                <button type="submit" class="btn btn-info w-100">Register</button>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php" class="text-info">Login here</a></p>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/b931534883.js" crossorigin="anonymous"></script>
</body>
</html>
