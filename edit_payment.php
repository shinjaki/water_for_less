<?php
include_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: payment.php");
    exit();
}

$payment_id = $_GET['id'];

// Fetch the payment details
$sql = "
    SELECT 
        p.payment_id, 
        p.customer_id, 
        p.container_id, 
        p.quantity, 
        p.total, 
        p.amount_received
    FROM 
        payment p
    WHERE 
        p.payment_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$payment_id]);
$payment = $stmt->fetch();

if (!$payment) {
    echo "Payment not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $container_id = $_POST['container_id'];
    $quantity = $_POST['quantity'];
    $total = $_POST['total'];
    $amount_received = $_POST['amount_received'];

    $updateSql = "
        UPDATE payment 
        SET customer_id = ?, container_id = ?, quantity = ?, total = ?, amount_received = ? 
        WHERE payment_id = ?
    ";
    $updateStmt = $pdo->prepare($updateSql);

    try {
        $updateStmt->execute([$customer_id, $container_id, $quantity, $total, $amount_received, $payment_id]);
        header("Location: payment.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all customers and containers for dropdowns
$customersStmt = $pdo->prepare("SELECT customer_id, CONCAT(fname, ' ', lname) AS name FROM customer");
$customersStmt->execute();
$customers = $customersStmt->fetchAll();

$containersStmt = $pdo->prepare("SELECT container_id, container_name FROM container");
$containersStmt->execute();
$containers = $containersStmt->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Edit Payment</title>
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
</head>
<body>
<div class="wrapper">
    <?php @include('sidebar.php'); ?>
    <div class="main-panel">
        <?php include('navbar.php'); ?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Payment</h4>
                            </div>
                            <div class="content">
                                <form method="post" action="">
                                    <div class="form-group">
                                        <label for="customer">Customer</label>
                                        <select class="form-control" id="customer" name="customer_id" required>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?php echo $customer['customer_id']; ?>" <?php echo ($payment['customer_id'] == $customer['customer_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($customer['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="container">Container</label>
                                        <select class="form-control" id="container" name="container_id" required>
                                            <?php foreach ($containers as $container): ?>
                                                <option value="<?php echo $container['container_id']; ?>" <?php echo ($payment['container_id'] == $container['container_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($container['container_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($payment['quantity']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="total">Total</label>
                                        <input type="number" class="form-control" id="total" name="total" value="<?php echo htmlspecialchars($payment['total']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="amount_received">Amount Received</label>
                                        <input type="number" class="form-control" id="amount_received" name="amount_received" value="<?php echo htmlspecialchars($payment['amount_received']); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Payment</button>
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
</body>
</html>
