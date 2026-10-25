<?php
// ============================================================
//  departure_generator.php — Departure billing page
//  Saves via php/departure_handler.php (built in step 4)
// ============================================================
session_start();

// No mode chosen yet this session → ask first
if (!isset($_SESSION['mode'])) {
    header('Location: welcome.php');
    exit;
}
$_SESSION['mode'] = 'departure';

$popup   = $_SESSION['popup']       ?? null; unset($_SESSION['popup']);
$restore = $_SESSION['restore_dep'] ?? null; unset($_SESSION['restore_dep']);
require_once 'php/undo_helper.php';
$undo = undoAvailable();
$flags   = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Care Departures</title>

    <link rel="stylesheet" href="html_css/base.css">
    <link rel="stylesheet" href="html_css/header.css">
    <link rel="stylesheet" href="html_css/table.css">
    <link rel="stylesheet" href="html_css/inputs.css">
    <link rel="stylesheet" href="html_css/buttons.css">
    <link rel="stylesheet" href="html_css/departure.css">
</head>
<body>

<?php if ($undo): ?>
<script>window._undoAvail = <?php echo json_encode($undo); ?>;</script>
<?php endif; ?>
<?php if ($popup): ?>
<script>window._pendingPopup = <?php echo json_encode($popup, $flags); ?>;</script>
<?php endif; ?>
<?php if ($restore): ?>
<script>window._restoreDep = <?php echo json_encode($restore, $flags); ?>;</script>
<?php endif; ?>

<?php include 'partials/header.html'; ?>

<form id="departureForm" method="post" action="php/departure_handler.php">

    <div class="center">

    </div>

    <!-- ── Entry bar ── -->
    <div class="dep-entry">
        <div class="dep-field dep-field-inv">
            <label for="depInv">Invoice No</label>
            <input type="text" id="depInv" maxlength="5" placeholder="00000" autocomplete="off" inputmode="numeric">
        </div>
        
        <div class="dep-field">
            <label for="depHotel">Hotel</label>
            <select id="depHotel"></select>
        </div>
        <div class="dep-field">
            <label for="depUnit">Unit Number</label>
            <select id="depUnit"></select>
        </div>
        <div class="dep-field">
            <label for="depCode">Departure Type</label>
            <select id="depCode"></select>
        </div>
        <div class="dep-field">
            <label for="depDate">Date</label>
            <input type="date" id="depDate">
        </div>
        <div class="dep-actions">
            <button type="button" class="uv-btn add-item" id="depAddBtn">
                <svg class="uv-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Add More</span>
            </button>
            <button type="button" class="uv-btn print-btn" id="depPrintBtn">
                <svg class="uv-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
                </svg>
                <span>Print</span>
            </button>
        </div>
    </div>


    <table id="depTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice No</th>
                <th>Hotel</th>
                <th>Unit</th>
                <th>Bedrooms</th>
                <th>Departure Type</th>
                <th>Price (R)</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="depBody"></tbody>
    </table>

    <div class="save-row">
        <button type="button" class="uv-btn" id="depCompleteBtn">
            <svg class="uv-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span>Complete &amp; Save</span>
        </button>
    </div>

    <div class="grand-total-row">
        <h3>Grand Total: R<span id="depGrandTotal">0.00</span></h3>
        <p class="dep-note"><span id="depCount">0</span> departure(s) · display only — each line is saved to its own hotel/unit</p>
    </div>

    <input type="hidden" name="payload" id="depPayload">
</form>

<button type="button" class="monthly-statements-fab" id="statementsFab">
    <svg width="16" height="16" viewBox="0 0 20 10" fill="white"><path d="M14.84 0l-1.08 1.06 3.3 3.2H0v1.49h17.05l-3.3 3.2L14.84 10 20 5l-5.16-5z"/></svg>
    Monthly Statements
</button>

<script src="js/priceList.js"></script>
<script src="js/depData.js"></script>
<script src="js/popupNotify.js"></script>
<script src="js/invoiceFormat.js"></script>
<script src="js/navigation.js"></script>
<script src="js/departure.js"></script>
<script src="js/undoSave.js"></script>
<script src="js/themeSearch.js"></script>

</body>
</html>