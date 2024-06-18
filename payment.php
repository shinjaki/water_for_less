<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include_once 'config.php';

$admin_id = $_SESSION['admin_id'];

// Fetch payment data
$sql = "
    SELECT 
        p.payment_id, 
        CONCAT(c.fname, ' ', c.lname) AS customer_name, 
        con.container_name, 
        cust.address, 
        p.quantity, 
        p.total, 
        p.amount_received, 
        (p.amount_received - p.total) AS changed
    FROM 
        payment p
    JOIN 
        customer c ON p.customer_id = c.customer_id
    JOIN 
        container con ON p.container_id = con.container_id
    JOIN 
        customer cust ON p.customer_id = cust.customer_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$payments = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Payment List</title>
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
    <link href="assets/css/pe-icon-7-stroke.css" rel='stylesheet' />

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
                                <h4 class="title" style="color: white;">Payment History</h4>
                                
                                <div style="margin: 2rem 2px 2rem 2px">
                                    <a href="add_payment.php" class="btn btn-info" style="color: white;">Add Payment</a>
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
                                            <th>Customer</th>
                                            <th>Container</th>
                                            <th>Address</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Amount Received</th>
                                            <th>Change</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentTableBody">
                                        <?php foreach ($payments as $payment): ?>
                                            <tr style="background-color: transparent; color: white;">
                                                <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['container_name']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['address']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['quantity']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['total']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['amount_received']); ?></td>
                                                <td><?php echo htmlspecialchars($payment['changed']); ?></td>
                                                <td>
                                                    <a href="edit_payment.php?id=<?php echo $payment['payment_id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                                    <a href="delete_payment.php?id=<?php echo $payment['payment_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment?');"><i class="fa fa-trash"></i> Delete</a>
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
        var tableRows = document.querySelectorAll('#paymentTableBody tr');
        
        tableRows.forEach(function(row) {
            var customerNameCell = row.cells[0].innerText.toLowerCase();
            var containerNameCell = row.cells[1].innerText.toLowerCase();
            
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
