<!-- Student Id : 105299366 , Name : Dibbo Barua Chamak -->
<html>
<body style="background-color: yellow;">
<h2>Register a New Customer</h2>

<!-- Form to register new customer -->
<form method="post" style="border: 2px solid black; padding: 20px; width: 95%;">
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required><br/><br/>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required><br/><br/>

    <label for="confirm_password">Re-type Password:</label>
    <input type="password" name="confirm_password" id="confirm_password" required><br/><br/>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required><br/><br/>

    <label for="phone_number">Phone Number:</label>
    <input type="text" name="phone_number" id="phone_number" required><br/><br/>

    <input type="submit" name="register" value="Register">
</form>
<?php
// Connect to the database
$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s105299366", "080798", "s105299366_db")
Or die ("<p>Unable to connect to the database server.</p>" . "<p>Error code " . mysqli_connect_errno() . ": " . mysqli_connect_error() . "</p>");

if (isset($_POST['register'])) {
    // Retrieve form data
    $name = $_POST['name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<p style='color:red;'>Passwords do not match.</p>";
    } else {
        // Check if email is unique
        $SQLstring = "SELECT * FROM customers WHERE email='$email'";
        $queryResult = @mysqli_query($DBConnect, $SQLstring);

        if (mysqli_num_rows($queryResult) > 0) {
            echo "<p style='color:red;'>Email already exists. Please use a different email address.</p>";
        } else {

            // Insert new customer into the database
            $SQLstring = "INSERT INTO customers (name, password, email, phone_number) 
                          VALUES ('$name', '$password', '$email', '$phone_number')";
            
            $queryResult = @mysqli_query($DBConnect, $SQLstring);
            if ($queryResult) {
                $customer_number = mysqli_insert_id($DBConnect); // Get the generated customer number
                echo "<p>Dear $name, you are successfully registered into ShipOnline, and your customer number is $customer_number, which will be used to get into the system.</p>";
            } else {
                die ("<p>Unable to insert the data into the table.</p>" . "<p>Error code " . mysqli_errno($DBConnect) . ": " . mysqli_error($DBConnect) . "</p>");
            }
        }
    }
    // Close the database connection
    mysqli_close($DBConnect);
}
?>
<a href="shiponline.php">Home</a>
</body>
</html>
