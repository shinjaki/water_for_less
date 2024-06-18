<?php
include_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: container.php");
    exit();
}

$container_id = $_GET['id'];

// Fetch the container details
$sql = "SELECT container_id, container_name, unit_price, stock FROM container WHERE container_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$container_id]);
$container = $stmt->fetch();

if (!$container) {
    echo "Container not found.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $container_name = $_POST['container_name'];
    $unit_price = $_POST['unit_price'];
    $stock = $_POST['stock'];

    $updateSql = "UPDATE container SET container_name = ?, unit_price = ?, stock = ? WHERE container_id = ?";
    $updateStmt = $pdo->prepare($updateSql);

    try {
        $updateStmt->execute([$container_name, $unit_price, $stock, $container_id]);
        header("Location: container.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Edit Container</title>
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
</head>
<body>

<div class="wrapper">
    <?php @include('sidebar.php'); ?>
    <div class="main-panel">
        <?php include('navbar.php'); ?>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Container</h4>
                            </div>
                            <div class="content">
                                <form method="post" action="">
                                    <div class="form-group">
                                        <label for="container_name">Container Name</label>
                                        <input type="text" class="form-control" id="container_name" name="container_name" value="<?php echo htmlspecialchars($container['container_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="unit_price">Unit Price</label>
                                        <input type="number" class="form-control" id="unit_price" name="unit_price" value="<?php echo htmlspecialchars($container['unit_price']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Stock</label>
                                        <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($container['stock']); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-fill pull-right">Update Container</button>
                                    <div class="clearfix"></div>
                                </form>
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
</body>
</html>
