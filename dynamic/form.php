<?php
$conn = mysqli_connect("localhost", "root", "", "test");

$submitted = false;
$errors = [];
$last_id = null;
$file_data = null;

// Clean input data for database insertion
function clean($data) {
    return quotemeta(trim($data));
}

// Validate phone number
function validate_phone($phone) {
    return preg_match("/^(?:\+962[0-9]{9}|0[0-9]{9})$/", $phone);
}

// Get last serial number from database
function get_last_serial($conn) {
    $result = mysqli_query($conn, "SELECT MAX(id) as last_id FROM users");
    $row = mysqli_fetch_assoc($result);
    return $row['last_id'] ? $row['last_id'] : 0;
}

// Read data from CSV file
$file_path = "data.csv";
if (file_exists($file_path)) {
    $file_handle = fopen($file_path, "r");
    // Skip header row
    $header = fgetcsv($file_handle);
    if ($row = fgetcsv($file_handle)) {
        $file_data = [
            'fname' => clean($row[0]),
            'lname' => clean($row[1]),
            'email' => clean($row[2]),
            'phone' => clean($row[3]),
            'language' => clean($row[4])
        ];
    }
    fclose($file_handle);
} else {
    $errors[] = "Data file (data.csv) not found.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_submit'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $language = trim($_POST['language']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!validate_phone($phone)) {
        $errors[] = "Invalid phone number. It must start with +962 or 0 and followed by 9 digits. Provided: '$phone'.";
    }

    if (empty($errors)) {
        // Clean data for database
        $fname_db = clean($fname);
        $lname_db = clean($lname);
        $email_db = clean($email);
        $phone_db = clean($phone);
        $language_db = clean($language);

        $query = "INSERT INTO users (fname, lname, email, phone, language)
                  VALUES ('$fname_db', '$lname_db', '$email_db', '$phone_db', '$language_db')";
        if (mysqli_query($conn, $query)) {
            $last_id = mysqli_insert_id($conn);
            $submitted = true;
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}

// Handle direct data insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['direct_submit'])) {
    $fname = "John";
    $lname = "Doe";
    $email = "john.doe@example.com";
    $phone = "0791234567"; // Changed to simpler format
    $language = "Python";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format in direct data.";
    }

    if (!validate_phone($phone)) {
        $errors[] = "Invalid phone number in direct data. Provided: '$phone'.";
    }

    if (empty($errors)) {
        // Clean data for database
        $fname_db = clean($fname);
        $lname_db = clean($lname);
        $email_db = clean($email);
        $phone_db = clean($phone);
        $language_db = clean($language);

        $query = "INSERT INTO users (fname, lname, email, phone, language)
                  VALUES ('$fname_db', '$lname_db', '$email_db', '$phone_db', '$language_db')";
        if (mysqli_query($conn, $query)) {
            $last_id = mysqli_insert_id($conn);
            $submitted = true;
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}

$last_serial = get_last_serial($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dynamic Registration Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="text-center mb-4">Registration Form</h2>

            <div class="alert alert-info">
              <strong>Last Serial Number in Database:</strong> <?= $last_serial ?>
            </div>

            <?php if ($submitted): ?>
              <div class="alert alert-success">
                <strong>Form submitted successfully!</strong><br>
                <ul class="mb-0">
                  <li><strong>Serial Number:</strong> <?= $last_id ?></li>
                  <li><strong>Name:</strong> <?= htmlspecialchars($fname) ?> <?= htmlspecialchars($lname) ?></li>
                  <li><strong>Email:</strong> <?= htmlspecialchars($email) ?></li>
                  <li><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></li>
                  <li><strong>Favorite Language:</strong> <?= htmlspecialchars($language) ?></li>
                </ul>
              </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul class="mb-0">
                  <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if ($file_data): ?>
              <div class="alert alert-warning">
                <strong>Data from file:</strong> 
                <?= htmlspecialchars($file_data['fname']) ?> <?= htmlspecialchars($file_data['lname']) ?> 
                with email <?= htmlspecialchars($file_data['email']) ?> 
                prefers <?= htmlspecialchars($file_data['language']) ?>.
              </div>
            <?php endif; ?>

            <form method="post" action="">
              <input type="hidden" name="form_submit" value="1">
              <div class="mb-3">
                <label for="fname" class="form-label">First Name</label>
                <input type="text" class="form-control" name="fname" id="fname" required>
              </div>

              <div class="mb-3">
                <label for="lname" class="form-label">Last Name</label>
                <input type="text" class="form-control" name="lname" id="lname" required>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" id="email" required>
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" name="phone" id="phone" required>
                <div class="form-text">Format: +962xxxxxxxxx or 0xxxxxxxxx</div>
              </div>

              <div class="mb-3">
                <label for="language" class="form-label">Favorite Language</label>
                <select class="form-select" name="language" id="language">
                  <option value="PHP">PHP</option>
                  <option value="JavaScript">JavaScript</option>
                  <option value="Python">Python</option>
                  <option value="Java">Java</option>
                </select>
              </div>

              <button type="submit" class="btn btn-success w-100 mb-3">Submit Form</button>
            </form>

            <form method="post" action="">
              <input type="hidden" name="direct_submit" value="1">
              <button type="submit" class="btn btn-primary w-100">Insert Direct Data</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
