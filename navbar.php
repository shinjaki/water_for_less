<?php
$first_name = '';

try {
    $sql = "SELECT fname FROM admin WHERE admin_id = :admin_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($admin) {
        $first_name = $admin['fname'];
    }
} catch (PDOException $e) {

    die("ERROR: Could not execute query. " . $e->getMessage());
}
?>

<nav class="navbar navbar-default navbar-fixed" style="background-color: transparent; color: white;">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#" style="color: white;">Hello, <?php echo htmlspecialchars($first_name); ?></a>
                </div>
                <div class="collapse navbar-collapse">

                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: white;">
                                    <p>
										Account
										<b class="caret"></b>
									</p>

                              </a>
                              <ul class="dropdown-menu" style="background-color: transparent; color: white;">
                                <li><a href="admin.php" style="color: white;">Profile</a></li>
                                <li><a href="logout.php" style="color: white;">Logout</a></li>
                              </ul>
                        </li>
						<li class="separator hidden-lg"></li>
                    </ul>
                </div>
            </div>
        </nav>