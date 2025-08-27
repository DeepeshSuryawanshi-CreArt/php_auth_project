<?php
require "./config/db.php";
require "./config/authcheck.php"; // starts session, ensures logged-in

$errors = [];
$success = "";
$user;

// Fetch user details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    } else {
        die("User Not Found");
    }
    mysqli_stmt_close($stmt);
} else {
    die("Query Failed:" . mysqli_error($conn));
}

//  create logic
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
        $password = $_POST['password'];
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
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>User Profile Edit</title>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link rel="stylesheet" href="./bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="./dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="./dist/css/skins/_all-skins.min.css">
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include "includes/header.php" ?>
        <?php include "includes/sidebar.php" ?>

        <div class="content-wrapper">
            <section class="content-header">
                <h1>Create New User</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">User Details</h3>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="box-body">
                                    <?php if ($success): ?>
                                        <div class="alert alert-success"><?php echo $success; ?></div>
                                    <?php endif; ?>
                                    <?php if (empty($errors)): ?>
                                        <div class="alert alert-danger"><?php echo implode("<br>", $errors); ?></div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="<?php echo htmlspecialchars($name); ?>" required>
                                        <?php if ($errors['name']): ?>
                                            <small class="text-danger"><?php echo $errors['name']; ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?php echo htmlspecialchars($email); ?>" required>
                                        <?php if ($errors['email']): ?>
                                            <small class="text-danger"><?php echo $errors['email']; ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="number" name="mobile" class="form-control"
                                            value="<?php echo htmlspecialchars($mobile); ?>">
                                        <?php if ($errors['mobile']): ?>
                                            <small class="text-danger"><?php echo $errors['mobile']; ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address"
                                            class="form-control"><?php echo htmlspecialchars($address); ?></textarea>
                                        <?php if ($errors['address']): ?>
                                            <small class="text-danger"><?php echo $errors['address']; ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label for="">Date of Birth</label>
                                        <input type="date" class="form-control" placeholder="Date of Birth" name="dob"
                                            value="<?php echo $dob ?>">
                                        <?php if ($errors['dob']): ?>
                                            <small class="text-danger"><?php echo $errors['dob']; ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select class="form-control" name="gender" selected="<?php echo $gender ?>">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">other</option>
                                        </select>
                                    </div>

                                    <!-- password -->
                                    <div class="form-group has-feedback">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" name="password"
                                            value="<?php echo $password ?>" placeholder="Password">
                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                        <?php if ($errors['password']): ?>
                                            <small class="text-danger"><?php echo $errors['password']; ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label for="Confirm password">confirm Password</label>
                                        <input type="password" class="form-control" name="confirm_password"
                                            placeholder="Retype password">
                                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                        <?php if ($errors['confirm_password']): ?>
                                            <small class="text-danger"><?php echo $errors['confirm_password']; ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label>Profile Picture</label><br>
                                        <?php if (!empty($profile_image)): ?>
                                            <img src="<?php echo $profile_image; ?>" width="100"
                                                class="img-thumbnail"><br><br>
                                        <?php endif; ?>
                                        <input type="file" name="profile_image">
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include "includes/footer.php" ?>
        <div class="control-sidebar-bg"></div>
    </div>

    <script src="./bower_components/jquery/dist/jquery.min.js"></script>
    <script src="./bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="./dist/js/adminlte.min.js"></script>
</body>

</html>