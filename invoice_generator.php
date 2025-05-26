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

    $hotelName = $_POST['hotel'] ?? '';
$hotelNumber = $_POST['unit-number'] ?? '';
   $dateReceived = $_POST['date_received'] ?? '';

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
    }
    if (empty($grandTotal)) {
        $missingFields[] = "Grand Total";
    }

    if (!empty($missingFields)) {
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
	<h2>
  Invoice Number
  <input 
    type="number" 
    id="invoice_number" 
    name="invoiceNumber" 
    class="invoice-input" 
    required 
    min="0" 
    max="9999" 
    oninput="formatInvoiceNumber(this)" 
  />
</h2>



</h2>

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
                <select class="hotel" name="hotel" id="hotelSelect" onchange="updateUnitDatalist(this)">
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
            <td>
    <input type="number" name="quantity" class="quantity" min="1" list="quantity-list" oninput="calculateTotal(this)">
    <datalist id="quantity-list">
        <!-- Predefined numbers 1-20 -->
        <option value="1">
        <option value="2">
        <option value="3">
        <option value="4">
        <option value="5">
        <option value="6">
        <option value="7">
        <option value="8">
        <option value="9">
        <option value="10">
        <option value="11">
        <option value="12">
        <option value="13">
        <option value="14">
        <option value="15">
        <option value="16">
        <option value="17">
        <option value="18">
        <option value="19">
        <option value="20">
    </datalist>
</td>
			<td><input type="number" name="total" class="total" readonly></td>
			<td>
 <input type="date" name="date_received" class="date-received" required value="<?php echo date('Y-m-d', strtotime('-3 days')); ?>">
</td>
        </tr>
    </tbody>
</table>

    <button onclick="addRow()" class="add-item">Add Item</button>
<button onclick="removeRow()" class="remove-item" id ="removeRowBtn">Remove Item</button>
<button onclick="printInvoice()" class="save">Print</button>
<button type="button" onclick="addCustomItem()" class="custom-item">Custom</button>
<button id="comboAddBtn" type="button">Add Combo</button>

<!-- Modal for Combo Selection -->
<div id="comboModal" style="
  position:fixed; 
  top:15%; 
  left:50%; 
  transform:translate(-50%, -15%); 
  background:#fff; 
  border:1px solid #ccc; 
  border-radius:12px; 
  padding:30px 50px; 
  z-index:9999; 
  display:none;
  width: 650px; 
  max-height: 80vh; 
  overflow-y: auto;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 16px;
  color: #333;
">

  <h3 style="font-size: 1.8em; margin-bottom: 20px; text-align:center;">🧺 Select Item Combo(s)</h3>
  
  <div id="categoryContainer" style="margin-bottom: 20px;"></div>

  <div style="text-align:center; margin-top:20px;">
  <button id="comboCancelBtn" type="button" class="modern-btn cancel">Cancel</button>
  <button id="comboConfirmBtn" type="button" class="modern-btn confirm">Enter</button>
</div>

</div>
<button class="monthly-statements-btn" onclick="navigateToMonthlyStatement()">
  <span class="text">Monthly Statements</span>
  <div class="icon-container">
    <div class="icon icon--left">
      <svg><use xlink:href="#arrow-right"></use></svg>
    </div>
  </div>
</button>

<svg style="display: none;">
  <symbol id="arrow-right" viewBox="0 0 20 10">
    <path d="M14.84 0l-1.08 1.06 3.3 3.2H0v1.49h17.05l-3.3 3.2L14.84 10 20 5l-5.16-5z"></path>
  </symbol>
</svg>

</div>

<input type="submit" name="save_invoice" value="Save Invoice" class="submit-btn" onclick="saveTimerPrompt();">

<h3>Grand Total: R<span id="grandTotal">0.00</span></h3>
<input type="hidden" name="grandTotal" id="grandTotalInput" value="0.00">

</form>

<script>
function showCategory(category) {
  document.getElementById('towelsCategory').style.display = (category === 'towels') ? 'block' : 'none';
  document.getElementById('linenCategory').style.display = (category === 'linen') ? 'block' : 'none';
}

function toggleComboModal() {
  const modal = document.getElementById("comboModal");
  modal.style.display = (modal.style.display === "none" || modal.style.display === "") ? "block" : "none";
}

function closeComboModal() {
  document.getElementById("comboModal").style.display = "none";
}

function confirmCombo() {
  const selectedCombos = Array.from(document.querySelectorAll('input[name="combo"]:checked'))
                              .map(cb => cb.value);
  selectedCombos.forEach(combo => selectCodes(combo));
  closeComboModal();
}

</script>

<script>

const hotelUnitMap = {
  "Bronze Beach": [1, 3, 5, 6, 7, 8, 9, 11, 12, 14, 16, 17, 18, 19, 25, 26],
  "Bronze Bay": [1, 2, 3, 6, 8, 10, 11, 12, 15, 17, 19, 21, 24, 25, 26],
  "Bensiesta": [201, 302],
  "Sea Lodge": [12, 14, 45, 53, 72, 92],
  "Sea Breeze": [4],
  "Terra Mare": [108],
  "Kyalanga": [10, 17],
  "Glitter Bay": [15]
};

function updateUnitDatalist(hotelSelect) {
  const unitInput = hotelSelect.closest('tr').querySelector('.unit-number');
  const datalistId = 'unit-options-' + Math.random().toString(36).substr(2, 5); // unique ID
  let datalist = document.createElement('datalist');
  datalist.id = datalistId;

  const selectedHotel = hotelSelect.value;
  const units = hotelUnitMap[selectedHotel] || [];

  datalist.innerHTML = units.map(unit => `<option value="${unit}">`).join("");
  document.body.appendChild(datalist);

  unitInput.setAttribute('list', datalistId);
}
</script>

<script>
const comboDetails = {
  TSM: { name: "Hand Towels, Bath Sheets, Bath Mats", codes: ["TWLHAND", "BATHSH", "BATHMA"] },
  TBM: { name: "Hand Towels, Bath Towels, Bath Mats", codes: ["TWLHAND", "BATHTWL", "BATHMA"] },
  TBSM: { name: "Hand Towels, Bath Towels, Bath Sheets, Bath Mats", codes: ["TWLHAND", "BATHTWL", "BATHSH", "BATHMA"] },
  KLD: { name: "King Fitted Sheet, King Duvet Cover", codes: ["KSFITSHT", "DUVCOVKS"] },
  KID: { name: "King Flat Sheet, King Duvet Cover", codes: ["KSFLATSHT", "DUVCOVKS"] },
  QID: { name: "Queen Fitted Sheet, Queen Duvet Cover", codes: ["QSFITSHT", "DUVCOVQU"] },
  QLD: { name: "Queen Flat Sheet, Queen Duvet Cover", codes: ["QSFLATSHT", "DUVCOVQU"] },
  DLD: { name: "Double Flat Sheet, Double Duvet Cover", codes: ["DOFLASHT", "DUVCOVDO"] },
  DID: { name: "Double Fitted Sheet, Double Duvet Cover", codes: ["DOFITSHT", "DUVCOVDO"] },
  "3/4D": { name: "3/4 Fitted Sheet, 3/4 Duvet Cover", codes: ["3/4FITSHT", "DUVCOV3/4"] },
  SID: { name: "Single Fitted Sheet, Single Duvet Cover", codes: ["SIFITSHT", "DUVCOVSI"] },
  SLD: { name: "Single Flat Sheet, Single Duvet Cover", codes: ["SIFLASHT", "DUVCOVSI"] },
  PCC: { name: "Standard Pillow Case, Continental Pillow Case", codes: ["PILLCASE", "CONTPCASE"] }
};

const categories = {
  "Linen": ["KLD", "KID", "QID", "QLD", "DLD", "DID", "3/4D", "SID", "SLD"],
  "Towels": ["TSM", "TBM", "TBSM"],
  "Pillowcases": ["PCC"]
};

let selectedCombos = new Set();
let currentCategoryIndex = 0;
const categoryNames = Object.keys(categories);

function openComboModal() {
  selectedCombos.clear();
  currentCategoryIndex = 0;
  renderCategories();
  document.getElementById("comboModal").style.display = "block";
}

function closeComboModal() {
  document.getElementById("comboModal").style.display = "none";
}

function toggleCombo(code) {
  if (selectedCombos.has(code)) {
    selectedCombos.delete(code);
  } else {
    selectedCombos.add(code);
  }
}

function renderCategories() {
  const container = document.getElementById("categoryContainer");
  container.innerHTML = "";

  const categoryName = categoryNames[currentCategoryIndex];
  const combos = categories[categoryName];

  const navHTML = `
    <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
      <button onclick="prevCategory()" ${currentCategoryIndex === 0 ? "disabled" : ""}>⟵ Previous</button>
      <strong>${categoryName}</strong>
      <button onclick="nextCategory()" ${currentCategoryIndex === categoryNames.length - 1 ? "disabled" : ""}>Next ⟶</button>
    </div>
  `;

const checkboxes = combos.map(combo => `
  <label style="
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: start;
    margin-bottom: 10px;
    padding: 8px 12px;
    border: 1px solid #eee;
    border-radius: 6px;
    background: #f9f9f9;
    font-family: 'Segoe UI', sans-serif;
    font-size: 14px;
    line-height: 1.4;
  ">
    <span>${comboDetails[combo].name}</span>
    <input type="checkbox" value="${combo}" onchange="toggleCombo('${combo}')" style="margin-left: 10px;">
  </label>
`).join('');

  container.innerHTML = navHTML + checkboxes;
}

function prevCategory() {
  if (currentCategoryIndex > 0) {
    currentCategoryIndex--;
    renderCategories();
  }
}

function nextCategory() {
  if (currentCategoryIndex < categoryNames.length - 1) {
    currentCategoryIndex++;
    renderCategories();
  }
}

function insertSelectedCombos(e) {
  e.preventDefault();

  if (selectedCombos.size === 0) {
    closeComboModal();
    return;
  }

  const table = document.getElementById("invoiceTable");
  const firstRow = table.rows[0];
  const prevHotel = firstRow.querySelector(".hotel")?.value || "";
  const prevUnit = firstRow.querySelector(".unit-number")?.value || "";
  const prevDate = firstRow.querySelector(".date-received")?.value || new Date().toISOString().split("T")[0];

  let isFirstRowEmpty =
    !firstRow.querySelector(".code")?.value &&
    !firstRow.querySelector(".description")?.value &&
    (!firstRow.querySelector(".price")?.value || firstRow.querySelector(".price")?.value === "0") &&
    (!firstRow.querySelector(".quantity")?.value || firstRow.querySelector(".quantity")?.value === "0") &&
    (!firstRow.querySelector(".total")?.value || firstRow.querySelector(".total")?.value === "0");

  selectedCombos.forEach(combo => {
    const codes = comboDetails[combo]?.codes || [];

    codes.forEach((code) => {
      const item = priceList.find(i => i.code === code);
      if (!item) return;

      let row;
      if (isFirstRowEmpty) {
        row = firstRow;
        isFirstRowEmpty = false;
      } else {
        row = table.insertRow();
      }

      row.innerHTML = `
        <td>
          <select class="hotel" name="hotel" onchange="updateUnitDatalist(this)">
            ${[...firstRow.querySelector(".hotel").options].map(opt => 
              `<option value="${opt.value}" ${opt.value === prevHotel ? "selected" : ""}>${opt.text}</option>`
            ).join('')}
          </select>
        </td>
        <td><input type="number" class="unit-number" name="unit-number" value="${prevUnit}"></td>
        <td><input type="text" class="code" value="${item.code}"></td>
        <td><input type="text" class="description" readonly value="${item.description}"></td>
        <td><input type="number" class="price" readonly value="${item.price.toFixed(2)}"></td>
        <td>
          <input type="number" class="quantity" min="1" list="quantity-list" oninput="calculateCustomTotal(this)">
          <datalist id="quantity-list">
            ${Array.from({ length: 20 }, (_, i) => `<option value="${i + 1}">`).join('')}
          </datalist>
        </td>
        <td><input type="number" class="total" readonly></td>
        <td><input type="date" class="date-received" name="date_received" value="${prevDate}" required></td>
      `;

      row.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('focus', () => el.setAttribute('data-touched', true));
        el.addEventListener('blur', checkInputs);
      });

      row.querySelector('.quantity').addEventListener('input', e => calculateCustomTotal(e.target));
    });
  });

  closeComboModal();
}

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("comboAddBtn")?.addEventListener("click", openComboModal);
  document.getElementById("comboCancelBtn")?.addEventListener("click", closeComboModal);
  document.getElementById("comboConfirmBtn")?.addEventListener("click", insertSelectedCombos);

  document.getElementById("comboModal").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
      e.preventDefault();
      insertSelectedCombos(e);
    }
  });
});

