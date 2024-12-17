<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dog_show";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$owner_id = isset($_GET['id']) ? $_GET['id'] : '';

$sql = "SELECT name, phone, email FROM owners WHERE id = ?;";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $owner = $result->fetch_assoc();
} else {
    $owner = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php if ($owner):
            echo $owner['name']; 
        ?>
        <?php else: ?>
            Owner not found
        <?php endif; ?>
    </title>
    <link rel="stylesheet" href="./css/styles.css">
    <body style="background-color: #eef6ef;">
        <div class="box" style="top: 50vh;">   
            <?php if ($owner): ?>
                <h1><?php echo $owner['name']; ?></h1>
                <p><strong>Phone:</strong> <?php echo $owner['phone']; ?></p>
                <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($owner['email']); ?>"><?php echo htmlspecialchars($owner['email']); ?></a></p>            
            <?php else: ?>
                <p>Owner not found</p>
            <?php endif; ?>
        </div>
    </body>
</head>
</html>