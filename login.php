<?php
session_start();
include_once 'config.php'; // Include database configuration file

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT admin_id, password, user_type FROM admin WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password']) && $user['user_type'] === 'admin') {
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['user_type'] = $user['user_type'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = 'Invalid email or password, or user type is not admin.';
        }
    } catch (PDOException $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Login</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="./assets/css/demo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMs9pL1Asl5kW7I2MtV2mZf+JH8/hU8k1FF6tZ1" crossorigin="anonymous">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4 rounded-4" style="width: 28rem;">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="fas fa-sign-in-alt fa-4x text-info"></i>
                <h3 class="mt-3">Login to Your Account</h3>
            </div>
            <form method="post">
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
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-info w-100">Login</button>
            </form>
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="register.php" class="text-info">Register here</a></p>
            </div>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/b931534883.js" crossorigin="anonymous"></script>
</body>
</html>
