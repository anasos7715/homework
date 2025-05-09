
<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "test");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// Variables for storing messages
$message = "";
$last_id = "";
$file_content = "";
$first_ten_chars = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_submit'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    if (empty($fname) || empty($lname) || empty($email) || empty($phone)) {
        $message = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> All fields are required!
                    </div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> Invalid email address!
                    </div>";
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> Phone number must be 10 digits!
                    </div>";
    } else {
        $fname = mysqli_real_escape_string($conn, $fname);
        $lname = mysqli_real_escape_string($conn, $lname);
        $email = mysqli_real_escape_string($conn, $email);
        $phone = mysqli_real_escape_string($conn, $phone);

        $query = "INSERT INTO users (fname, lname, email, phone) VALUES ('$fname', '$lname', '$email', '$phone')";
        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success d-flex align-items-center' role='alert'>
                            <i class='bi bi-check-circle-fill me-2'></i> Data added successfully!
                        </div>";
        } else {
            $message = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                            <i class='bi bi-exclamation-triangle-fill me-2'></i> Error: " . mysqli_error($conn) . "
                        </div>";
        }
    }
}

// Insert static data
$static_fname = "Mohammed";
$static_lname = "Ahmed";
$static_email = "mohammed@example.com";
$static_phone = "1234567890";
if (!empty($static_fname) && filter_var($static_email, FILTER_VALIDATE_EMAIL)) {
    $query = "SELECT COUNT(*) AS count FROM users WHERE email = '$static_email'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    if ($row['count'] == 0) {
        $static_fname = mysqli_real_escape_string($conn, $static_fname);
        $static_lname = mysqli_real_escape_string($conn, $static_lname);
        $static_email = mysqli_real_escape_string($conn, $static_email);
        $static_phone = mysqli_real_escape_string($conn, $static_phone);
        $query = "INSERT INTO users (fname, lname, email, phone) VALUES ('$static_fname', '$static_lname', '$static_email', '$static_phone')";
        if (!mysqli_query($conn, $query)) {
            die("Error inserting static data: " . mysqli_error($conn));
        }
    }
}

// Fetch the last ID
$query = "SELECT MAX(id) AS last_id FROM users";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$last_id = $row['last_id'] ? $row['last_id'] : "No data available";

// Read or create data.txt
$existing_file = "data.txt";
if (!file_exists($existing_file)) {
    // Create the file with default content
    $default_content = "Default data for data.txt";
    $file_handle = fopen($existing_file, "w"); // Open in write mode (creates file)
    if ($file_handle === false) {
        $file_message = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                            <i class='bi bi-exclamation-triangle-fill me-2'></i> Failed to create $existing_file!
                        </div>";
    } else {
        fwrite($file_handle, $default_content);
        fclose($file_handle);
        $file_message = "<div class='alert alert-info d-flex align-items-center' role='alert'>
                            <i class='bi bi-info-circle-fill me-2'></i> File $existing_file created with default content: " . htmlspecialchars($default_content) . "
                        </div>";
    }
} else {
    $file_content = file_get_contents($existing_file);
    $file_message = "<div class='alert alert-info d-flex align-items-center' role='alert'>
                        <i class='bi bi-info-circle-fill me-2'></i> File content: " . htmlspecialchars($file_content) . "
                    </div>";
}

// Create and write to newfile.txt
$new_file = "newfile.txt";
$new_data = "Hello, this is a new file";
$file_handle = fopen($new_file, "w"); // Open in write mode (creates or overwrites)
if ($file_handle === false) {
    $new_file_message = "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                            <i class='bi bi-exclamation-triangle-fill me-2'></i> Failed to create $new_file!
                        </div>";
} else {
    fwrite($file_handle, $new_data);
    fclose($file_handle);
    $new_file_content = file_get_contents($new_file);
    $new_file_message = "<div class='alert alert-info d-flex align-items-center' role='alert'>
                            <i class='bi bi-info-circle-fill me-2'></i> New file content: " . htmlspecialchars($new_file_content) . "
                        </div>";
}

// Append data to output.txt and print characters
$output_file = "output.txt";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['file_text'])) {
    $text = $_POST['file_text'];
    $file_handle = fopen($output_file, "a"); // Open in append mode
    if ($file_handle === false) {
        $file_message .= "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                            <i class='bi bi-exclamation-triangle-fill me-2'></i> Failed to append to $output_file!
                        </div>";
    } else {
        fwrite($file_handle, $text . "\n");
        fclose($file_handle);
        $file_message .= "<div class='alert alert-success d-flex align-items-center' role='alert'>
                            <i class='bi bi-check-circle-fill me-2'></i> Text appended to file successfully!
                        </div>";
    }
}

// Append static data to output.txt
$static_file_data = "Static data";
$file_handle = fopen($output_file, "a"); // Open in append mode
if ($file_handle !== false) {
    fwrite($file_handle, $static_file_data . "\n");
    fclose($file_handle);
}

// Read output.txt and extract first 10 characters
if (file_exists($output_file)) {
    $output_content = file_get_contents($output_file);
    $first_ten_chars = substr($output_content, 0, 10);
    $file_message .= "<div class='alert alert-info d-flex align-items-center' role='alert'>
                        <i class='bi bi-info-circle-fill me-2'></i> First 10 characters of file: " . htmlspecialchars($first_ten_chars) . "
                    </div>";
} else {
    $file_message .= "<div class='alert alert-danger d-flex align-items-center' role='alert'>
                        <i class='bi bi-exclamation-triangle-fill me-2'></i> File $output_file does not exist!
                    </div>";
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Entry System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: scale(1.05);
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Data Entry System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <!-- Data Entry Form -->
            <div class="col-lg-6 mb-4">
                <div class="card p-4">
                    <h2 class="card-title mb-4">Enter Data</h2>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="fname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <button type="submit" name="form_submit" class="btn btn-primary w-100">
                            <i class="bi bi-send-fill me-2"></i>Submit
                        </button>
                    </form>
                </div>
            </div>

            <!-- File Input Form -->
            <div class="col-lg-6 mb-4">
                <div class="card p-4">
                    <h2 class="card-title mb-4">Append Text to File</h2>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Enter Text</label>
                            <input type="text" name="file_text" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class='bi bi-file-earmark-text-fill me-2'></i>Append to File
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display Messages -->
        <?php if (!empty($message)) echo $message; ?>
        <?php if (!empty($file_message)) echo $file_message; ?>
        <?php if (!empty($new_file_message)) echo $new_file_message; ?>

        <!-- Display Last ID -->
        <div class="card p-4 mt-4">
            <h3 class="card-title">Last Record ID: <?php echo htmlspecialchars($last_id); ?></h3>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

