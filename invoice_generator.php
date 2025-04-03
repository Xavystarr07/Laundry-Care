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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data safely using filter_input
    $invoiceNumber = filter_input(INPUT_POST, 'invoiceNumber', FILTER_SANITIZE_NUMBER_INT);
	if (!is_numeric($invoiceNumber) || $invoiceNumber <= 0) {
    die("Error: Invalid Invoice Number");
}

    $hotelName = filter_input(INPUT_POST, 'hotel', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $hotelNumber = filter_input(INPUT_POST, 'unit-number', FILTER_SANITIZE_STRING);
    $dateReceived = $_POST['date-received'] ?? '';
    $grandTotal = filter_input(INPUT_POST, 'grandTotal', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Initialize error messages
    $missingFields = [];

    // Check for missing fields
    if (empty($invoiceNumber)) {
        $missingFields[] = "Invoice Number";
    }
    if (empty($hotelName)) {
        $missingFields[] = "Hotel Name";
    }
    if (empty($hotelNumber)) {
        $missingFields[] = "Unit Number";
    }
    if (empty($dateReceived)) {
        $missingFields[] = "Date Received";
		
		if (!isset($_POST['date-received'])) {
    die("Error: Date Received field is not being sent.");
}

    }
    if (empty($grandTotal)) {
        $missingFields[] = "Grand Total";
    }

    if (!empty($missingFields) && is_array($missingFields)) {
    die("Error: The following fields are missing: " . implode(", ", $missingFields));
}


    // Use prepared statement to insert data
    $sql = "INSERT INTO Invoices (InvoiceNumber, HotelName, HotelNumber, DateReceived, Total) 
            VALUES (?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the prepared statement
        $stmt->bind_param("isssd", $invoiceNumber, $hotelName, $hotelNumber, $dateReceived, $grandTotal);

        if ($stmt->execute()) {
            // Store session data
			$_SESSION['invoiceNumber'] = $invoiceNumber;
            $_SESSION['hotelName'] = $hotelName;
            $_SESSION['hotelNumber'] = $hotelNumber;
            $_SESSION['dateReceived'] = $dateReceived;
            $_SESSION['grandTotal'] = $grandTotal;

        } else {
            echo "Error inserting data: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "SQL Error: " . $conn->error;
    }
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="inv.css"> 
    <title>Laundry Care Invoice</title>
 
</head>
<body>
    <h1>Laundry Care</h1>
	
	 <!-- <div class="search-container">
    <input type="text" id="searchBar" placeholder="Search by code or description..." onkeyup="searchItem()">
 
    <div id="searchResults"></div>
</div> -->
    
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <table>
	<h2>Invoice Number <span id="invoiceNumber"></span> <input type="number" name="invoiceNumber" class="invoice-input" required /></h2>
    <thead>
        <tr>
            <th>Hotel</th>
            <th>Unit Number</th>
            <th>Code</th>
            <th>Description</th>
            <th>Unit Price (R)</th>
            <th>Quantity</th>
            <th>Total Price (R)</th>
			<th>Date Received</th>
        </tr>
    </thead>
    <tbody id="invoiceTable">
        <tr>
            <td>
                <select class="hotel" name="hotel">
                    <option value="Beacon-Rock">Beacon-Rock</option>
<option value="Beniseta">Bensieta</option>
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
            </td>
            <td>
                <input type="number" name="unit-number" class="unit-number" placeholder="Unit Number">
            </td>
            <td>
                <input type="text" class="code" oninput="suggestCode(this)" list="suggestions">
                <datalist id="suggestions"></datalist>
            </td>
            <td><input type="text" name="description" class="description" readonly></td>
            <td><input type="number" name="price" class="price" readonly></td>
            <td><input type="number" name="quantity" class="quantity" oninput="calculateTotal(this)"></td>
            <td><input type="number" name="total" class="total" readonly></td>
			<td><input type="date" name="date-received" class="date-received" required value="<?php echo date('Y-m-d'); ?>"></td>
        </tr>
    </tbody>
</table>

    <button onclick="addRow()" class="add-item">Add Item</button>
<button onclick="removeRow()" class="remove-item" id ="removeRowBtn">Remove Item</button>
<button onclick="printInvoice()" class="save">Print</button>
<button type="button" onclick="addCustomItem()" class="custom-item">Custom</button>


<button class="monthly-statements-btn" onclick="navigateToMonthlyStatement()">Monthly Statements</button>

</div>

<input type="submit" name="save_invoice" value="Save Invoice" class="submit-btn">


<h3>Grand Total: R<span id="grandTotal">0.00</span></h3>
<input type="hidden" name="grandTotal" id="grandTotalInput" value="0.00">


</form>
    <script>
         const priceList = [
    { code: "BATHMATREC", description: "Rectangular Bathmat", price: 17.34 },
	{ code: "MATTOI", description: "Toilet Mat", price: 20.00 },
	{ code: "MATTSC", description: "Toilet Seat Cover", price: 20.00 },
	{ code: "BASE", description: "Base cover", price: 15.00 },
	{ code: "TWLHAND", description: "Hand Towel", price: 15.00 },
    { code: "BATHTWL", description: "Bath Towel", price: 20.00 },
	{ code: "BATHSH", description: "Bath Sheet", price: 30.00 },
	{ code: "BATHMA", description: "Bath Math", price: 25.00 },  
	{ code: "BLANKK", description: "King Blanket", price: 121.26 },
    { code: "BLANKQ", description: "Queen Blanket", price: 121.26 },
	{ code: "BLANKDBL", description: "Double Blanket", price: 65.54 },
    { code: "CCOV", description: "Cushion Covers", price: 18.48 },
	{ code: "COMFKS", description: "King Comforter", price: 114.34 },
	{ code: "COMFQUE", description: "Queen Comforter", price: 114.34 },
    { code: "COMFDBL", description: "Double Comforter", price: 114.34 },
	{ code: "COMFSI", description: "Single Comforter", price: 56.34 },
    { code: "CURTDRP", description: "Curtains (Per Drop)", price: 103.90 },
    { code: "DISCLT", description: "Dish Cloth", price: 4.77 },
    { code: "PILLCASE", description: "Pillowcase", price: 8.50 },
    
    { code: "BDFR", description: "Bed Frill", price: 30.52 },
    { code: "BEATWL", description: "Beach Towel", price: 10.42 },
	{ code: "CARSML", description: "Small Carpet", price: 50.00 },
    { code: "BEDMAT", description: "Bedside Mat", price: 69.30 },
    { code: "BLANKS", description: "Single Blanket", price: 60.00 },
    { code: "BSQ", description: "Bed Spread-Quilt", price: 121.26 },
	
    { code: "CCOVLRG", description: "Cushion Covers/Large", price: 43.29 },
    { code: "CCOVSML", description: "Cushion Cover-Small", price: 38.10 },
    
    { code: "CONTPCASE", description: "Continental Pillowcase", price: 10.00 },
    { code: "CONTPLW", description: "Continental Pillow", price: 86.58 },
    { code: "COUSCOVLRG", description: "Large Cushion Cover", price: 72.73 },
    { code: "COUSCOVMED", description: "Medium Cushion Cover", price: 26.01 },
    { code: "COUSCOVS", description: "Small Cushion Covers", price: 20.84 },
    { code: "DBLDUVFEA", description: "Duvet Double Feather", price: 112.59 },
    { code: "DBLTHW", description: "Double Throw", price: 95.25 },
    { code: "DELCOL", description: "Delivery/Collection Fee", price: 0.00 },
    { code: "DUVCOVKS", description: "King-size Duvet Cover", price: 21.71 },
    { code: "DUVCOVQU", description: "Queen Duvet Cover", price: 14.92 },
	
	
	{ code: "DUVCOVDO", description: "Double Duvet Cover", price: 14.92 },
	{ code: "DUVCOV3/4", description: "3/4 Duvet Cover", price: 14.92 },
	{ code: "DUVCOVSI", description: "Single Duvet Cover", price: 14.92 },
    { code: "DUVDBLINN", description: "Double Duvet Inner", price: 103.90 },
    { code: "FACCLTH", description: "Face Cloth", price: 2.82 },
    { code: "FREJAC", description: "Freezer Jackets", price: 83.80 },
    { code: "GLASCLO", description: "Glass Cloth", price: 6.12 },
    { code: "GOWN/W/B", description: "Gowns with Belt", price: 28.90 },
    { code: "KSDUVINN/F", description: "King Duvet Inner-Feather", price: 129.86 },
    { code: "KSDUVINN/N", description: "King Duvet Inner-Normal", price: 95.25 },
    { code: "KSFITSHT", description: "King Fitted Sheet", price: 15.59 },
    { code: "KSFLATSHT", description: "King Flat Sheet", price: 15.59 },
    { code: "KSMATPRO", description: "King Mattress Protector", price: 60.63 },
    { code: "KSQUIL", description: "King Quilt", price: 147.21 },
	 { code: "POOLCUS", description: "Pool Cushion Cover", price: 40.00 },
    { code: "MATCOV", description: "Mattress Cover", price: 22.38 },
    { code: "MATLRG", description: "Large Rectangular Mat", price: 34.68 },
    { code: "MATPRO/K/Q", description: "Mattress Protector (K/Q)", price: 112.59 },
	   { code: "MATPRODO", description: "Double Mattress Protector", price: 51.96 },
	{ code: "MATPRO3/4", description: "Mattress Protector (3/4)", price: 20.00 },
    { code: "MATPROS", description: "Mattress Protector (Single)", price: 43.29 },
    { code: "MATREC", description: "Rectangular Mats", price: 17.34 },
    { code: "MATUSHP", description: "U Shaped Toilet Mat", price: 6.92 },
	
    { code: "NAPCLOTH", description: "Napkin Cloth", price: 2.62 },
	 { code: "NGTFRILKS", description: "Night Frill King", price: 65.00 },
	  { code: "NGTFRILQS", description: "Night Frill Queen", price: 60.00 },
	 { code: "NGTFRILDO", description: "Night Frill Double", price: 19.49 },
	 { code: "NGTFRIL3/4", description: "Night Frill 3/4", price: 19.49 }, 
	{ code: "NGTFRILSI", description: "Night Frill Single", price: 19.49 },
    { code: "OVENGLO", description: "Oven Gloves", price: 17.34 },
    { code: "PILLPRO", description: "Continental Pillow Protector", price: 26.01 },
    { code: "PILLPROSTD", description: "Standard Pillow Protector", price: 8.67 },
    { code: "PILLSTD", description: "Standard Pillow", price: 81.94 },
    { code: "PLCEMAT", description: "Place Mats", price: 5.78 },
    { code: "PLWCASLRG", description: "Large Pillowcases", price: 11.29 },
    { code: "QSFITSHT", description: "Queen Fitted Sheet", price: 10.00 },
	{ code: "DOFITSHT", description: "Double Fitted Sheet", price: 10.00 },
	{ code: "DOFLASHT", description: "Double Flat Sheet", price: 10.00 },
	{ code: "SIFITSHT", description: "Single Fitted Sheet", price: 10.00 },
	{ code: "3/4FITSHT", description: "3/4 Fitted Sheet", price: 10.00 },
	{ code: "SIFLASHT", description: "Single Flat Sheet", price: 10.00 },
    { code: "QSFLATSHT", description: "Queen Flat Sheet", price: 15.59 },
    { code: "RWASH", description: "Re-Wash", price: 0.00 },
	{ code: "RUGLRG", description: "Large Rug", price: 45.00 },
	{ code: "RUGSTD", description: " Standard Rug", price: 35.00 },
    { code: "SHOWCURT", description: "Shower Curtain", price: 38.10 },
    { code: "SINGDUVINN", description: "Single Duvet Inner", price: 85.77 },
    { code: "SINGLETHRW", description: "Single Throw", price: 57.13 },
    { code: "STAINTRT", description: "Stain Treatment", price: 0.00 },
    { code: "TBLCLOTH", description: "Tablecloth", price: 17.34 },
    { code: "TBLRUN", description: "Table Runner", price: 17.34 },
    { code: "THROW/A/S", description: "Throws-All Sizes", price: 86.58 },
    { code: "TURNMAT", description: "Turndown Mats", price: 4.71 },
    
    { code: "TWLTEA", description: "Tea Towel", price: 4.77 }
];

function searchItem() {
    let query = document.getElementById("searchBar").value.toLowerCase();
    let resultsDiv = document.getElementById("searchResults");
    resultsDiv.innerHTML = ""; // Clear previous results

    if (query.trim() === "") {
        resultsDiv.style.display = "none";
        return;
    }

    let foundItem = priceList.find(item => 
        item.code.includes(query) || item.description.toLowerCase().includes(query)
    );

    resultsDiv.style.display = "block";

    if (foundItem) {
        resultsDiv.innerHTML = `<p><strong>${foundItem.description}</strong> - ${foundItem.price}</p>`;
    } else {
        resultsDiv.innerHTML = "<p style='color: red;'>Item not found</p>";
    }
} 


function navigateToMonthlyStatement() {
    
	 // Prevent the form from being submitted
    event.preventDefault();
    window.location.href = 'monthly_statement.php';
}

function addCustomItem() {
    const table = document.getElementById("invoiceTable");
    const newRow = table.insertRow();

    newRow.innerHTML = `
        <td>
            <select class="hotel">
                <option value="Beacon-Rock">Beacon-Rock</option>
                <option value="Beniseta">Bensieta</option>
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
        </td>
        <td><input type="number" name="customUnit" class="unit-number" placeholder="Unit Number"></td>
        <td><input type="text" name="customCode" class="code" placeholder="Custom Code"></td>
        <td><input type="text" name="customDescription" class="description" placeholder="Description"></td>
        <td><input type="number" name="price" class="price" placeholder="Unit Price" oninput="calculateCustomTotal(this)"></td>
        <td><input type="number" name="quantity" class="quantity" oninput="calculateCustomTotal(this)"></td>
        <td><input type="text" name="total" class="total" readonly></td>
        <td><input type="date" name="customDate" class="date-received" required value="<?php echo date('Y-m-d'); ?>"></td>
    `;

    // After adding a custom item, recalculate the grand total
    updateGrandTotal();
}

function calculateCustomTotal(input) {
    const row = input.closest("tr");
    const price = parseFloat(row.querySelector(".price").value) || 0;
    const quantity = parseInt(row.querySelector(".quantity").value) || 0;
    const total = (price * quantity).toFixed(2);
    row.querySelector(".total").value = total;

    // After calculating total for the custom item, update the grand total
    updateGrandTotal();
}

// Function to calculate and update the grand total
function updateGrandTotal() {
    let grandTotal = 0;

    // Loop through all rows in the invoice table and sum up the total values
    const rows = document.querySelectorAll("#invoiceTable tr");
    rows.forEach(row => {
        const totalCell = row.querySelector(".total");
        if (totalCell) {
            const totalValue = parseFloat(totalCell.value) || 0;
            grandTotal += totalValue;
        }
    });

    // Update the grand total display (assuming there's an element to show the grand total)
    const grandTotalElement = document.getElementById("grandTotal");
    if (grandTotalElement) {
        grandTotalElement.textContent = `Grand Total: $${grandTotal.toFixed(2)}`;
    }
}



function suggestCode(input) {
    let query = input.value.toUpperCase();
    let dropdownId = input.getAttribute("list");
    let dropdown = document.getElementById(dropdownId);
    
    dropdown.innerHTML = ''; // Reset dropdown

    let suggestions = priceList.filter(item => item.code.includes(query));

    suggestions.forEach(item => {
        let option = document.createElement('option');
        option.value = item.code;
        dropdown.appendChild(option);
    });

    input.addEventListener('change', function () {
        let selectedItem = priceList.find(item => item.code === input.value);
        if (selectedItem) {
            let row = input.closest('tr');
            row.querySelector('.description').value = selectedItem.description;
            row.querySelector('.price').value = selectedItem.price.toFixed(2);
        }
    });
}     

        // Function to calculate total for each item
        function calculateTotal(input) {
            let row = input.closest('tr');
            let price = parseFloat(row.querySelector('.price').value) || 0;
            let quantity = parseInt(input.value) || 0;
            let total = price * quantity;
            row.querySelector('.total').value = total.toFixed(2);
            calculateGrandTotal();
        }

function printInvoice() {
	
	 // Prevent the form from being submitted
    event.preventDefault();
	
  const rows = document.querySelectorAll('#invoiceTable tr');
  let allFilled = true;

  rows.forEach(row => {
    const hotelSelect = row.querySelector('.hotel');
    const unitNumberInput = row.querySelector('.unit-number');
    const codeInput = row.querySelector('.code');
    const descriptionInput = row.querySelector('.description');
    const priceInput = row.querySelector('.price');
    const quantityInput = row.querySelector('.quantity');
    const totalInput = row.querySelector('.total');
	const daterecieved = row.querySelector('.date-received');

    if (
      !hotelSelect.value.trim() 
      || !unitNumberInput.value.trim() 
      || !codeInput.value.trim() 
      || !descriptionInput.value.trim() 
      || !priceInput.value.trim() 
      || !quantityInput.value.trim() 
      || !totalInput.value.trim() 
	  || !daterecieved.value.trim()
    ) {
      allFilled = false;
      return; 
    }
  });

  if (!allFilled) {
    alert("Please fill in all missing inputs."); 
  } else {
    window.print(); 
  }
}
function updateGrandTotal() {
    let grandTotal = 0;

    // Loop through all rows in the invoice table and sum up the total values
    const rows = document.querySelectorAll("#invoiceTable tr");
    rows.forEach(row => {
        const totalCell = row.querySelector(".total");
        if (totalCell) {
            const total = parseFloat(totalCell.value) || 0; // Ensure no NaN
            grandTotal += total;
        }
    });

    // Update the grand total displayed on the page
    document.getElementById("grandTotal").textContent = grandTotal.toFixed(2);

    // Also update the hidden grand total input for form submission
    document.getElementById("grandTotalInput").value = grandTotal.toFixed(2);
}


// Call this function whenever an item's total is updated
document.querySelectorAll('.quantity').forEach(input => {
    input.addEventListener('input', updateGrandTotal);
});

function checkInputs() {
  const inputFields = document.querySelectorAll('input, select');
  let allFilled = true;

  inputFields.forEach(input => {
    // Check if the input value is empty or only contains whitespaces
    if (!input.value || /^\s*$/.test(input.value)) {
      if (input.hasAttribute('data-touched')) { // Check if the field has been touched
        input.classList.add('invalid');
      }
    } else {
      input.classList.remove('invalid');
    }
  });

  document.querySelector('.save').disabled = !allFilled;
}

// Add event listeners to all input fields
document.querySelectorAll('input, select').forEach(input => {
  input.addEventListener('focus', () => {
    input.setAttribute('data-touched', true); 
  });
  input.addEventListener('blur', checkInputs); 
});

let selectedRow = null; // Track the selected row

function selectRow(row) {
            if (selectedRow) {
                selectedRow.classList.remove('selected');
            }
            selectedRow = row;
            selectedRow.classList.add('selected');
        }
		
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("removeRowBtn").addEventListener("click", function (event) {
        event.preventDefault(); // Prevent page refresh
        removeRow();
    });
});

function removeRow() {
    let table = document.getElementById("invoiceTable");
    let rowCount = table.rows.length;

    // Ensure there is more than one row to delete
    if (rowCount <= 1) {
        alert("No rows to remove.");
        return;
    }

    let rowNumber = prompt(`Enter the row number to remove (2-${rowCount}):`);

    if (rowNumber !== null && rowNumber !== "") {
        let rowIndex = parseInt(rowNumber, 10);

        // Ensure input is a valid row number and does not remove the header row
        if (!isNaN(rowIndex) && rowIndex >= 2 && rowIndex <= rowCount) {
            table.deleteRow(rowIndex - 1); // Adjust for zero-based index
            calculateGrandTotal(); // Recalculate total after removing a row
        } else {
            alert("Invalid row number. Please enter a valid row number.");
        }
    }
}





document.querySelector(".add-item").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default button action

    let missingFields = [];

    let invoiceNumber = document.querySelector('input[name="invoiceNumber"]').value.trim();
    let hotel = document.querySelector('select[name="hotel"]').value.trim();
    let unitNumber = document.querySelector('input[name="unit-number"]').value.trim();
    let dateReceived = document.querySelector('input[name="date-received"]').value.trim();
    let grandTotal = document.querySelector('input[name="grandTotal"]').value.trim();

    if (!invoiceNumber) missingFields.push("Invoice Number");
    if (!hotel) missingFields.push("Hotel Name");
    if (!unitNumber) missingFields.push("Unit Number");
    if (!dateReceived) missingFields.push("Date Received");
    if (!grandTotal || parseFloat(grandTotal) <= 0) missingFields.push("Grand Total");

    if (missingFields.length > 0) {
        alert("Error: The following fields are missing:\n" + missingFields.join(", "));
    } else {
        console.log("All fields are filled correctly.");
        // Continue with adding item functionality if needed
    }
});

