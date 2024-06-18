<?php
include_once 'config.php';

$sql = "SELECT 
            COUNT(CASE WHEN t.status = 'Pending' THEN 1 END) AS pending_count,
            COUNT(CASE WHEN t.status = 'Out For Delivery' THEN 1 END) AS out_for_delivery_count,
            COUNT(CASE WHEN t.status = 'Delivered' THEN 1 END) AS delivered_count,
            COUNT(CASE WHEN t.status = 'Cancelled' THEN 1 END) AS cancelled_count
        FROM 
            transaction t
        JOIN 
            payment p ON t.payment_id = p.payment_id";

$stmt = $pdo->query($sql);
$status_counts = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($status_counts);
?>
