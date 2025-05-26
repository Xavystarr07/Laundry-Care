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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['hotelName'])) {
        // Store the selected hotel in session
        $_SESSION['hotelName'] = $_POST['hotelName'];
        $_SESSION['action'] = $_POST['action'];  // To distinguish between views
    } else {
        // Action for monthly statement without hotel selection
        $_SESSION['action'] = $_POST['action'];
    }

    // Redirect to the same page to display the selected hotel's statement
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Check if hotel is selected and action is set
if (isset($_SESSION['action'])) {
    $action = $_SESSION['action'];

    // SQL for unit totals (Statement view) - when a hotel is selected
    if ($action == "viewStatement" && isset($_SESSION['hotelName'])) {
        $hotelName = $_SESSION['hotelName'];
        $sql = "SELECT HotelNumber, SUM(Total) AS UnitTotal
                FROM Invoices 
                WHERE HotelName = '$hotelName'
                GROUP BY HotelNumber
                ORDER BY CAST(HotelNumber AS UNSIGNED) ASC"; // Ensure numeric sorting

        $result = $conn->query($sql);

        $monthlyStatement = array();
        $grandTotalSum = 0;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $monthlyStatement[] = array(
                    "HotelNumber" => $row["HotelNumber"],
                    "UnitTotal" => $row["UnitTotal"]
                );
                $grandTotalSum += $row["UnitTotal"]; // Sum the grouped totals for the grand total
            }
        } else {
            echo "<script>alert('No invoices found for the selected hotel.');</script>";
        }
    }

    // SQL for monthly grand total (Second view: Monthly Statement) - for all hotels
    if ($action == "monthlyStatement") {
        $sql = "SELECT HotelName, SUM(Total) AS GrandTotal
                FROM Invoices 
                GROUP BY HotelName";

        $result = $conn->query($sql);

        $hotelStatements = array();
        $grandTotalSum = 0;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $hotelStatements[] = array(
                    "HotelName" => $row["HotelName"],
                    "GrandTotal" => $row["GrandTotal"]
                );
                $grandTotalSum += $row["GrandTotal"]; // Sum for the overall grand total
            }
        } else {
            echo "<script>alert('No invoices found for any hotels.');</script>";
        }
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
        <select name="hotelName" id="hotelName">
            <option value="">-- Hotel --</option>
            <option value="Beacon-Rock">Beacon-Rock</option>
            <option value="Bensiesta">Bensiesta</option>
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

        <button type="submit" name="action" value="viewStatement" class="small-green-btn">Unit Statement</button>
        <button type="submit" name="action" value="monthlyStatement" class="small-green-btn">Monthly Statement</button><br></br>
			

    <input list="dateOptions" id="statementDate" name="statementDate" placeholder="Enter date">
    <datalist id="dateOptions" >
        <option value="Jan-Feb 2025">
        <option value="Feb-Mar 2025">
        <option value="Mar-Apr 2025">
        <option value="Apr-May 2025">
        <option value="May-Jun 2025">
        <option value="Jun-Jul 2025">
        <option value="Jul-Aug 2025">
        <option value="Aug-Sep 2025">
        <option value="Sep-Oct 2025">
        <option value="Oct-Nov 2025">
        <option value="Nov-Dec 2025">
        <option value="Dec-Jan 2025">
        <option value="Jan-Feb 2026">
        <option value="Feb-Mar 2026">
        <option value="Mar-Apr 2026">
        <option value="Apr-May 2026">
        <option value="May-Jun 2026">
        <option value="Jun-Jul 2026">
        <option value="Jul-Aug 2026">
        <option value="Aug-Sep 2026">
        <option value="Sep-Oct 2026">
        <option value="Oct-Nov 2026">
        <option value="Nov-Dec 2026">
        <option value="Dec-Jan 2026">
        <option value="Jan-Feb 2027">
        <option value="Feb-Mar 2027">
        <option value="Mar-Apr 2027">
        <option value="Apr-May 2027">
        <option value="May-Jun 2027">
        <option value="Jun-Jul 2027">
        <option value="Jul-Aug 2027">
        <option value="Aug-Sep 2027">
        <option value="Sep-Oct 2027">
        <option value="Oct-Nov 2027">
        <option value="Nov-Dec 2027">
        <option value="Dec-Jan 2027">
        <option value="Jan-Feb 2028">
        <option value="Feb-Mar 2028">
        <option value="Mar-Apr 2028">
        <option value="Apr-May 2028">
        <option value="May-Jun 2028">
        <option value="Jun-Jul 2028">
        <option value="Jul-Aug 2028">
        <option value="Aug-Sep 2028">
        <option value="Sep-Oct 2028">
        <option value="Oct-Nov 2028">
        <option value="Nov-Dec 2028">
        <option value="Dec-Jan 2028">
        <option value="Jan-Feb 2029">
        <option value="Feb-Mar 2029">
        <option value="Mar-Apr 2029">
        <option value="Apr-May 2029">
        <option value="May-Jun 2029">
        <option value="Jun-Jul 2029">
        <option value="Jul-Aug 2029">
        <option value="Aug-Sep 2029">
        <option value="Sep-Oct 2029">
        <option value="Oct-Nov 2029">
        <option value="Nov-Dec 2029">
        <option value="Dec-Jan 2029">
        <option value="Jan-Feb 2030">
        <option value="Feb-Mar 2030">
        <option value="Mar-Apr 2030">
        <option value="Apr-May 2030">
        <option value="May-Jun 2030">
        <option value="Jun-Jul 2030">
        <option value="Jul-Aug 2030">
        <option value="Aug-Sep 2030">
        <option value="Sep-Oct 2030">
        <option value="Oct-Nov 2030">
        <option value="Nov-Dec 2030">
        <option value="Dec-Jan 2030">
    </datalist>