</script>

<script>
    // On page load, check if there's a previously selected hotel in localStorage
    window.onload = function() {
        const selectedHotel = localStorage.getItem('selectedHotel');
        if (selectedHotel) {
            // Set the previously selected hotel as selected in the dropdown
            document.getElementById('hotelSelect').value = selectedHotel;
        }
    };

    // When a new hotel is selected, save it to localStoragrage
    document.getElementById('hotelSelect').addEventListener('change', function() {
        localStorage.setItem('selectedHotel', this.value);
    });
</script>

<script>

// Updates the grand total
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

// Function to calculate total for each item
function calculateTotal(input) {
    let row = input.closest('tr');
    let price = parseFloat(row.querySelector('.price').value) || 0;
    let quantity = parseInt(input.value) || 0;
    let total = price * quantity;
    row.querySelector('.total').value = total.toFixed(2);
    updateGrandTotal();  // Calls the function to update the grand total
}

// Event listener to handle input change
document.querySelectorAll('.quantity').forEach(input => {
    input.addEventListener('input', function () {
        calculateTotal(this); // Calculate total when quantity changes
    });
});

// Handle the quantity input change when a value from datalist is selected
document.querySelectorAll('input[list="quantity-list"]').forEach(input => {
    input.addEventListener('change', function () {
        calculateTotal(this); // Recalculate the total when value changes from datalist
    });
});

