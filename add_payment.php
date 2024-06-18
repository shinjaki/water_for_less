<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include database configuration
include_once 'config.php';

// Get admin ID from session
$admin_id = $_SESSION['admin_id'];

// Fetch containers for dropdown
$container_sql = "SELECT container_id, container_name, unit_price FROM container";
$container_stmt = $pdo->query($container_sql);
$containers = $container_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customers for dropdown
$customer_sql = "SELECT customer_id, CONCAT(fname, ' ', lname) AS full_name FROM customer";
$customer_stmt = $pdo->query($customer_sql);
$customers = $customer_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $customer_name = $_POST['customer_name'];
    $container_id = $_POST['container_id'];
    $quantity = $_POST['quantity'];
    $amount_received = $_POST['amount_received'];

    // Fetch customer ID based on selected customer name
    $customer_sql = "SELECT customer_id FROM customer WHERE CONCAT(fname, ' ', lname) = ?";
    $customer_stmt = $pdo->prepare($customer_sql);
    $customer_stmt->execute([$customer_name]);
    $customer = $customer_stmt->fetch(PDO::FETCH_ASSOC);

    // Check if customer exists
    if ($customer) {
        $customer_id = $customer['customer_id'];

        // Fetch unit price of selected container
        $container_sql = "SELECT unit_price FROM container WHERE container_id = ?";
        $container_stmt = $pdo->prepare($container_sql);
        $container_stmt->execute([$container_id]);
        $container = $container_stmt->fetch(PDO::FETCH_ASSOC);

        // Check if container exists
        if ($container) {
            $unit_price = $container['unit_price'];
            $total = $unit_price * $quantity;

            // Prepare SQL statement to insert payment
            $sql_payment = "INSERT INTO payment (customer_id, container_id, quantity, total, amount_received) VALUES (?, ?, ?, ?, ?)";
            $stmt_payment = $pdo->prepare($sql_payment);
            $stmt_payment->bindParam(1, $customer_id);
            $stmt_payment->bindParam(2, $container_id);
            $stmt_payment->bindParam(3, $quantity);
            $stmt_payment->bindParam(4, $total);
            $stmt_payment->bindParam(5, $amount_received);

            // Execute payment insertion
            if ($stmt_payment->execute()) {
                $payment_id = $pdo->lastInsertId();

                // Prepare SQL statement to insert transaction
                $sql_transaction = "INSERT INTO transaction (payment_id, transaction_date, status) VALUES (?, NOW(), 'Pending')";
                $stmt_transaction = $pdo->prepare($sql_transaction);
                $stmt_transaction->bindParam(1, $payment_id);

                // Execute transaction insertion
                if ($stmt_transaction->execute()) {
                    echo "<script>alert('Payment and Transaction added successfully');</script>";
                    header("Location: payment.php");
                    exit();
                } else {
                    echo "<script>alert('Error adding transaction');</script>";
                }
            } else {
                echo "<script>alert('Error adding payment');</script>";
            }
        } else {
            echo "<script>alert('Invalid container selected');</script>";
        }
    } else {
        echo "<script>alert('Customer not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Payment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Add Payment</h4>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <select class="form-control" id="customer_name" name="customer_name" required>
                                <?php foreach ($customers as $customer): ?>
                                    <option value="<?= htmlspecialchars($customer['full_name']); ?>">
                                        <?= htmlspecialchars($customer['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="container_id">Container</label>
                            <select class="form-control" id="container_id" name="container_id" required>
                                <?php foreach ($containers as $container): ?>
                                    <option value="<?= $container['container_id']; ?>"><?= $container['container_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" required>
                        </div>
                        <div class="form-group">
                            <label for="total">Total</label>
                            <input type="number" class="form-control" id="total" name="total" readonly>
                        </div>
                        <div class="form-group">
                            <label for="amount_received">Amount Received</label>
                            <input type="number" step="0.01" class="form-control" id="amount_received" name="amount_received" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Payment</button>
                        <a href="payment.php" class="btn btn-default">Back to List</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Calculate total based on selected container and quantity
    document.getElementById('container_id').addEventListener('change', updateTotal);
    document.getElementById('quantity').addEventListener('input', updateTotal);

    function updateTotal() {
        var container_id = document.getElementById('container_id').value;
        var quantity = document.getElementById('quantity').value;

        if (container_id && quantity) {
            var unit_price = <?= json_encode(array_column($containers, 'unit_price', 'container_id')); ?>;
            var total = unit_price[container_id] * quantity;
            document.getElementById('total').value = total.toFixed(2);
        }
    }
</script>
</body>
</html>
