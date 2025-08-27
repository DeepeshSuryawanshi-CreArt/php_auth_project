<?php
require "./config/authcheck.php";
require "./config/db.php";

$query = "SELECT name, email, mobile, address,profile_image FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User Not Found";
        exit();
    }
} else {
    die("Query Failed:" . mysqli_errno($conn));
}

$query = "SELECT * FROM users";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $users = mysqli_fetch_all($result);
        // echo "<pre>";
        // var_dump($users);
        // echo "</pre>";
    } else {
        echo "User Not Found";
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    die("Query Failed:" . mysqli_errno($conn));
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | User Profile</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="./bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="./bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="./bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="./dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="./dist/css/skins/_all-skins.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <!-- header -->
        <?php include "includes/header.php" ?>
        <!-- sidebar -->
        <?php include "includes/sidebar.php" ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    User Dashboard
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active">User Table</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header ">
                                <h3 class="box-title">User Data Table</h3>
                                <div class="">
                                    <a href="create_user.php?>" class="btn btn-md btn-success" >
                                        <i class="fa fa-plus-square"></i> Create User
                                    </a>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                                    <div class="row">
                                        <div class="col-sm-6"></div>
                                        <div class="col-sm-6"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <table id="example2" class="table table-bordered table-hover dataTable">
                                                <thead>
                                                    <tr role="row">
                                                        <th>Id</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Mobile no.</th>
                                                        <th>Gender</th>
                                                        <th>DOB</th>
                                                        <th>Address</th>
                                                        <th>Profile</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($users)): ?>
                                                        <?php foreach ($users as $user): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($user[0]) ?></td>
                                                                <td><?= htmlspecialchars($user[1]) ?></td>
                                                                <td><?= htmlspecialchars($user[2]) ?></td>
                                                                <td><?= htmlspecialchars($user[7]) ?></td>
                                                                <td><?= htmlspecialchars($user[8]) ?></td>
                                                                <td><?= htmlspecialchars($user[10]) ?></td>
                                                                <td><?= htmlspecialchars($user[9]) ?></td>
                                                                <td>
                                                                    <img src="<?= htmlspecialchars($user[4]) ?>" alt="Profile"
                                                                        width="50" height="50" style="border-radius:50%;">
                                                                </td>
                                                                <td>
                                                                    <a href="update_user.php?id=<?= $user[0] ?>"
                                                                        class="btn btn-sm btn-primary">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                    <a href="delete_user.php?id=<?= $user[0] ?>"
                                                                        class="btn btn-sm btn-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this user?');">
                                                                        <i class="fa fa-trash"></i> Delete
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="9" class="text-center">No users found</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-5">
                                            <div class="dataTables_info" id="example2_info" role="status"
                                                aria-live="polite">Showing 1 to 10 of 57 entries</div>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="dataTables_paginate paging_simple_numbers"
                                                id="example2_paginate">
                                                <ul class="pagination">
                                                    <li class="paginate_button previous disabled"
                                                        id="example2_previous"><a href="#" aria-controls="example2"
                                                            data-dt-idx="0" tabindex="0">Previous</a></li>
                                                    <li class="paginate_button active"><a href="#"
                                                            aria-controls="example2" data-dt-idx="1" tabindex="0">1</a>
                                                    </li>
                                                    <li class="paginate_button "><a href="#" aria-controls="example2"
                                                            data-dt-idx="2" tabindex="0">2</a></li>
                                                    <li class="paginate_button "><a href="#" aria-controls="example2"
                                                            data-dt-idx="3" tabindex="0">3</a></li>
                                                    <li class="paginate_button "><a href="#" aria-controls="example2"
                                                            data-dt-idx="4" tabindex="0">4</a></li>
                                                    <li class="paginate_button "><a href="#" aria-controls="example2"
                                                            data-dt-idx="5" tabindex="0">5</a></li>
                                                    <li class="paginate_button "><a href="#" aria-controls="example2"
                                                            data-dt-idx="6" tabindex="0">6</a></li>
                                                    <li class="paginate_button next" id="example2_next"><a href="#"
                                                            aria-controls="example2" data-dt-idx="7"
                                                            tabindex="0">Next</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <b>Version</b> 2.4.18
            </div>
            <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE</a>.</strong> All rights
            reserved.
        </footer>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="./bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="./bower_components/fastclick/lib/fastclick.js"></script>
    <!-- AdminLTE App -->
    <script src="./dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="./dist/js/demo.js"></script>
</body>

</html>