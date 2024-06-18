<?php
include_once 'config.php';

if (isset($_GET['id'])) {
    $transaction_id = $_GET['id'];

    $sql = "DELETE FROM transaction WHERE transaction_id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$transaction_id]);
        header("Location: transaction.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No transaction ID specified.";
}
?>
