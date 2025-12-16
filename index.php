//   Php link = http://localhost/Week-4_FSD/index.php/    //
<?php
// Initialize variables
$name = $email = $password = $confirm_password = "";
$nameErr = $emailErr = $passwordErr = $confirmErr = "";
$successMsg = "";

// If form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ------------------ VALIDATION ------------------

    // Name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required.";
    } else {
        $name = htmlspecialchars($_POST["name"]);
    }

    // Email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required.";
    } else {
        $email = htmlspecialchars($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format.";
        }
    }

    // Password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required.";
    } else {
        $password = $_POST["password"];

        // Password criteria: min 6 chars + 1 special char
        if (strlen($password) < 6 || !preg_match('/[\W]/', $password)) {
            $passwordErr = "Password must be at least 6 characters and contain a special character.";
        }
    }

    // Confirm password
    if (empty($_POST["confirm_password"])) {
        $confirmErr = "Please confirm your password.";
    } else {
        $confirm_password = $_POST["confirm_password"];

        if ($password !== $confirm_password) {
            $confirmErr = "Passwords do not match.";
        }
    }

    // If no validation errors → process registration
    if ($nameErr == "" && $emailErr == "" && $passwordErr == "" && $confirmErr == "") {

        $file = "users.json";

        // File exists?
        if (!file_exists($file)) {
            file_put_contents($file, "[]");
        }

        // Read JSON file
        $jsonData = file_get_contents($file);
        if ($jsonData === false) {
            die("<div style='color:red;'>Error reading JSON file.</div>");
        }

        $users = json_decode($jsonData, true);
        if ($users === null) {
            die("<div style='color:red;'>Error decoding JSON file.</div>");
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare user array
        $newUser = [
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword
        ];

        // Add to users array
        $users[] = $newUser;

        // Write back to JSON file
        $updatedJson = json_encode($users, JSON_PRETTY_PRINT);
        if (file_put_contents($file, $updatedJson) === false) {
            die("<div style='color:red;'>Error writing to JSON file.</div>");
        }

        // Success message
        $successMsg = "✔ Registration Successful!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <style>
        .error { color: red; font-size: 14px; }
        .success { color: green; font-size: 18px; margin-bottom: 15px; }
        form { width: 320px; margin: auto; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        label { margin-top: 10px; display: block; }
    </style>
</head>
<body>

<h2 style="text-align:center;">User Registration</h2>

<?php if ($successMsg): ?>
    <div class="success"><?php echo $successMsg; ?></div>
<?php endif; ?>

<form method="POST">

    <label>Name:</label>
    <input type="text" name="name" value="<?php echo $name; ?>">
    <span class="error"><?php echo $nameErr; ?></span>

    <label>Email:</label>
    <input type="text" name="email" value="<?php echo $email; ?>">
    <span class="error"><?php echo $emailErr; ?></span>

    <label>Password:</label>
    <input type="password" name="password">
    <span class="error"><?php echo $passwordErr; ?></span>

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password">
    <span class="error"><?php echo $confirmErr; ?></span>

    <br><br>
    <input type="submit" value="Register">

</form>

</body>
</html>
