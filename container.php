<?php
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include_once 'config.php';

$admin_id = $_SESSION['admin_id'];

// Fetch container data
$sql = "SELECT container_id, container_name, unit_price, stock FROM container";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$containers = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>Container List</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!-- Light Bootstrap Table core CSS -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>

    <!-- CSS for Demo Purpose, don't include it in your project -->
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
                                <h4 class="title" style="color: white;">Container Sizes</h4>
                                
                                <div style="margin: 2rem 2px 2rem 2px">
                                    <a href="add_container.php" class="btn btn-info" style="color: white;">Add Container</a>
                                </div>

                                <div>
                                    <!-- Search Form -->
                                    <input type="text" class="form-control ms-3" id="searchInput" placeholder="Search by name or ID" aria-label="Search" style="background-color: transparent; color: white;">
                                </div>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="text-center" >
                                            <th style="color: yellow;">Container Name</th>
                                            <th>Unit Price</th>
                                            <th>Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="containerTableBody">
                                        <?php foreach ($containers as $container): ?>
                                            <tr style="background-color: transparent; color: white;">
                                                <td><?php echo htmlspecialchars($container['container_name']); ?></td>
                                                <td><?php echo htmlspecialchars($container['unit_price']); ?></td>
                                                <td><?php echo htmlspecialchars($container['stock']); ?></td>
                                                <td>
                                                    <a href="edit_container.php?id=<?php echo $container['container_id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                                    <a href="delete_container.php?id=<?php echo $container['container_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this container?');"><i class="fa fa-trash"></i> Delete</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
<script src="assets/js/demo.js"></script>

<script>
    // JavaScript to handle the search input
    document.getElementById('searchInput').addEventListener('input', function() {
        var searchTerm = this.value.toLowerCase();
        var tableRows = document.querySelectorAll('#containerTableBody tr');
        
        tableRows.forEach(function(row) {
            var nameCell = row.cells[0].innerText.toLowerCase();
            var unitPriceCell = row.cells[1].innerText.toLowerCase();
            
            if (nameCell.includes(searchTerm) || unitPriceCell.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>
