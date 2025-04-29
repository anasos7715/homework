<?php
$conn = mysqli_connect("localhost", "root", "", "test");

$submitted = false;
$errors = [];
$last_id = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function clean($data) {
        return quotemeta(trim($data)); // Use quotemeta as per book
    }
    $fname = clean($_POST['fname']);
    $lname = clean($_POST['lname']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $language = clean($_POST['language']);
}
    

    if (!preg_match("/^(\+962|0)[0-9]{9}$/", $phone)) {
        $errors[] = "Invalid phone number. It must start with +962 or 0 and followed by 9 digits.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO users (fname, lname, email, phone, language)
                  VALUES ('$fname', '$lname', '$email', '$phone', '$language')";
        if (mysqli_query($conn, $query)) {
            $last_id = mysqli_insert_id($conn);
            $submitted = true;
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dynamic Bootstrap Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h2 class="text-center mb-4">Registration Form</h2>

            <?php if ($submitted): ?>
              <div class="alert alert-success">
                <strong>Form submitted successfully!</strong><br>
                <ul class="mb-0">
                  <li><strong>Serial Number:</strong> <?= $last_id ?></li>
                  <li><strong>Name:</strong> <?= $fname ?> <?= $lname ?></li>
                  <li><strong>Email:</strong> <?= $email ?></li>
                  <li><strong>Phone:</strong> <?= $phone ?></li>
                  <li><strong>Favorite Language:</strong> <?= $language ?></li>
                </ul>
              </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul class="mb-0">
                  <?php foreach ($errors as $err): ?>
                    <li><?= $err ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <form method="post" action="">
              <div class="mb-3">
                <label for="fname" class="form-label">First Name</label>
                <input type="text" class="form-control" name="fname" id="fname" required>
              </div>

              <div class="mb-3">
                <label for="lname" class="form-label">Last Name</label>
                <input type="text" class="form-control" name="lname" id="lname" required>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
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

              <button type="submit" class="btn btn-success w-100">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
