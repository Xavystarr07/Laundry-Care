<?php
// ============================================================
//  php/departure_handler.php — Saves departure billing
//  Every departure line has its OWN invoice number.
//  Nothing from the browser is trusted: invoice number, hotel, unit, code
//  and date are re-checked here and the price is looked up here.
//  All lines are saved in ONE transaction (all or nothing).
//
//  KEEP IN SYNC with js/depData.js — if you add units or change prices
//  there, change them in the two arrays below as well.
// ============================================================
session_start();
date_default_timezone_set('Africa/Johannesburg');

require_once __DIR__ . '/../config.php';

// ── Prices (server copy — these are the ones that get saved) ─────────────────
const DEP_PRICES = [
    'DEP1' => 129.86,
    'DEP2' => 225.11,
    'DEP3' => 292.66,
    'DEP4' => 323.85,
];

// ── hotel → unit → bedrooms (null = unit exists, bedroom count unknown) ──────
const DEP_UNITS = [
    'Bronze Bay'   => [1=>2, 2=>2, 3=>2, 6=>2, 8=>2, 10=>2, 11=>2, 12=>2, 15=>2, 17=>2, 19=>2, 21=>1, 24=>1, 25=>3, 26=>3],
    'Bronze Beach' => [1=>2, 3=>2, 5=>2, 6=>2, 7=>2, 8=>2, 9=>2, 10=>2, 11=>2, 12=>2, 14=>2, 16=>null, 17=>2, 18=>2, 19=>2, 25=>3, 26=>3],
    'Breakers'     => [128=>2, 131=>2, 226=>2, 228=>2, 231=>2, 311=>2, 331=>2, 423=>2, 515=>2, 516=>2, 210=>3, 422=>1, 512=>1],
    'Sea Lodge'    => [12=>3, 14=>3, 45=>null, 53=>3, 64=>3, 72=>3, 84=>3, 92=>null],
    'Terra Mare'   => [108=>3],
    'Cormoran'     => [10=>3, 25=>3, 31=>4],
    'Glitter Bay'  => [15=>3],
    'Kyalanga'     => [17=>3, 27=>3],
    'Lighthouse'   => [201=>2, 204=>1],
    'Marine'       => [35=>4],
    'Oceans'       => [2106=>2],
    'Pearls'       => [14=>1, 43=>2],
    'Sea Breeze'   => [4=>2],
    'Bensiesta'    => [201=>null, 302=>null],
    'Beacon-Rock'  => [],
    'Bermudas'     => [],
    'Malindi'      => [],
    'Oyster Rock'  => [],
    'Shades'       => [],
];

const MAX_ROWS = 200;

// ── Helper: go back to the departure page with a popup ───────────────────────
function backWithPopup(string $type, string $msg, string $tip, ?array $restore = null): void {
    $_SESSION['popup'] = ['type' => $type, 'msg' => $msg, 'tip' => $tip];
    if ($restore !== null) {
        $_SESSION['restore_dep'] = $restore;
    }
    header('Location: ../departure_generator.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../departure_generator.php');
    exit;
}

// ── 1. Read the payload ──────────────────────────────────────────────────────
$payload = json_decode($_POST['payload'] ?? '', true);

// Data to put back on the page if something fails (so nothing is retyped)
$restore = ['rows' => []];
if (is_array($payload) && isset($payload['rows']) && is_array($payload['rows'])) {
    foreach (array_slice($payload['rows'], 0, MAX_ROWS) as $r) {
        if (!is_array($r)) continue;
        $restore['rows'][] = [
            'inv'   => is_scalar($r['inv'] ?? null) ? preg_replace('/\D/', '', (string)$r['inv']) : '',
            'hotel' => (string)($r['hotel'] ?? ''),
            'unit'  => (string)($r['unit']  ?? ''),
            'dep'   => (string)($r['dep']   ?? ''),
            'date'  => (string)($r['date']  ?? ''),
        ];
    }
}

if (!is_array($payload) || !isset($payload['rows']) || !is_array($payload['rows'])) {
    backWithPopup('error', 'Something went wrong reading the departures.', '💡 Please try again.', $restore);
}

// ── 2. Validate every line (nothing is saved unless ALL are valid) ───────────
$rows = $payload['rows'];
if (count($rows) < 1) {
    backWithPopup('error', 'No departures to save.', '💡 Add at least one departure.', $restore);
}
if (count($rows) > MAX_ROWS) {
    backWithPopup('error', 'Too many departures at once (max ' . MAX_ROWS . ').',
        '💡 Save them in two batches.', $restore);
}

$today = new DateTimeImmutable('today');
$min   = $today->modify('-2 months');

$clean   = [];
$seen    = [];   // hotel|unit|date
$seenInv = [];   // invoice numbers used in this batch

