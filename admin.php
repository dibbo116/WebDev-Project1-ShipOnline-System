<!-- Student Id : 105299366 , Name : Dibbo Barua Chamak -->
<html>
<head>
    <title>ShipOnline System Administration Page</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td {
            background-color: #ffffff;
        }
    </style>
</head>
<body style="background-color: yellow;">
<h2>ShipOnline System Administration Page</h2>
<!-- form submission -->
    <form method="post" style="border: 2px solid black; padding: 20px; width: 95%;">
            <label for="date">Date For Retrieve:</label>
            <select name="day" id="day" required>
            <option value="" disabled selected>Day</option>
                <?php for ($i = 1; $i <= 31; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                } ?>
            </select>
            <select name="month" id="month" required>
            <option value="" disabled selected>Month</option>
                <?php for ($i = 1; $i <= 12; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                } ?>
            </select>
            <select name="year" id="year" required>
            <option value="" disabled selected>Year</option>
                <?php for ($i = date('Y'); $i <= date('Y') + 1; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                } ?>
            </select><br/><br/>
            <label for="dateItem">Select Date Item for Retrieve</label>
            <input type="radio" id="request_date" name="date_type" value="request_date" required>
            <label for="request_date">Request Date</label>
            <input type="radio" id="pickup_date" name="date_type" value="pickup_date">
            <label for="pickup_date">Pick-Up Date</label><br/><br/>

            <input type="submit" value="Show">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $dateType = $_POST['date_type'];
        $year = $_POST['year'];
        $month = $_POST['month'];
        $day = $_POST['day'];

        $selectedDate = "$year-$month-$day";
        
        // Database connection
        $DBConnect = @mysqli_connect("feenix-mariadb.swin.edu.au", "s105299366", "080798", "s105299366_db")
        or die ("<p>Unable to connect to the database server.</p>" . "<p>Error code " .
        mysqli_connect_errno() . ": " . mysqli_connect_error() . "</p>");

        if ($dateType == 'request_date') {
            // Query to retrieve requests by request date
            $SQLstring = "SELECT customer_number, request_number, item_description, weight, pickup_suburb, 
                          DATE(preferred_pickup_date) AS preferred_pickup_date, delivery_suburb, delivery_state 
                          FROM requests 
                          WHERE DATE(request_date) = '$selectedDate'";
            
            $queryResult = @mysqli_query($DBConnect, $SQLstring);

            if ($queryResult) {
                $totalRequests = 0;
                $totalCost = 0;
                
                echo "<h3>Requests for $selectedDate</h3>";
                echo "<table>";
                echo "<tr><th>Customer Number</th><th>Request Number</th><th>Item Description</th><th>Weight</th><th>Pick-Up Suburb</th><th>Preferred Pick-Up Date</th><th>Delivery Suburb</th><th>Delivery State</th></tr>";

                while ($row = mysqli_fetch_assoc($queryResult)) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>$cell</td>";
                    }
                    echo "</tr>";
                    $totalRequests++;
                    $totalCost += ($row['weight'] <= 2) ? 20 : 20 + ($row['weight'] - 2) * 3;
                }
                echo "</table>";
                
                echo "<p>Total Requests: $totalRequests</p>";
                echo "<p>Total Revenue: $totalCost</p>";
            } else {
                echo "<p>No requests found for $selectedDate.</p>";
            }
        } else {
            // Query to retrieve requests by pick-up date with customer details
            $SQLstring = "SELECT r.customer_number, c.name AS customer_name, c.phone_number, r.request_number, r.item_description, r.weight, r.pickup_address, r.pickup_suburb, r.preferred_pickup_date, r.preferred_pickup_time, r.delivery_suburb, r.delivery_state 
                          FROM requests r 
                          JOIN customers c ON r.customer_number = c.customer_number 
                          WHERE DATE(r.preferred_pickup_date) = '$selectedDate' 
                          ORDER BY r.pickup_suburb, r.delivery_state, r.delivery_suburb";

            $queryResult = @mysqli_query($DBConnect, $SQLstring);

            if ($queryResult) {
                $totalRequests = 0;
                $totalWeight = 0;
                
                echo "<h3>Requests for $selectedDate</h3>";
                echo "<table>";
                echo "<tr><th>Customer Number</th><th>Customer Name</th><th>Contact Phone</th><th>Request Number</th><th>Item Description</th><th>Weight</th><th>Pick-Up Address</th><th>Pick-Up Suburb</th><th>Preferred Pick-Up Date</th><th>Preferred Pick-Up Time</th><th>Delivery Suburb</th><th>Delivery State</th></tr>";

                while ($row = mysqli_fetch_assoc($queryResult)) {
                    echo "<tr>";
                    foreach ($row as $cell) {
                        echo "<td>$cell</td>";
                    }
                    echo "</tr>";
                    $totalRequests++;
                    $totalWeight += $row['weight'];
                }
                echo "</table>";
                
                echo "<p>Total Requests: $totalRequests</p>";
                echo "<p>Total Weight:  $totalWeight kg</p>";
            } else {
                echo "<p>No requests found for $selectedDate.</p>";
            }
        }
        mysqli_close($DBConnect);
    }
    ?>
    <br>
    <a href="shiponline.php">Home</a>
</body>
</html>
