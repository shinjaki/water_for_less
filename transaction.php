<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include_once 'config.php';

$admin_id = $_SESSION['admin_id'];

// Fetch transaction data
$sql = "SELECT 
            t.transaction_id, 
            t.transaction_date, 
            CONCAT(c.fname, ' ', c.lname) AS customer_name, 
            con.container_name, 
            cust.address, 
            t.status,
            CONCAT(e.fname, ' ', e.lname) AS delivery_rider_name
        FROM 
            transaction t
        JOIN 
            payment p ON t.payment_id = p.payment_id
        JOIN 
            customer c ON p.customer_id = c.customer_id
        JOIN 
            container con ON p.container_id = con.container_id
        JOIN 
            customer cust ON c.customer_id = cust.customer_id
        LEFT JOIN 
            employee e ON t.employee_id = e.employee_id";

$stmt = $pdo->query($sql);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Transaction Details</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Animation library for notifications -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>
    <!-- Light Bootstrap Table core CSS -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
    <!-- CSS for Demo Purpose -->
    <link href="assets/css/demo.css" rel="stylesheet" />
    <!-- Fonts and icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

    <style>
        .btn-add {
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
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
                        <div class="card" style="background-color: transparent; color: white;">
                            <div class="header d-flex justify-content-between align-items-center">
                                <h4 class="title" style="color: white;">Transaction / Delivery</h4>
                                <div style="margin: 2rem 2px 2rem 2px">
                                </div>
                                <div>
                                    <!-- Search Form -->
                                    <input type="text" class="form-control ms-3" id="searchInput" placeholder="Search by customer or container" aria-label="Search" style="background-color: transparent; color: white;">
                                </div>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Transaction Date</th>
                                            <th>Delivery Rider</th>
                                            <th>Customer</th>
                                            <th>Container</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactionTableBody">
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr style="background-color: transparent; color: white;">
                                                <td><?= htmlspecialchars($transaction['transaction_date']); ?></td>
                                                <td><?= htmlspecialchars($transaction['delivery_rider_name']); ?></td>
                                                <td><?= htmlspecialchars($transaction['customer_name']); ?></td>
                                                <td><?= htmlspecialchars($transaction['container_name']); ?></td>
                                                <td><?= htmlspecialchars($transaction['address']); ?></td>
                                                <td style="color: <?= $transaction['status'] == 'Pending' ? '#FFA500' : ($transaction['status'] == 'Out For Delivery' ? '#008000' : ($transaction['status'] == 'Delivered' ? '#008000' : '#FF0000')); ?>;">
                                                    <?= htmlspecialchars($transaction['status']); ?>
                                                </td>
                                                <td>
                                                    <a href="edit_transaction.php?id=<?= htmlspecialchars($transaction['transaction_id']); ?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Update</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
<!-- Charts Plugin -->
<script src="assets/js/chartist.min.js"></script>
<!-- Notifications Plugin -->
<script src="assets/js/bootstrap-notify.js"></script>
<!-- Google Maps Plugin -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
<script src="assets/js/demo.js"></script>

<script>
    // JavaScript to handle the search input
    document.getElementById('searchInput').addEventListener('input', function() {
        var searchTerm = this.value.toLowerCase();
        var tableRows = document.querySelectorAll('#transactionTableBody tr');
        
        tableRows.forEach(function(row) {
            var customerNameCell = row.cells[2].innerText.toLowerCase();
            var containerNameCell = row.cells[3].innerText.toLowerCase();
            
            if (customerNameCell.includes(searchTerm) || containerNameCell.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