foreach ($rows as $i => $r) {
    $n = $i + 1;

    if (!is_array($r)) {
        backWithPopup('error', "Line $n is not valid.", '💡 Please try again.', $restore);
    }

    // Invoice number (each line has its own)
    $invRaw = is_scalar($r['inv'] ?? null) ? preg_replace('/\D/', '', (string)$r['inv']) : '';
    $inv    = intval($invRaw);
    if ($invRaw === '' || strlen($invRaw) > 5 || $inv < 1 || $inv > 50000) {
        backWithPopup('error', "Line $n: invoice number must be between 1 and 50000.",
            '💡 Enter a valid 5-digit invoice number (e.g. 12345).', $restore);
    }
    if (isset($seenInv[$inv])) {
        backWithPopup('error', "Invoice #$inv is used on more than one line.",
            '💡 Every departure needs its own invoice number.', $restore);
    }
    $seenInv[$inv] = true;

    $hotel = (string)($r['hotel'] ?? '');
    $unit  = $r['unit'] ?? '';
    $dep   = (string)($r['dep'] ?? '');
    $date  = (string)($r['date'] ?? '');

    if (!array_key_exists($hotel, DEP_UNITS)) {
        backWithPopup('error', "Line $n: unknown hotel.", '💡 Pick a hotel from the list.', $restore);
    }
    if (!is_int($unit) && !(is_string($unit) && ctype_digit($unit))) {
        backWithPopup('error', "Line $n: unit number is not valid.", '💡 Pick a unit from the list.', $restore);
    }
    $unit = (int)$unit;
    if (!array_key_exists($unit, DEP_UNITS[$hotel])) {
        backWithPopup('error', "Line $n: unit $unit isn't listed for $hotel.", '💡 Pick a unit from the list.', $restore);
    }
    if (!isset(DEP_PRICES[$dep])) {
        backWithPopup('error', "Line $n: invalid departure type.", '💡 Choose DEP1 to DEP4.', $restore);
    }

    $d = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    $validDate = $d && $d->format('Y-m-d') === $date;
    if (!$validDate || $d < $min || $d > $today) {
        backWithPopup('error', "Line $n: date must be within the last 2 months and not in the future.",
            '💡 Pick a valid date.', $restore);
    }

    $key = "$hotel|$unit|$date";
    if (isset($seen[$key])) {
        backWithPopup('error', "$hotel $unit is listed twice for $date.",
            '💡 Remove the duplicate line.', $restore);
    }
    $seen[$key] = true;

    $clean[] = [
        'inv'   => $inv,
        'hotel' => $hotel,
        'unit'  => (string)$unit,          // "HotelNumber" is VARCHAR
        'dep'   => $dep,
        'date'  => $date,
        'total' => DEP_PRICES[$dep],       // price comes from HERE, never the browser
    ];
}

// ── 3. Save everything in one transaction ────────────────────────────────────
try {
    $pdo = new PDO(
        "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (Exception $e) {
    error_log('departure_handler connect: ' . $e->getMessage());
    backWithPopup('error', 'Database connection failed.', '💡 Check config.php credentials.', $restore);
}

try {
    $pdo->beginTransaction();

    // Lock every invoice number (in order, to avoid deadlocks) so two people
    // can't save the same number at the same moment
    $numbers = array_keys($seenInv);
    sort($numbers);
    $lock = $pdo->prepare('SELECT pg_advisory_xact_lock(CAST(:n AS BIGINT))');
    foreach ($numbers as $num) {
        $lock->execute([':n' => $num]);
    }

    // Numbers come from one slip book → reject if ANY invoice already uses one
    $check = $pdo->prepare('SELECT 1 FROM invoices WHERE "InvoiceNumber" = :n LIMIT 1');
    foreach ($numbers as $num) {
        $check->execute([':n' => $num]);
        if ($check->fetchColumn()) {
            $pdo->rollBack();
            backWithPopup('dupe', "Invoice #$num already exists in the database. Nothing was saved.",
                '💡 Change that line\'s invoice number and save again.', $restore);
        }
    }

    $ins = $pdo->prepare('
        INSERT INTO invoices
            ("InvoiceNumber", "HotelName", "HotelNumber", "DateReceived", "Total", "BillingType", "DepCode")
        VALUES
            (:inv, :hotel, :unit, :date, :total, \'departure\', :dep)
        RETURNING id
    ');

    $sum = 0.0;
    $savedIds = [];
    foreach ($clean as $c) {
        $ins->execute([
            ':inv'   => $c['inv'],
            ':hotel' => $c['hotel'],
            ':unit'  => $c['unit'],
            ':date'  => $c['date'],
            ':total' => number_format($c['total'], 2, '.', ''),
            ':dep'   => $c['dep'],
        ]);
        $savedIds[] = (int)$ins->fetchColumn();
        $ins->closeCursor();
        $sum += $c['total'];
    }

    $pdo->commit();
    $_SESSION['last_save'] = ['type' => 'departure', 'ids' => $savedIds, 'time' => time(), 'restore' => $restore];

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('departure_handler save: ' . $e->getMessage());
    backWithPopup('error', 'Could not save — nothing was saved.',
        '💡 Your lines are still here. Try again, or contact your system administrator.', $restore);
}

// ── 4. Success ───────────────────────────────────────────────────────────────
$count = count($clean);
backWithPopup('success',
    "$count departure" . ($count === 1 ? '' : 's') . ' saved, each with its own invoice number (R' .
    number_format($sum, 2) . ' total) 🎊',
    '✅ Ready for the next departures.');