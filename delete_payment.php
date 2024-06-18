<?php
include_once 'config.php';

if (isset($_GET['id'])) {
    $payment_id = $_GET['id'];

    $sql = "DELETE FROM payment WHERE payment_id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$payment_id]);
        header("Location: payment.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No payment ID specified.";
}
?>
