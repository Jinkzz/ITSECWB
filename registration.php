<?php
require 'core.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['full_name'];
    
    // filter_var checks if the email is in a valid format (e.g., name@email.com)
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    
    // preg_replace removes anything that isn't a number to clean the phone input
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']); 

    // password_hash() converts the password into a secure string.
    // It automatically creates a "salt" to make it impossible to reverse.
    // The 'cost' determines the number of iterations (2^cost). 
    $options = ['cost' => 12]; 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT, $options);

    $target_dir = "uploads/";

    // We get the file extension (like .jpg or .png)
    $file_ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
    
    // finfo (File Information) looks AT THE ACTUAL DATA inside the file.
    // This stops people from renaming a virus as "image.jpg".
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES["photo"]["tmp_name"]);
    $allowed_mimes = ['image/jpeg', 'image/png'];
    
    // Check if the file is a real image AND if the email was valid
    if (in_array($mime, $allowed_mimes) && $email) {
        
        // Gives the photo a random name so hackers can't guess it
        $photo_name = bin2hex(random_bytes(10)) . "." . $file_ext;
        
        // move_uploaded_file transfers the photo from temporary storage to your 'uploads' folder
        if(move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $photo_name)) {
            try {
                // Prepared Statements (?) prevent SQL Injection attacks
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, profile_photo) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $email, $phone, $password, $photo_name]);
                
                $message = "<div class='alert alert-success'>Registration successful! <a href='login.php'>Login here</a></div>";
            } catch (PDOException $e) {
                // If the email is already in the database, SQL sends code 23000
                if ($e->getCode() == 23000) { 
                    $message = "<div class='alert alert-danger'>This email is already registered.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
                }
            }
        } else {
            $message = "<div class='alert alert-danger'>Failed to save the photo. Make sure the 'uploads' folder exists!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Invalid input or file type. Only JPG/PNG allowed.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 card p-4 shadow-sm">
            <h3 class="text-center mb-4">Create Account</h3>
            <?php echo $message; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="col"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" required></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
                <div class="mb-3">
                    <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>