<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "LC";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle hotel selection form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hotelName'])) {
    // Store the selected hotel and dates in session
    $_SESSION['hotelName'] = $_POST['hotelName'];
    $_SESSION['startDate'] = $_POST['startDate'];
    $_SESSION['endDate'] = $_POST['endDate'];

    // Redirect to the same page to display the selected hotel's statement
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Check if hotel and dates are selected
if (isset($_SESSION['hotelName']) && isset($_SESSION['startDate']) && isset($_SESSION['endDate'])) {
    $hotelName = $_SESSION['hotelName'];
    $startDate = $_SESSION['startDate'];
    $endDate = $_SESSION['endDate'];

    // Fetch invoices for the selected hotel within the date range
    $sql = "SELECT * FROM Invoices WHERE HotelName = '$hotelName' AND DateReceived BETWEEN '$startDate' AND '$endDate' ORDER BY DateReceived ASC";

    $result = $conn->query($sql);

    $monthlyStatement = array();
    $grandTotalSum = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $monthlyStatement[] = array(
                "HotelNumber" => $row["HotelNumber"],
                "HotelName" => $row["HotelName"],
                "DateReceived" => $row["DateReceived"],
                "GrandTotal" => $row["Total"] // Use the Total column here
            );
            $grandTotalSum += $row["Total"]; // Sum the 'Total' column for grand total
        }
    } else {
        echo "<script>alert('No invoices found for the selected hotel within the given date range.');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Statement</title>
    <link rel="stylesheet" href="monthly_statement.css">
    <style>
        .header-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
        }
        .business-info {
            flex: 1;
            text-align: center;
        }
        .contact-info {
            flex: 1;
            text-align: right;
            margin-bottom: 100px;
        }
        .recipient-info {
            text-align: left;
            margin-top: 50px;
        }
        .tagline {
            text-align: center;
            font-style: italic;
            margin-top: 10px;
            font-weight: bold;
        }
        .statement-title {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header-info">
        <div class="business-info">
            <h1>Laundry Care</h1>
            <p>2 Tottenham Road</p>
            <p>Parkgate</p>
            <p>Cornubia</p>
        </div>
    </div>
    <div class="contact-info">
        <p>Email: donovanmark14@gmail.com</p>
        <p>Cell: 062 283 9374</p>
        <p>Cell: 069 780 0340</p>
    </div>
    <div class="recipient-info">
        <p><strong>To:</strong> Umhlanga Accommodation,</p>
        <p>Penny Underwood</p>
        <p>Shop 14, Chartwell Centre</p>
        <p>Umhlanga Rocks</p>
    </div>

    <h2 class="statement-title"><u>Monthly Statement</u></h2>
    <p class="tagline">“A FRESH START IN EVERY WASH”</p>

    <!-- Hotel Selection Form -->
    <form method="post">
        <label for="hotelName">Select Hotel:</label>
        <select name="hotelName" id="hotelName" required>
            <option value="">-- Hotel --</option>
            <option value="Beacon-Rock">Beacon-Rock</option>
            <option value="Beniseta">Beniseta</option>
            <option value="Berumdas">Bermudas</option>
            <option value="Bronze Bay">Bronze Bay</option>
            <option value="Bronze Beach">Bronze Beach</option>
            <option value="Breakers">Breakers</option>
            <option value="Cormoran">Cormoran</option>
            <option value="Glitter Bay">Glitter Bay</option>
            <option value="Kyalanga">Kyalanga</option>
            <option value="Malindi">Malindi</option>
            <option value="Marine">Marine</option>
            <option value="Oyster Rock">Oyster Rock</option>
            <option value="Pearls">Pearls</option>
            <option value="Sea Lodge">Sea Lodge</option>
            <option value="Sea Breeze">Sea Breeze</option>
            <option value="Shades">Shades</option>
            <option value="Terra Mare">Terra Mare</option>
        </select>

        <label for="startDate">Start Date:</label>
        <input type="date" name="startDate" id="startDate" required class="select">

        <label for="endDate">End Date:</label>
        <input type="date" name="endDate" id="endDate" required class="select">
        <button type="submit" class="small-green-btn">View Statement</button>
    </form>

    <?php if (isset($hotelName)) : ?>
        <table id="statementTable">
            <thead>
                <tr>
                    <th>Hotel Number</th>
                    <th>Hotel Name</th>
                    <th>Date Received</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($monthlyStatement)) {
                    foreach ($monthlyStatement as $invoice) {
                        echo "<tr>";
                        echo "<td>" . $invoice["HotelNumber"] . "</td>";
                        echo "<td>" . $invoice["HotelName"] . "</td>";
                        echo "<td>" . $invoice["DateReceived"] . "</td>";
                        echo "<td>R " . number_format($invoice["GrandTotal"], 2) . "</td>";
                        echo "</tr>";
                    }
                    // Display Grand Total at the bottom
                    echo "<tr class='grand-total-row'>";
                    echo "<td colspan='3'><strong>Grand Total</strong></td>";
                    echo "<td><strong>R " . number_format($grandTotalSum, 2) . "</strong></td>";
                    echo "</tr>";
                    
                    // Display Banking Details
                    echo "<tr>";
                    echo "<td colspan='4' style='padding-top: 20px; text-align: center;'><strong>Banking Details</strong></td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td colspan='4' style='text-align: center;'>Bank: FNB</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td colspan='4' style='text-align: center;'>Account Holder Name: Laundry Care</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td colspan='4' style='text-align: center;'>Account Number: 62936500520</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td colspan='4' style='text-align: center;'>Account Type: Cheque</td>";
                    echo "</tr>";

                } else {
                    echo "<tr><td colspan='4'>No invoices found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <form method="post" action="" target="_blank">
            <input type="button" value="Print Monthly Statement" onclick="window.print();" class="print-btn">
        </form>
    <?php endif; ?>

    <!-- Back Button -->
    <button class="back-button" onclick="window.location.href='invoice_generator.php'">← Back to Invoice Generator</button>
</body>
</html>
