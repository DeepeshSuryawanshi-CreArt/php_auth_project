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
    <!-- Php logics -->
    <?php
    require "config/db.php";  // $conn = mysqli_connect(...)
    $name = $email = $mobile = $gender = $dob = $password = $address = $file_path = '';
    $all_set = true;
    $errors = [
        'name' => '',
        'email' => '',
        'mobile' => '',
        'gender' => '',
        'address' => '',
        'dob' => '',
        'password' => '',
        'confirm_password' => '',
        'file' => '',
        'system' => ''

    ];
    function test_data($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // username
        if (empty($_POST['name'])) {
            $errors['name'] = "Name is Required";
            $all_set = false;
        } else {
            $name = test_data($_POST["name"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
                $errors['name'] = "Only letters and white space allowed";
                $all_set = false;
            }
        }

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

        // mobile
        if (empty($_POST['mobile'])) {
            $errors['mobile'] = "Mobile No. is Required";
            $all_set = false;
        } else {
            $mobile = test_data($_POST["mobile"]);
            if (strlen($mobile) > 10 or strlen($mobile) < 10) {
                $errors['mobile'] = "Invalid Mobile No.";
                $all_set = false;
            }
        }

        // gender
        if (empty($_POST['gender'])) {
            $errors['gender'] = "Gender is require";
            $all_set = false;
        } else {
            $gender = test_data($_POST["gender"]);
        }

        // address
        if (empty($_POST['address'])) {
            $error['address'] = "Address Is require.";
        } else {
            $address = test_data($_POST['address']);
            if (strlen($address) > 10) {
                $error['address'] = "Invalid Address.";
            }
        }
        
        // Date og birth
        if (empty($_POST['dob'])) {
            $error['dob'] = "Date of Birth Is require.";
        } else {
            $dob = test_data($_POST['dob']);
            if (strlen($dob) > 8) {
                $error['address'] = "Invalid Address.";
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

        // Conform - password
        if ($_POST['confirm_password'] !== $_POST['confirm_password']) {
            $errors['confirm_password'] = "conform Passwords do not match";
            $all_set = false;
        }

        // File upload
        if (!empty($_FILES['profile_image']['name'])) {
            if ($_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
                $errors['file'] = "Error uploading file.";
                $all_set = false;
            } else {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
                    $errors['file'] = "Only JPG, PNG, GIF allowed.";
                    $all_set = false;
                } else {
                    $target_dir = "uploads/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
                    $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
                    $target_file = $target_dir . $file_name;

                    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                        $file_path = $target_file;
                    } else {
                        $errors['file'] = "Could not save uploaded file.";
                        $all_set = false;
                    }
                }
            }
        }


        // Insert into DB if all valid
        if ($all_set) {
            // Escape values for safety
            $name = mysqli_real_escape_string($conn, $name);
            $email = mysqli_real_escape_string($conn, $email);
            $mobile = mysqli_real_escape_string($conn, $mobile);
            $gender = mysqli_real_escape_string($conn, $gender);
            $address = mysqli_real_escape_string($conn, $address);
            $dob = mysqli_real_escape_string($conn, $dob);
            $password = mysqli_real_escape_string($conn, $password);
            $file_path = mysqli_real_escape_string($conn, $file_path);

            $query = "INSERT INTO users (name, email, mobile, gender, dob,address ,password, profile_image) 
                  VALUES ('$name', '$email','$mobile','$gender','$dob','$address','$password', '$file_path')";
            $result = mysqli_query($conn, $query);
            if (!$result) {
                if (mysqli_errno($conn) == 1062) {
                    $errors['system'] = "Error: Duplicate entry (maybe email already exists)";
                } else {
                    $error['system'] = "Someting went Wrong, Please Try Again.";
                    error_log("Database Error:" . mysqli_error($conn));
                }
            } else {
                header("Location: login.php?error=$error[system]");
                exit();
            }
        }
    }
    ?>
    <!-- Php end -->
    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition login-page">
    <div class="register-box">
        <div class="login-logo">
            <a href=""><b>Admin</b>Register</a>
        </div>
        <!-- /.login-logo -->
        <div class="register-box-body">
            <p class="register-box-msg">Sign up to be A Member</p>
            <?php if (!empty($errors['system'])): ?>
                <div class="alert alert-danger">
                    <?php echo $errors['system']; ?>
                </div>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <!-- full name -->
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="name" value="<?php echo $name ?>"
                        placeholder="Full name" required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    <?php if ($errors['name']): ?>
                        <small class="text-danger"><?php echo $errors['name']; ?></small>
                    <?php endif; ?>
                </div>
                <!-- email -->
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" name="email" value="<?php echo $email ?>"
                        placeholder="Email" required>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    <?php if ($errors['email']): ?>
                        <small class="text-danger"><?php echo $errors['email']; ?></small>
                    <?php endif; ?>
                </div>
                <!-- mobile -->
                <div class="form-group has-feedback">
                    <input type="number" class="form-control" name="mobile" value="<?php echo $mobile ?>"
                        placeholder="Mobile No." required>
                    <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                    <?php if ($errors['mobile']): ?>
                        <small class="text-danger"><?php echo $errors['mobile']; ?></small>
                    <?php endif; ?>
                </div>
                <!-- gender -->
                <div class="form-group has-feedback">
                    <select class="form-control" name="gender" value="<?php echo $gender ?>" required>
                        <option value="">Select Gender</option>
                        <option class="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">other</option>
                    </select>
                    <?php if ($errors['gender']): ?>
                        <small class="text-danger"><?php echo $errors['gender']; ?></small>
                    <?php endif; ?>
                </div>
                <!-- Address -->
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="address" value="<?php echo $address ?>"
                        placeholder="Address" required>
                    <span class="glyphicon glyphicon-home form-control-feedback"></span>
                    <?php if ($errors['address']): ?>
                        <small class="text-danger"><?php echo $errors['address']; ?></small>
                    <?php endif; ?>
                </div>
                <!-- dob -->
                <div class="form-group has-feedback">
                    <input type="date" class="form-control" placeholder="Date of Birth" name="dob"
                        value="<?php echo $dob ?>">
                    <?php if ($errors['dob']): ?>
                        <small class="text-danger"><?php echo $errors['dob']; ?></small>
                    <?php endif; ?>
                </div>
                <!-- password -->
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" value="<?php echo $password ?>"
                        placeholder="Password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <?php if ($errors['password']): ?>
                        <small class="text-danger"><?php echo $errors['password']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="confirm_password" placeholder="Retype password">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    <?php if ($errors['confirm_password']): ?>
                        <small class="text-danger"><?php echo $errors['confirm_password']; ?></small>
                    <?php endif; ?>
                </div>
                <!-- Profile Image -->
                <div class="form-group has-feedback">
                    <input type="file" class="form-control" name="profile_image" placeholder="Chose Profile Image">
                    <span class="glyphicon glyphicon-file form-control-feedback"></span>
                    <?php if ($errors['file']): ?>
                        <small class="text-danger"><?php echo $errors['file']; ?></small>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox"> I agree to the <a href="#">terms</a>
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-12">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>
            <div class="mt-2">
                <a href="#">I forgot my password</a><br>
                <a href="./login.php" class="text-center">Sign in</a>
            </div>

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