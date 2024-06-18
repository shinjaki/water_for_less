<?php
include_once 'config.php';

if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    $sql = "DELETE FROM customer WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$customer_id]);
        header("Location: customer.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No customer ID specified.";
}
?>