</script>

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
    { code: "LFRRGS", description: "Large Floor Rags", price: 60.00 },
    { code: "BDFR", description: "Bed Frill", price: 30.52 },
    { code: "BEATWL", description: "Beach Towel", price: 10.42 },
	{ code: "CARSML", description: "Small Carpet", price: 50.00 },
	{ code: "CARMED", description: "Medium Carpet", price: 55.00 },
	{ code: "CARLRG", description: "Large Carpet", price: 60.00 },
    { code: "BEDMAT", description: "Bedside Mat", price: 69.30 },
    { code: "BLANKS", description: "Single Blanket", price: 60.00 },
    { code: "BSQ", description: "Bed Spread-Quilt", price: 121.26 },
	{ code: "SWBS", description: "Swabs", price: 5.00 },
    { code: "CCOVLRG", description: "Cushion Covers/Large", price: 43.29 },
	{ code: "LACECURT", description: "Small Lace Cutain", price: 60.00 },
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

document.getElementById('invoice_number').addEventListener('blur', function() {
    const invoiceNumber = this.value;
    if (invoiceNumber) {
        navigator.clipboard.writeText(invoiceNumber).then(() => {
            console.log('Invoice number copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }
});

function addCustomItem() {
  const table = document.getElementById("invoiceTable");

  // Check if first row can be reused (row[1] because row[0] is the header)
  let targetRow = null;
  if (table.rows.length > 1) {
    const firstRow = table.rows[1];

    const hotel = firstRow.querySelector('.hotel')?.value.trim();
    const unit = firstRow.querySelector('.unit-number')?.value.trim();
    const date = firstRow.querySelector('.date-received')?.value.trim();

    const code = firstRow.querySelector('.code')?.value.trim();
    const desc = firstRow.querySelector('.description')?.value.trim();
    const price = firstRow.querySelector('.price')?.value;
    const qty = firstRow.querySelector('.quantity')?.value;
    const total = firstRow.querySelector('.total')?.value;

    const hasHotelInfo = hotel && unit && date;
    const isBlankElse = !code && !desc && (!price || price == 0) && (!qty || qty == 0) && (!total || total == 0);

    if (hasHotelInfo && isBlankElse) {
      targetRow = firstRow;
    }
  }

  // If not reusing, insert a new row at the end
  if (!targetRow) {
    targetRow = table.insertRow();
  }

  // Get latest known hotel/unit/date from the last filled row
  const lastFilledRow = [...table.rows].reverse().find(row =>
    row.querySelector('.hotel') &&
    row.querySelector('.unit-number') &&
    row.querySelector('.date-received')
  );

  const previousHotel = lastFilledRow?.querySelector('.hotel')?.value || "";
  const previousUnit = lastFilledRow?.querySelector('.unit-number')?.value || "";
  const previousDate = lastFilledRow?.querySelector('.date-received')?.value || new Date().toISOString().split('T')[0];

  // Fill the row
  targetRow.innerHTML = `
    <td>
      <select class="hotel" onchange="updateUnitDatalist(this)">
        <option value="Beacon-Rock" ${previousHotel === "Beacon-Rock" ? "selected" : ""}>Beacon-Rock</option>
        <option value="Bensiesta" ${previousHotel === "Bensiesta" ? "selected" : ""}>Bensiesta</option>
        <option value="Berumdas" ${previousHotel === "Berumdas" ? "selected" : ""}>Bermudas</option>
        <option value="Bronze Bay" ${previousHotel === "Bronze Bay" ? "selected" : ""}>Bronze Bay</option>
        <option value="Bronze Beach" ${previousHotel === "Bronze Beach" ? "selected" : ""}>Bronze Beach</option>
        <option value="Breakers" ${previousHotel === "Breakers" ? "selected" : ""}>Breakers</option>
        <option value="Cormoran" ${previousHotel === "Cormoran" ? "selected" : ""}>Cormoran</option>
        <option value="Glitter Bay" ${previousHotel === "Glitter Bay" ? "selected" : ""}>Glitter Bay</option>
        <option value="Kyalanga" ${previousHotel === "Kyalanga" ? "selected" : ""}>Kyalanga</option>
        <option value="Malindi" ${previousHotel === "Malindi" ? "selected" : ""}>Malindi</option>
        <option value="Marine" ${previousHotel === "Marine" ? "selected" : ""}>Marine</option>
        <option value="Oyster Rock" ${previousHotel === "Oyster Rock" ? "selected" : ""}>Oyster Rock</option>
        <option value="Pearls" ${previousHotel === "Pearls" ? "selected" : ""}>Pearls</option>
        <option value="Sea Lodge" ${previousHotel === "Sea Lodge" ? "selected" : ""}>Sea Lodge</option>
        <option value="Sea Breeze" ${previousHotel === "Sea Breeze" ? "selected" : ""}>Sea Breeze</option>
        <option value="Shades" ${previousHotel === "Shades" ? "selected" : ""}>Shades</option>
        <option value="Terra Mare" ${previousHotel === "Terra Mare" ? "selected" : ""}>Terra Mare</option>
      </select>
    </td>
    <td><input type="number" class="unit-number" value="${previousUnit}" placeholder="Unit Number"></td>
    <td><input type="text" class="code" placeholder="Custom Code"></td>
    <td><input type="text" class="description" placeholder="Description"></td>
    <td><input type="number" class="price" placeholder="Unit Price" oninput="calculateCustomTotal(this)"></td>
    <td>
      <input type="number" class="quantity" min="1" list="quantity-list" oninput="calculateCustomTotal(this)">
      <datalist id="quantity-list">
        ${Array.from({length: 20}, (_, i) => `<option value="${i + 1}">`).join('')}
      </datalist>
    </td>
    <td><input type="text" class="total" readonly></td>
    <td><input type="date" name="date_received" class="date-received" value="${previousDate}" required></td>

</td>
  `;

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

function checkInputs() {
  const inputFields = document.querySelectorAll('input, select');
  let allFilled = true;

  inputFields.forEach(input => {
    // Check if the input value is empty or only contains whitespaces
    if (!input.value || /^\s*$/.test(input.value)) {
      if (input.hasAttribute('data-touched')) { // Check if the field has been touched
        input.classList.add('invalid');
      }
      allFilled = false; // Mark as not all filled
    } else {
      input.classList.remove('invalid');
    }
  });

  document.querySelector('.save').disabled = !allFilled;
}


//Suggests codes to choose from
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

//Updates grand total
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

//Removes a row
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
            updateGrandTotal(); // Recalculate total after removing a row
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
</script>

<script>

function calculateGrandTotal() {
  updateGrandTotal();
}

function addRow() {
  let table = document.getElementById("invoiceTable");
  let lastRow = table.rows[table.rows.length - 1];

  // Get values to carry forward
  let prevHotel = lastRow?.querySelector(".hotel")?.value || "";
  let prevUnit = lastRow?.querySelector(".unit-number")?.value || "";
  let prevDate = lastRow?.querySelector(".date-received")?.value || "";

  // Create a new row
  let newRow = table.insertRow();

  newRow.innerHTML = `
    <td>
      <select class="hotel" name="hotel" onchange="updateUnitDatalist(this)">
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
    </td>
    <td><input type="number" name="unit-number" class="unit-number" placeholder="Unit Number"></td>
    <td>
      <input type="text" class="code" oninput="suggestCode(this)" list="suggestions">
      <datalist id="suggestions"></datalist>
    </td>
    <td><input type="text" name="description" class="description" readonly></td>
    <td><input type="number" name="price" class="price" readonly></td>
    <td>
      <input type="number" name="quantity" class="quantity" min="1" list="quantity-list" oninput="calculateTotal(this)">
      <datalist id="quantity-list">
        ${Array.from({ length: 20 }, (_, i) => `<option value="${i + 1}">`).join("")}
      </datalist>
    </td>
    <td><input type="number" name="total" class="total" readonly></td>
    <td><input type="date" name="date_received" class="date-received" required>
</td>
  `;

  // Set values that should carry over
  newRow.querySelector(".hotel").value = prevHotel;
  newRow.querySelector(".unit-number").value = prevUnit;
  newRow.querySelector(".date-received").value = prevDate;

  function isValidCode(code) {
    return /^[a-zA-Z0-9]{3,}$/.test(code);
  }

  codeInput.addEventListener("blur", () => {
    if (isValidCode(codeInput.value.trim())) {
      quantityInput.focus();
    }
  });

  newRow.querySelectorAll('input, select').forEach(newInput => {
    newInput.addEventListener('focus', () => {
      newInput.setAttribute('data-touched', true);
    });
    newInput.addEventListener('blur', checkInputs);
  });

  // 👉 Recalculate the grand total after the row is added
  updateGrandTotal();
}
    </script>
	
	<script>
	// Prints out invoice
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

	</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const invoiceInput = document.getElementById('invoice_number');
  const dateInput = document.querySelector('.date-received');
  const unitInput = document.querySelector('.unit-number');
  const codeInput = document.querySelector('.code');
  const quantityInputs = document.querySelectorAll('.quantity');
  const addItemBtn = document.querySelector('.add-item');
  const addComboBtn = document.getElementById('comboAddBtn');

  if (!invoiceInput || !dateInput || !unitInput || quantityInputs.length === 0 || !codeInput) return;

  // Step 1: Focus invoice number
  invoiceInput.focus();

  // Step 2: Move to date if 5 digits typed
  invoiceInput.addEventListener('input', () => {
    if (invoiceInput.value.length === 5) {
      openCalendar(dateInput);
    }
  });

  // Step 3: After date selected, go to unit
  dateInput.addEventListener('change', () => {
    unitInput.focus();
  });

  // Step 4: After unit filled, go to code
  unitInput.addEventListener('blur', () => {
    const firstCode = document.querySelector('.code');
    if (firstCode) firstCode.focus();
  });

  // Step 5: When code is changed, go to quantity
  document.addEventListener('change', (e) => {
    if (e.target.classList.contains('code')) {
      const relatedQuantity = e.target.closest('tr').querySelector('.quantity');
      if (relatedQuantity) relatedQuantity.focus();
    }
  });

  // Step 6: After Add Item, go to last new code field
  if (addItemBtn) {
    addItemBtn.addEventListener('click', () => {
      setTimeout(() => {
        const newCodeInputs = document.querySelectorAll('.code');
        const lastCodeInput = newCodeInputs[newCodeInputs.length - 1];
        lastCodeInput?.focus();
      }, 50);
    });
  }

  // Step 7: After Add Combo, walk through each new quantity input
if (addComboBtn) {
  addComboBtn.addEventListener('click', () => {
    const oldQuantities = document.querySelectorAll('.quantity');

    // Wait until new quantity fields appear
    const waitForNewRows = setInterval(() => {
      const allQuantities = document.querySelectorAll('.quantity');
      if (allQuantities.length > oldQuantities.length) {
        clearInterval(waitForNewRows);

        // Get only new quantity fields
        const newQuantities = Array.from(allQuantities).slice(oldQuantities.length);

        // Focus and step through each new empty quantity
        const walkQuantities = (inputs, index = 0) => {
          if (index >= inputs.length) return;
          inputs[index].focus();
          inputs[index].addEventListener('input', function handler() {
            if (this.value.trim()) {
              this.removeEventListener('input', handler);
              walkQuantities(inputs, index + 1);
            }
          });
        };

        walkQuantities(newQuantities);
      }
    }, 50);
  });
}

});

// 🔧 Open calendar popup reliably
function openCalendar(input) {
  if (typeof input.showPicker === 'function') {
    input.showPicker(); // modern browsers
  } else {
    input.focus(); // fallback
  }
}
</script>

<script>
const observer = new MutationObserver(() => {
  const lastRow = document.querySelector('#invoiceTable tbody tr:last-child');
  if (lastRow) {
    const rect = lastRow.getBoundingClientRect();
    if (rect.bottom > window.innerHeight - 50) {
      window.scrollBy({ top: 150, behavior: 'smooth' });
    }
  }
});

// Start observing the table for row changes
const tableBody = document.querySelector('#invoiceTable tbody');
if (tableBody) {
  observer.observe(tableBody, { childList: true });
}
</script>





</body>
</html>
