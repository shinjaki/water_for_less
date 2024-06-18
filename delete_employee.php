<?php
include_once 'config.php';

if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];

    $sql = "DELETE FROM employee WHERE employee_id = ?";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$employee_id]);
        header("Location: employee.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No employee ID specified.";
}
?>
