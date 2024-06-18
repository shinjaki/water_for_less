<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include_once 'config.php';

$admin_id = $_SESSION['admin_id'];

if (isset($_GET['id'])) {
    $transaction_id = $_GET['id'];

    // Fetch transaction details
    $sql = "SELECT 
            t.transaction_id, 
            t.transaction_date, 
            c.customer_id, 
            CONCAT(c.fname, ' ', c.lname) AS customer_name, 
            con.container_id, 
            con.container_name, 
            t.status, 
            p.quantity, 
            p.amount_received, 
            t.employee_id
        FROM 
            transaction t
        JOIN 
            payment p ON t.payment_id = p.payment_id
        JOIN 
            customer c ON p.customer_id = c.customer_id
        JOIN 
            container con ON p.container_id = con.container_id
        WHERE 
            t.transaction_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$transaction_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$transaction) {
        echo "<script>alert('Transaction not found');</script>";
        header("Location: transaction.php");
        exit();
    }

    // Fetch container options
    $container_sql = "SELECT container_id, container_name, unit_price FROM container";
    $container_stmt = $pdo->query($container_sql);
    $containers = $container_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all customers for dropdown
    $customer_sql = "SELECT customer_id, CONCAT(fname, ' ', lname) AS full_name FROM customer";
    $customer_stmt = $pdo->query($customer_sql);
    $customers = $customer_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch employee list for delivery rider dropdown
    $employee_sql = "SELECT employee_id, CONCAT(fname, ' ', lname) AS full_name FROM employee";
    $employee_stmt = $pdo->query($employee_sql);
    $employees = $employee_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    header("Location: transaction.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Edit Transaction</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Animation library for notifications -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>
    <!-- Light Bootstrap Table core CSS -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
    <!-- CSS for Demo Purpose, don't include it in your project -->
    <link href="assets/css/demo.css" rel="stylesheet" />
    <!-- Fonts and icons -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
</head>
<body>

<div class="wrapper">
    <?php @include('sidebar.php'); ?>
    <div class="main-panel">
        <?php include('navbar.php'); ?>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Transaction</h4>
                            </div>
                            <div class="content">
                                <form method="post" action="update_transaction.php">
                                    <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($transaction['transaction_id']); ?>">
                                    <div class="form-group">
                                        <label for="customer_name">Customer Name</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?= htmlspecialchars($transaction['customer_name']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="container_name">Container</label>
                                        <input type="text" class="form-control" id="container_name" name="container_name" value="<?= htmlspecialchars($transaction['container_name']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($transaction['quantity']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="amount_received">Amount Received</label>
                                        <input type="number" step="0.01" class="form-control" id="amount_received" name="amount_received" value="<?= htmlspecialchars($transaction['amount_received']); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="employee_id">Delivery Rider</label>
                                        <select class="form-control" id="employee_id" name="employee_id">
                                            <?php foreach ($employees as $employee): ?>
                                                <option value="<?= $employee['employee_id']; ?>" <?= $transaction['employee_id'] == $employee['employee_id'] ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($employee['full_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status" required>

                                            <option value="Pending" <?= $transaction['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>

                                            <option value="Out For Delivery" <?= $transaction['status'] == 'Out For Delivery' ? 'selected' : ''; ?>>Out For Delivery</option>

                                            <option value="Delivered" <?= $transaction['status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>

                                            <option value="Cancelled" <?= $transaction['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Transaction</button>
                                    <a href="transaction.php" class="btn btn-default">Back to List</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core JS Files -->
<script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<!-- Charts Plugin -->
<script src="assets/js/chartist.min.js"></script>
<!-- Notifications Plugin -->
<script src="assets/js/bootstrap-notify.js"></script>
<!-- Google Maps Plugin -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
<script src="assets/js/demo.js"></script>
</body>
</html>