</div>
</div>

</select>

    </form>

    <?php if (isset($action) && $action == "viewStatement" && isset($hotelName)) : ?>
        <table id="statementTable">
            <thead>
                <tr>
                    <th>Hotel Name</th>
                    <th>Hotel Unit</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($monthlyStatement)) {
                    foreach ($monthlyStatement as $invoice) {
                        echo "<tr>";
                        echo "<td>" . $hotelName . "</td>"; // Display the hotel name
                        echo "<td>" . $invoice["HotelNumber"] . "</td>";
                        echo "<td>R " . number_format($invoice["UnitTotal"], 2) . "</td>";
                        echo "</tr>";
                    }

                    // Display Grand Total at the bottom
                    echo "<tr class='grand-total-row'>";
                    echo "<td><strong>Grand Total</strong></td>";
                    echo "<td></td>"; // Empty column for hotel unit
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
                    echo "<tr><td colspan='3'>No invoices found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
<form method="post" action="" target="_blank">
    <input type="button" value="Print Monthly Statement" onclick="window.print();" class="print-btn">
</form>
<?php endif; ?>

<?php if (isset($action) && $action == "monthlyStatement") : ?>
    <table id="statementTable">
        <thead>
            <tr>
                <th>Hotel Name</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($hotelStatements)) {
                foreach ($hotelStatements as $statement) {
                    echo "<tr>";
                    echo "<td>" . $statement["HotelName"] . "</td>";
                    echo "<td>R " . number_format($statement["GrandTotal"], 2) . "</td>";
                    echo "</tr>";
                }

            // Standard Fuel Cost Row
echo '<tr id="fuelCostRow">';
echo '<td style="text-align: left;"><strong>Standard Fuel Levy</strong></td>';
echo '<td><span>R</span> <input type="number" id="fuelCost" value="2871.50" step="0.01" onchange="updateGrandTotal()" style="width: 85%; padding: 5px; text-align: left; border: none; font-weight: normal; font-size: inherit; font-family: inherit;" /></td>';
echo '</tr>';



                // Display Overall Grand Total (initial PHP-calculated)
                echo "<tr class='grand-total-row'>";
                echo "<td><strong>Grand Total</strong></td>";
                echo "<td id='grandTotalCell'><strong>R " . number_format($grandTotalSum, 2) . "</strong></td>";
                echo "</tr>";
                
                // Display Banking Details
                echo "<tr>";
                echo "<td colspan='2' style='padding-top: 20px; text-align: center;'><strong>Banking Details</strong></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='2' style='text-align: center;'>Bank: FNB</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='2' style='text-align: center;'>Account Holder Name: Laundry Care</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='2' style='text-align: center;'>Account Number: 62936500520</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td colspan='2' style='text-align: center;'>Account Type: Cheque</td>";
                echo "</tr>";
            } else {
                echo "<tr><td colspan='2'>No invoices found for any hotels.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <form method="post" action="" target="_blank">
        <input type="button" value="Print Monthly Statement" onclick="window.print();" class="print-btn">
    </form>

    <script>
    function updateGrandTotal() {
        let fuelCost = parseFloat(document.getElementById('fuelCost').value) || 0;
        let baseTotal = <?php echo $grandTotalSum; ?>;
        let updatedTotal = baseTotal + fuelCost;

        document.getElementById("grandTotalCell").innerHTML = "<strong>R " + updatedTotal.toFixed(2) + "</strong>";
    }

    window.onload = updateGrandTotal;
    </script>
<?php endif; ?>


    <!-- Back Button -->
    <button class="back-button" onclick="window.location.href='invoice_generator.php'">← Back to Invoice Generator</button>
</body>
</html>
