<?php
include_once 'config.php';

if (isset($_GET['id'])) {
    $container_id = $_GET['id'];

    // First, update payments to remove the container reference
    $updatePaymentsSql = "UPDATE payment SET container_id = NULL WHERE container_id = ?";
    $updatePaymentsStmt = $pdo->prepare($updatePaymentsSql);
    
    try {
        $updatePaymentsStmt->execute([$container_id]);

        // Then delete the container
        $sql = "DELETE FROM container WHERE container_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$container_id]);
        
        header("Location: container.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No container ID specified.";
}
?>
