<!-- Student Id : 105299366 , Name : Dibbo Barua Chamak -->
<html>
<body style="background-color: yellow;">

 <!-- form inputs -->
<form method="post" style="border: 2px solid black; padding: 20px; width: 95%;">
    <p>Item Information:</p>
    <div style="border: 2px solid black; padding: 20px; width: 95%;">       
        <label for="item_description">Description:</label>
        <input type="text" name="item_description" id="item_description" required><br/><br/>
        <label for="weight">Weight:</label>
        <select name="weight" id="weight"  required>
        <option value="" disabled selected>Select Weight</option>
            <?php for ($i = 2; $i <= 20; $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?> kg</option>
            <?php endfor; ?>
        </select><br/><br/>
    </div>
    <p>Pickup Information:</p>
    <div style="border: 2px solid black; padding: 20px; width: 95%;">       
        <label for="pickup_address">Address:</label>
        <input type="text" name="pickup_address" id="pickup_address" required><br/><br/>

        <label for="pickup_suburb">Suburb:</label>
        <input type="text" name="pickup_suburb" id="pickup_suburb" required><br/><br/>

        <label for="pickup_day">Preferred Date:</label>
            <select name="pickup_day" id="pickup_day" required>
            <option value="" disabled selected>Day</option>
                <?php for ($i = 1; $i <= 31; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                } ?>
            </select>
            <select name="pickup_month" id="pickup_month" required>
            <option value="" disabled selected>Month</option>
                <?php for ($i = 1; $i <= 12; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                } ?>
            </select>
            <select name="pickup_year" id="pickup_year" required>
            <option value="" disabled selected>Year</option>
                <?php for ($i = date('Y'); $i <= date('Y') + 1; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                } ?>
            </select><br/><br/>

    <label for="pickup_hour">Preferred Time:</label>
            <select name="pickup_hour" id="pickup_hour" required>
            <option value="" disabled selected>Hour</option>
                <?php for ($i = 8; $i <= 20; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                } ?>
    </select>
    Minute:
    <input type="text" name="pickup_minute" id="pickup_minute" maxlength="2" required><br/><br>
    <small>If you don't input minute properly, we'll assume you want us to pick the item up at the exact hour</small>
    </div>
    <p>Delivery Information:</p>
    <div style="border: 2px solid black; padding: 20px; width: 95%;"> 
    <label for="receiver_name">Receiver Name:</label>
    <input type="text" name="receiver_name" id="receiver_name" required><br/><br/>

    <label for="delivery_address">Address:</label>
    <input type="text" name="delivery_address" id="delivery_address" required><br/><br/>

    <label for="delivery_suburb">Suburb:</label>
    <input type="text" name="delivery_suburb" id="delivery_suburb" required><br/><br/>

    <label for="delivery_state">State:</label>
    <select name="delivery_state" id="delivery_state" required>
        <option value="" disabled selected>Select State</option>
        <option value="NSW">NSW</option>
        <option value="VIC">VIC</option>
        <option value="QLD">QLD</option>
        <option value="WA">WA</option>
        <option value="SA">SA</option>
        <option value="TAS">TAS</option>
        <option value="ACT">ACT</option>
        <option value="NT">NT</option>
    </select><br/><br/>
    </div>

    <input type="submit" name="request" value="Request">
</form>
<?php
$DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s105299366", "080798", "s105299366_db")
Or die("<p>Unable to connect to the database server.</p>");

// Check if customer number is passed as a parameter
if (isset($_GET['customer_number'])) {
    $customerNumber = $_GET['customer_number'];
} else {
    die("<p>Customer number is missing. Please log in again.</p>");
}

if (isset($_POST['request'])) {
    $itemDescription = $_POST['item_description'];
    $weight = $_POST['weight'];
    $pickupAddress = $_POST['pickup_address'];
    $pickupSuburb = $_POST['pickup_suburb'];
    $pickupDay = $_POST['pickup_day'];
    $pickupMonth = $_POST['pickup_month'];
    $pickupYear = $_POST['pickup_year'];
    $pickupHour = $_POST['pickup_hour'];
    $pickupMinute = $_POST['pickup_minute'];
    $preferredPickupDate = "$pickupYear-$pickupMonth-$pickupDay";
    $preferredPickupTime = "$pickupHour:$pickupMinute:00";
    $receiverName = $_POST['receiver_name'];
    $deliveryAddress = $_POST['delivery_address'];
    $deliverySuburb = $_POST['delivery_suburb'];
    $deliveryState = $_POST['delivery_state'];

    // field check validations
    if (!$itemDescription || !$weight || !$pickupAddress || !$pickupSuburb || !$preferredPickupDate|| !$preferredPickupTime || !$receiverName || !$deliveryAddress || !$deliverySuburb || !$deliveryState) {
        $errors[] = "All fields are required.";
    }

    // Validation checks
    $currentDateTime = new DateTime();
    $preferredPickupDateTime = new DateTime("$preferredPickupDate $preferredPickupTime");

    if ($preferredPickupDateTime < $currentDateTime->modify('+24 hours')) {
        echo "<p style='color:red;'>Preferred pickup date and time must be at least 24 hours from now.</p>";
    } else {
        // Calculate the cost
        $cost = 20 + (($weight - 2) * 3);

        $requestDate = date('Y-m-d H:i:s');

        // Insert request into the database
        $SQLstring = "INSERT INTO requests (customer_number, request_date,  item_description, weight, pickup_address, 
                    pickup_suburb, preferred_pickup_date, preferred_pickup_time, receiver_name, 
                    delivery_address, delivery_suburb, delivery_state)
                    VALUES ('$customerNumber', '$requestDate', '$itemDescription', '$weight', '$pickupAddress', 
                    '$pickupSuburb', '$preferredPickupDate', '$preferredPickupTime', '$receiverName', 
                    '$deliveryAddress', '$deliverySuburb', '$deliveryState')";

        $queryResult = @mysqli_query($DBConnect, $SQLstring);
        if ($queryResult) {
            $requestNumber = mysqli_insert_id($DBConnect);  // Get the inserted request number

            // Retrieve customer details
            $customerSQL = "SELECT name, email FROM customers WHERE customer_number='$customerNumber'";
            $customerResult = @mysqli_query($DBConnect, $customerSQL);
            $customerRow = mysqli_fetch_assoc($customerResult);
            $customerName = $customerRow['name'];
            $customerEmail = $customerRow['email'];

            // Prepare email
            $to = $customerEmail;
            $subject = "Shipping request with ShipOnline";
            $message = "Dear $customerName, Thank you for using ShipOnline! Your request number is $requestNumber. The cost is $$cost. We will pick-up the item at $preferredPickupTime on $preferredPickupDate.";
            $headers = "From: 105299366@student.swin.edu.au";

            // Send email
            if (mail($to, $subject, $message, $headers, "-r 105299366@student.swin.edu.au")) {
                echo "<p>Thank you! Your request number is $requestNumber. The cost is $$cost. We will pick-up the item at $preferredPickupTime on $preferredPickupDate.</p>";
            } else {
                echo "<p>Error: Unable to send the confirmation email. Please try again later.</p>";
            }
        } else {
            echo "<p>Error: Unable to submit the request. Please try again later.</p>";
        }
    }
}
?>

</body>
</html>