function addRow() {
  let table = document.getElementById("invoiceTable");
  let lastRow = table.rows[table.rows.length - 1];

  // Get previous values
  let prevHotel = lastRow ? lastRow.querySelector(".hotel").value : "";
  let prevUnit = lastRow ? lastRow.querySelector(".unit-number").value : "";
  let prevDate = lastRow ? lastRow.querySelector(".date-received").value : "";

  let newRow = table.insertRow();

  newRow.innerHTML = `
    <td>
      <select class="hotel">
        <option value="Beacon-Rock" ${prevHotel === "Beacon-Rock" ? "selected" : ""}>Beacon-Rock</option>
        <option value="Beniseta" ${prevHotel === "Beniseta" ? "selected" : ""}>Bensieta</option>
        <option value="Berumdas" ${prevHotel === "Berumdas" ? "selected" : ""}>Bermudas</option>
        <option value="Bronze Bay" ${prevHotel === "Bronze Bay" ? "selected" : ""}>Bronze Bay</option>
        <option value="Bronze Beach" ${prevHotel === "Bronze Beach" ? "selected" : ""}>Bronze Beach</option>
        <option value="Breakers" ${prevHotel === "Breakers" ? "selected" : ""}>Breakers</option>
        <option value="Cormoran" ${prevHotel === "Cormoran" ? "selected" : ""}>Cormoran</option>
        <option value="Glitter Bay" ${prevHotel === "Glitter Bay" ? "selected" : ""}>Glitter Bay</option>
        <option value="Kyalanga" ${prevHotel === "Kyalanga" ? "selected" : ""}>Kyalanga</option>
        <option value="Malindi" ${prevHotel === "Malindi" ? "selected" : ""}>Malindi</option>
        <option value="Marine" ${prevHotel === "Marine" ? "selected" : ""}>Marine</option>
        <option value="Oyster Rock" ${prevHotel === "Oyster Rock" ? "selected" : ""}>Oyster Rock</option>
        <option value="Pearls" ${prevHotel === "Pearls" ? "selected" : ""}>Pearls</option>
        <option value="Sea Lodge" ${prevHotel === "Sea Lodge" ? "selected" : ""}>Sea Lodge</option>
		 <option value="Shades" ${prevHotel === "Shades" ? "selected" : ""}>Shades</option> 
		 <option value="Sea Breeze" ${prevHotel === "Sea Breeze" ? "selected" : ""}>Sea Breeze</option> 
		 <option value="Terra Mare" ${prevHotel === "Terra Mare" ? "selected" : ""}>Terra Mare</option> 
      </select>
    </td>
    <td>
      <input type="number" class="unit-number" placeholder="Unit Number" value="${prevUnit}">
    </td>
    <td>
      <input type="text" class="code" oninput="suggestCode(this)" list="suggestions${table.rows.length}">
      <datalist id="suggestions${table.rows.length}"></datalist>
    </td>
    <td><input type="text" class="description" readonly></td>
    <td><input type="number" class="price" readonly></td>
    <td><input type="number" class="quantity" oninput="calculateCustomTotal(this)"></td>
    <td><input type="number" class="total" readonly></td>
    <td><input type="date" name="date-received" class="date-received" required value="${prevDate || new Date().toISOString().split('T')[0]}"></td>
  `;

  // Add event listeners to the newly added inputs
  let codeInput = newRow.querySelector(".code");
  let quantityInput = newRow.querySelector(".quantity");

  // Valid code checking function (can be customized)
  function isValidCode(code) {
    // Assuming valid codes follow a specific pattern (e.g., alphanumeric, at least 3 characters)
    return /^[a-zA-Z0-9]{3,}$/.test(code);
  }

  codeInput.addEventListener("blur", () => {
    if (isValidCode(codeInput.value.trim())) {
      quantityInput.focus();  // Move focus to quantity if the code is valid
    }
  });

  newRow.querySelectorAll('input, select').forEach(newInput => {
    newInput.addEventListener('focus', () => {
      newInput.setAttribute('data-touched', true);
    });
    newInput.addEventListener('blur', checkInputs);
  });
}

		
    </script>

</body>
</html>
