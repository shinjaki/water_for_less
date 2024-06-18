<?php
include_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction_id = $_POST['transaction_id'];
    $employee_id = $_POST['employee_id'];
    $status = $_POST['status'];

    // Fetch the quantity, container_id, and current status from the payment table
    $fetch_sql = "SELECT p.quantity, p.container_id, t.status AS current_status FROM transaction t
                  JOIN payment p ON t.payment_id = p.payment_id
                  WHERE t.transaction_id = ?";
    $fetch_stmt = $pdo->prepare($fetch_sql);
    $fetch_stmt->execute([$transaction_id]);
    $transaction = $fetch_stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction) {
        $quantity = $transaction['quantity'];
        $container_id = $transaction['container_id'];
        $current_status = $transaction['current_status'];

        // Update the transaction status and employee ID
        $update_sql = "UPDATE transaction SET employee_id = :employee_id, status = :status WHERE transaction_id = :transaction_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute(['employee_id' => $employee_id, 'status' => $status, 'transaction_id' => $transaction_id]);

        // Handle stock adjustments based on the status transition
        if ($current_status == 'Pending' && ($status == 'Out For Delivery' || $status == 'Delivered')) {
            // Deduct stock
            $update_stock_sql = "UPDATE container SET stock = stock - :quantity WHERE container_id = :container_id";
            $update_stock_stmt = $pdo->prepare($update_stock_sql);
            $update_stock_stmt->execute(['quantity' => $quantity, 'container_id' => $container_id]);
        } elseif (($current_status == 'Out For Delivery' || $current_status == 'Delivered') && $status == 'Pending') {
            // Add stock back
            $update_stock_sql = "UPDATE container SET stock = stock + :quantity WHERE container_id = :container_id";
            $update_stock_stmt = $pdo->prepare($update_stock_sql);
            $update_stock_stmt->execute(['quantity' => $quantity, 'container_id' => $container_id]);
        } elseif (($current_status == 'Out For Delivery' || $current_status == 'Delivered') && $status == 'Cancelled') {
            // Add stock back
            $update_stock_sql = "UPDATE container SET stock = stock + :quantity WHERE container_id = :container_id";
            $update_stock_stmt = $pdo->prepare($update_stock_sql);
            $update_stock_stmt->execute(['quantity' => $quantity, 'container_id' => $container_id]);
        }

        echo "<script>alert('Transaction updated successfully');</script>";
    } else {
        echo "<script>alert('Transaction not found');</script>";
    }

    header("Location: transaction.php");
    exit();
}
?>
