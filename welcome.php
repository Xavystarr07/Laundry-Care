<?php
// ============================================================
//  welcome.php — Asks Regular or Departure, remembers the choice
//  Regular   → invoice_generator.php
//  Departure → departure_generator.php
// ============================================================
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? '';
    if ($mode === 'regular') {
        $_SESSION['mode'] = 'regular';
        header('Location: invoice_generator.php');
        exit;
    }
    if ($mode === 'departure') {
        $_SESSION['mode'] = 'departure';
        header('Location: departure_generator.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Care — Welcome</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: #0f0f1a;
            color: #e0e0f0;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 2.2em;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #fff;
        }

        .sub { margin: 0 0 40px; color: #9a9ab8; font-size: 1.05em; }

        .choices {
            display: flex;
            gap: 28px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .choice-form { margin: 0; }

        .choice {
            width: 280px;
            padding: 36px 24px;
            background: #1a1a2e;
            border-radius: 22px;
            cursor: pointer;
            font-family: inherit;
            color: #e0e0f0;
            transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .choice:hover { transform: translateY(-6px) scale(1.03); }
        .choice:active { transform: scale(0.98); }

        .choice svg { width: 56px; height: 56px; margin-bottom: 14px; }
        .choice .title { display: block; font-size: 1.5em; font-weight: 700; margin-bottom: 8px; }
        .choice .desc  { display: block; font-size: 0.95em; line-height: 1.4; color: #b0b0c8; }

        .choice.regular   { border: 2px solid #8b5cf6; }
        .choice.regular svg,
        .choice.regular .title { color: #8b5cf6; }
        .choice.regular:hover { background: #221a3d; box-shadow: 0 12px 36px rgba(139,92,246,0.35); }

        .choice.departure { border: 2px solid #f59e0b; }
        .choice.departure svg,
        .choice.departure .title { color: #f59e0b; }
        .choice.departure:hover { background: #33250a; box-shadow: 0 12px 36px rgba(245,158,11,0.35); }
    </style>
</head>
<body>

    <h1>Laundry Care</h1>
    <p class="sub">What type of billing are you doing?</p>

    <div class="choices">

        <form method="post" class="choice-form">
            <input type="hidden" name="mode" value="regular">
            <button type="submit" class="choice regular">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="8" y1="13" x2="16" y2="13"/>
                    <line x1="8" y1="17" x2="16" y2="17"/>
                </svg>
                <span class="title">Regular</span>
                <span class="desc">Individual items billed per invoice</span>
            </button>
        </form>

        <form method="post" class="choice-form">
            <input type="hidden" name="mode" value="departure">
            <button type="submit" class="choice departure">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/>
                    <path d="M2 17h20"/><path d="M6 8v9"/>
                </svg>
                <span class="title">Departure</span>
                <span class="desc">Flat rate per unit by bedroom count</span>
            </button>
        </form>

    </div>

</body>
</html>