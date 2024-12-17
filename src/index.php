<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dog_show";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* Get the total number of owners, dogs, and amount of events hosted */
$sql = "SELECT 
(SELECT COUNT(DISTINCT owner_id) FROM dogs WHERE owner_id IS NOT NULL) AS owners, 
(SELECT COUNT(DISTINCT dog_id) FROM entries WHERE dog_id IS NOT NULL) AS dogs, 
(SELECT COUNT(DISTINCT competition_id) FROM entries WHERE id IS NOT NULL) AS events";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $owners = $row["owners"];
        $dogs = $row["dogs"];
        $events = $row["events"];
    }
} else {
    $owners = $dogs = $events = 0;
}

$sql = "SELECT d.id AS dog_id, d.name AS dog_name, b.name AS breed_name, o.name AS owner_name, o.id AS owner_id, AVG(e.score) AS average_score
FROM entries e
JOIN dogs d ON e.dog_id = d.id
JOIN owners o ON d.owner_id = o.id
JOIN breeds b ON d.breed_id = b.id
GROUP BY d.id, d.name, b.name, o.name, o.id
HAVING COUNT(e.id) > 1
ORDER BY average_score DESC
LIMIT 10;";

$result = $conn->query($sql);
$topDogs = [];
if($result ->num_rows > 0) {
    while($row = $result -> fetch_assoc()) {
        $topDogs[] = $row;
    }
} else {
    $topDogs = $events = 0;
}
$conn->close();
?>


<!DOCTYPE html>
<head>
    <html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poppleton Dog Show</title>
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <!-- Main Image Banner Section -->
    <main>
        <section class="imageContainer">
            <header class="image-container__header">
                <h2>WELCOME TO THE</h2>
                <h1>Poppleton Dog Show</h1>
            </header>
            <img src="../assets/show.webp" alt="Dog Show">
        </section>
        <div class="box" style="top: 140vh;">
            <h3>WE LOOK FORWARD TO SEEING YOU NEXT YEAR!</h3>
            <h1>This year <?php echo $owners; ?> owners entered<br><?php echo $dogs; ?> dogs in <?php echo $events; ?> events!</h1>
            <p>
                This year, we saw an incredible turnout at the Poppleton Paws Dog Show, where <?php echo $owners; ?> participants
                brought their 4-legged friends to compete in exciting competitions. We ran a total of <?php echo $events; ?>
                events throughout the year, with competition being very fierce. Every participant showcasing their unique
                talents and incredible abilities, from events such as <em>Agility courses</em> to events like 
                <em>Loudest Bark</em> and even the <em>Waggiest Tail</em>, the dogs and their handlers all demonstrated
                remarkable skill and determination, making it truly an unforgettable event. It is heartwarming to witness
                the bond between the owners and their companions, and this year's show certainly highlighted the passion
                and commitment of the dog-loving community and brought communities and families together.
            </p>
            <p>
                <br>We would like to extend our heartfelt thanks to all the participants, volunteers, and sponsors who made
                this event possible. We are grateful for your support and dedication to the Poppleton Dog Show, and we
                look forward to seeing you all again next year! Tickets for the next year's show will be available soon.
            </p>
            <div class="box__footer">
                <p>ASK US A QUESTION</p>
                <p><a href="mailto:dogshow@poppleton.co.uk">dogshow@poppleton.co.uk</a></p>            
            </div>
        </div>
    </main>

    <!-- Sub-Sections -->
    <div style="background-color: #eef6ef; width: 100%; height: 1000px"></div> 
    <div class="container">
        <section class ="banner">
            <h1 style="color: #0b3a2e; font-size: 3rem;">2024 Leaderboard</h1>
        </section>
        <h1 style="color: #0b3a2e; margin-top: 100px;">Our Top 10 Winners</h1>
        <table class="table">
            <thead>
                <tr style="color: black">
                    <th>Position</th>
                    <th>Dog Name</th>
                    <th>Breed</th>
                    <th>Owner</th>
                    <th>Average Score</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $position = 1;
                foreach($topDogs as $dog) {
                    echo "<tr>";
                    echo "<td>" . $position . "</td>";
                    echo "<td>" . $dog["dog_name"] . "</td>";
                    echo "<td>" . $dog["breed_name"] . "</td>";
                    echo "<td><a href='owner.php?id=" . urlencode($dog["owner_id"]) . "'>" . htmlspecialchars($dog["owner_name"]) . "</a></td>";
                    echo "<td>" . number_format($dog["average_score"], 2) . "</td>";
                    echo "</tr>";
                    $position++;
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>