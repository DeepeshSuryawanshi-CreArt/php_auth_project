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

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $profile_image = $user['profile_image'];

    // Handle profile picture upload
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
    else{
        $file_path = $user['profile_image'];
    }

    if (empty($errors)) {
        $update_sql = "UPDATE users SET name=?, email=?, mobile=?, address=?, profile_image=?, gender=?, dob=? WHERE id=?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "sssssssi", $name, $email, $mobile, $address, $file_path, $gender , $dob,$user_id);
        if (mysqli_stmt_execute($update_stmt)) {
            $success = "Profile updated successfully!";
            $user['name'] = $name;
            $user['email'] = $email;
            $user['mobile'] = $mobile;
            $user['address'] = $address;
            $user['profile_image'] = $profile_image;
            header('Location:profile.php');
        } else {
            $errors[] = "Database update failed.";
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
                <h1>Edit Profile</h1>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Update Your Details</h3>
                            </div>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="box-body">
                                    <?php if ($success): ?>
                                        <div class="alert alert-success"><?php echo $success; ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($errors)): ?>
                                        <div class="alert alert-danger"><?php echo implode("<br>", $errors); ?></div>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <input type="text" name="mobile" class="form-control"
                                            value="<?php echo htmlspecialchars($user['mobile']); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address"
                                            class="form-control"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                    </div>

                                    <div class="form-group has-feedback">
                                        <label for="">Date of Birth</label>
                                        <input type="date" class="form-control" placeholder="Date of Birth" name="dob"
                                            value="<?php echo $user['dob'] ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select class="form-control" name="gender"
                                            selected="<?php echo $user['gender'] ?>">
                                            <option value="" disabled hidden>Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Profile Picture</label><br>
                                        <?php if (!empty($user['profile_image'])): ?>
                                            <img src="<?php echo $user['profile_image']; ?>" width="100"
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