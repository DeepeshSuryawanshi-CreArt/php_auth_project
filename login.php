<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | Log in</title>
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
  <!-- iCheck -->
  <link rel="stylesheet" href="./plugins/iCheck/square/blue.css">

  <!-- Google Font -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- php script -->
  <?php
  require "config/db.php";  // $conn = mysqli_connect(...)
  $email = $password = "";
  $all_set = true;
  $errors = [
    'email' => '',
    'password' => '',
    'system' => ''
  ];

  //testing data ;
  function test_data($data)
  {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  // post function 
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // email
    if (empty($_POST['email'])) {
      $errors['email'] = "Email is Required";
      $all_set = false;
    } else {
      $email = test_data($_POST["email"]);
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid Email Address.";
        $all_set = false;
      }
    }

    // password
    if (empty($_POST['password'])) {
      $errors['password'] = "Password is Required";
      $all_set = false;
    } else {
      $password = $_POST['password'];
      if (strlen($password) < 8) {
        $errors['password'] = "Password must have at least 8 characters";
        $all_set = false;
      } else {
        // hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);
      }
    }
    // DB query
    // Insert into DB if all valid
    if ($all_set) {
      // Escape values for safety
      $email = mysqli_real_escape_string($conn, $email);

      $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
      $result = mysqli_query($conn, $query);
      if (!$result) {
        if (mysqli_errno($conn) == 1062) {
          $errors['system'] = "Error: Duplicate entry (maybe email already exists)";
        } else {
          $error['system'] = "Someting went Wrong, Please Try Again.";
          error_log("Database Error:" . mysqli_error($conn));
        }
      } else {
        header("Location: profile.php");
        exit();
      }
    }
  }

  ?>
  <!-- php script end -->
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href=""><b>Admin</b>Login</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <p class="login-box-msg">Sign in to start your session</p>
      <?php if (!empty($errors['system'])): ?>
        <div class="alert alert-danger">
          <?php echo $errors['system']; ?>
        </div>
      <?php endif; ?>
      <form action="" method="post">
        <div class="form-group has-feedback">
          <input type="email" name="email" class="form-control" value="<?php echo $email ?>" placeholder="Email">
          <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          <?php if ($errors['email']): ?>
            <small class="text-danger"><?php echo $errors['email']; ?></small>
          <?php endif; ?>
        </div>
        <div class="form-group has-feedback">
          <input type="password" class="form-control" name="password" value="<?php echo $password ?>"
            placeholder="Password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          <?php if ($errors['password']): ?>
            <small class="text-danger"><?php echo $errors['password']; ?></small>
          <?php endif; ?>
        </div>
        <div class="row">
          <div class="col-xs-8">
            <div class="checkbox icheck">
              <label>
                <input type="checkbox"> Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <a href="#">I forgot my password</a><br>
      <a href="./register.php" class="text-center">Register a new membership</a>

    </div>
    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery 3 -->
  <script src="./bower_components/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- iCheck -->
  <script src="./plugins/iCheck/icheck.min.js"></script>
  <script>
    $(function () {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' /* optional */
      });
    });
  </script>
</body>

</html>