<!-- Student Id : 105299366 , Name : Dibbo Barua Chamak -->
<html>
<body style="background-color: yellow;">

<h2>ShipOnline System Login Page</h2>
<form method="post"  style="border: 2px solid black; padding: 20px; width: 95%;">
    <label for="customer_number">Customer Number:</label>
    <input type="text" name="customer_number" id="customer_number" required><br/><br/>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required><br/><br/>

    <input type="submit" name="login" value="Login">
</form>

<?php
// Connect to the database
$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s105299366", "080798", "s105299366_db")
Or die ("<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_connect_errno() . ": " . mysqli_connect_error() . "</p>");

if (isset($_POST['login'])) {
    // Retrieve form data
    $customer_number = $_POST['customer_number'];
    $password = $_POST['password'];

    // get password from database
    $SQLstring = "SELECT password FROM customers WHERE customer_number='$customer_number'";
    $queryResult = @mysqli_query($DBConnect, $SQLstring);

    if ($queryResult) {
        $row = mysqli_fetch_assoc($queryResult);
        
        if ($row) {
            // Verify the password
            if ($password === $row['password']) {
                // Password is correct, redirect to request page
                header("Location: request.php?customer_number=$customer_number");
                exit();
            } else {
                echo "<p style='color:red;'>Incorrect password.</p>";
            }
        } else {
            echo "<p style='color:red;'>Customer number does not exist.</p>";
        }
    } else {
        die ("<p>Unable to query the table.</p>" . "<p>Error code " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>");
    }
}
// Close the database connection
mysqli_close($DBConnect);
?>
<a href="shiponline.php">Home</a>
</body>
</html>
