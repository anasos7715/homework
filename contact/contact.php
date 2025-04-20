<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
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

$name = $_POST['myname'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$inquiry_type = $_POST['inquiry_type'];


// الاتصال بقاعدة البيانات
$con = mysqli_connect('localhost', 'root', '', 'dummy');
if (!$con) {
    die('<h3>Could not connect to the database!</h3>');
}

// إدخال البيانات باستخدام Prepared Statement
$stmt = $con->prepare("INSERT INTO contacts (name, email, gender, inquiry_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $gender, $inquiry_type);

if (!$stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    die('<h3>Could not execute query!</h3>');
}

// استرجاع آخر ID
$last_id = mysqli_insert_id($con);

$stmt->close();
mysqli_close($con);
?>

<h1>Thank you</h1>
<p class="visitor">You are visitor number: <?php echo htmlspecialchars($last_id); ?></p>
<p>You provided us with the following info:</p>
<p>Name: <?php echo htmlspecialchars($name); ?></p>
<p>Email: <?php echo htmlspecialchars($email); ?></p>
<p>Gender: <?php echo ($gender == 1) ? 'Male' : 'Female'; ?></p>
<p>Inquiry Type: 
    <?php 
    $inquiry_types = [
        'support' => 'Support',
        'sales' => 'Sales',
        'feedback' => 'Feedback'
    ];
    echo htmlspecialchars($inquiry_types[$inquiry_type]);
    ?>
</p>
</body>
</html>