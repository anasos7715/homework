<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #4CAF50;
        }
        .visitor {
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
<?php
if (!isset($_POST['submit'])) {
    die('Please submit the form.');
}
$name = $_POST['myname'] ;
$email = $_POST['email'] ;
$gender = $_POST['gender'] ;
$inquiry_type = $_POST['inquiry_type'] ;

if (!preg_match('/^[[:alnum:]]+@[[:alnum:]]+\.com$/i', $email)) {
    die('<div class="alert alert-danger">Email is invalid. Please use an email ending in .com with alphanumeric characters.</div>');
}

// Connect to database
$con = mysqli_connect('localhost', 'root', '', 'dummy');
if (!$con) {
    die('<div class="alert alert-danger">Could not connect to the database!</div>');
}

// Prepare and execute query
$stmt = $con->prepare("INSERT INTO contacts (name, email, gender, inquiry_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $gender, $inquiry_type);

if (!$stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    die('<div class="alert alert-danger">Could not execute query!</div>');
}

// Get last inserted ID
$last_id = mysqli_insert_id($con);

$stmt->close();
mysqli_close($con);
?>

<div class="p-3">
    <h1 class="text-success">Thank you</h1>
    <p class="visitor">You are visitor number: <?php echo htmlspecialchars($last_id); ?></p>
    <p>You provided us with the following info:</p>
    <ul class="list-group">
        <li class="list-group-item">Name: <?php echo htmlspecialchars($name); ?></li>
        <li class="list-group-item">Email: <?php echo htmlspecialchars($email); ?></li>
        <li class="list-group-item">Gender: <?php echo ($gender == 1) ? 'Male' : 'Female'; ?></li>
        <li class="list-group-item">Inquiry Type: 
            <?php 
            $inquiry_types = [
                'support' => 'Support',
                'sales' => 'Sales',
                'feedback' => 'Feedback'
            ];
            echo htmlspecialchars($inquiry_types[$inquiry_type] ?? 'Unknown');
            ?>
        </li>
    </ul>
</div>
</body>
</html>
