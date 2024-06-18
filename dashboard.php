<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include_once 'config.php';

$admin_id = $_SESSION['admin_id'];

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
$pending_count = $status_counts['pending_count'];
$out_for_delivery_count = $status_counts['out_for_delivery_count'];
$delivered_count = $status_counts['delivered_count'];
$cancelled_count = $status_counts['cancelled_count'];



// Fetch delivery counts per rider
$sql = "SELECT 
            CONCAT(e.fname, ' ', e.lname) AS employee_name, 
            COUNT(CASE WHEN t.status = 'Pending' THEN 1 END) AS pending_count,
            COUNT(CASE WHEN t.status = 'Out For Delivery' THEN 1 END) AS out_for_delivery_count,
            COUNT(CASE WHEN t.status = 'Delivered' THEN 1 END) AS delivered_count,
            COUNT(CASE WHEN t.status = 'Cancelled' THEN 1 END) AS cancelled_count
        FROM 
            transaction t
        JOIN 
            employee e ON t.employee_id = e.employee_id
        GROUP BY 
            employee_name";

try {
    $stmt = $pdo->query($sql);
    $deliveryCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Light Bootstrap Dashboard</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet"/>
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
    <link href="assets/css/demo.css" rel="stylesheet" />
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="wrapper">
    <?php include('sidebar.php'); ?>
    <div class="main-panel">
        <?php @include('navbar.php'); ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="card" style="background-color: transparent; color: white;">
                    <div class="card-header">
                        <h4 class="card-title">Transaction Status</h4>
                        <p class="card-category">Current Status of Transactions</p>
                    </div>
                    <div class="card-body">
                        <canvas id="transactionChart" style="max-height: 300px;"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fa fa-clock-o"></i> Data last updated
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card" style="background-color: transparent; color: white;">
                    <div class="card-header">
                        <h4 class="card-title">Deliveries by Rider</h4>
                        <p class="card-category">Number of Items Delivered per Rider</p>
                    </div>
                    <div class="card-body">
                        <canvas id="riderChart" style="max-height: 400px;"></canvas>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="fa fa-clock-o"></i> Data last updated
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Transaction Status Chart
        const transactionChartCtx = document.getElementById('transactionChart').getContext('2d');
        const transactionChart = new Chart(transactionChartCtx, {
            type: 'pie',
            data: {
                labels: ['Pending', 'Out For Delivery', 'Delivered', 'Cancelled'],
                datasets: [{
                    label: 'Transaction Status',
                    data: [<?php echo $pending_count; ?>, <?php echo $out_for_delivery_count; ?>, <?php echo $delivered_count; ?>, <?php echo $cancelled_count; ?>],
                    backgroundColor: ['#FFCE56', '#36A2EB', '#4BC0C0', '#FF6384']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });

        // Deliveries by Rider Chart
        const riderChartCtx = document.getElementById('riderChart').getContext('2d');
        const riderChart = new Chart(riderChartCtx, {
            type: 'bar',
            data: {
                labels: [<?php echo '"' . implode('","', array_column($deliveryCounts, 'employee_name')) . '"'; ?>],
                datasets: [
                    {
                        label: 'Pending',
                        data: [<?php echo implode(',', array_column($deliveryCounts, 'pending_count')); ?>],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Out For Delivery',
                        data: [<?php echo implode(',', array_column($deliveryCounts, 'out_for_delivery_count')); ?>],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Delivered',
                        data: [<?php echo implode(',', array_column($deliveryCounts, 'delivered_count')); ?>],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Cancelled',
                        data: [<?php echo implode(',', array_column($deliveryCounts, 'cancelled_count')); ?>],
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    });
</script>
</body>
</html>