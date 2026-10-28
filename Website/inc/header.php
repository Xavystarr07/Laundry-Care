<?php
/* Expects before include: $page (slug), $title, $desc */
$nav = [
    ['index.php',    'Home',        'index',    'fa-house'],
    ['about.php',    'About Us',    'about',    'fa-circle-info'],
    ['services.php', 'Services',    'services', 'fa-list-check'],
    ['process.php',  'Our Process', 'process',  'fa-rotate'],
    ['pricing.php',  'Pricing',     'pricing',  'fa-tag'],
];
$title = $title ?? 'Laundry Care | A Fresh Start In Every Wash';
$desc  = $desc  ?? 'Laundry Care - premium hospitality linen & garment maintenance in Durban.';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>" />
    <meta name="theme-color" content="#0B1E3D" />
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <script>try{var t=localStorage.getItem('lcTheme');if(t)document.documentElement.setAttribute('data-theme',t)}catch(e){}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Fraunces:opsz,wght@9..144,400;9..144,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/pages.css" />
</head>
<body class="page-<?= $page ?>">

<?php if ($page === 'index'): ?>
    <!-- PRELOADER (home only, once per visit) -->
    <div id="preloader" aria-hidden="true">
        <div class="pl-wrap">
            <div class="pl-door">
                <div class="pl-ring"></div>
                <div class="pl-lc"><span>L</span><span>C</span></div>
            </div>
            <p class="pl-name"><span>L</span><b>aundry</b> <span class="c">C</span><b>are</b></p>
            <p class="pl-slogan"><span id="pl-t"></span><span id="pl-r" style="visibility:hidden"></span></p>
        </div>
    </div>
    <script>try{if(sessionStorage.getItem('lcSeen')){var p=document.getElementById('preloader');p&&p.remove()}else{sessionStorage.setItem('lcSeen','1')}}catch(e){}</script>
<?php endif; ?>

    <header id="site-header" role="banner">
        <div class="header-inner container">
            <a href="index.php" class="brand" aria-label="Laundry Care Home">
                <img src="logo.png" alt="Laundry Care" class="brand-logo-img" />
                <span class="brand-wordmark">Laundry<em>Care</em></span>
            </a>

            <nav id="primary-nav" role="navigation" aria-label="Primary">
                <ul>
                    <?php foreach ($nav as $n): ?>
                    <li><a href="<?= $n[0] ?>" class="nav-link<?= $page === $n[2] ? ' current' : '' ?>"><?= $n[1] ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="contact.php" class="nav-link nav-cta<?= $page === 'contact' ? ' current' : '' ?>">Get a Quote</a></li>
                </ul>
            </nav>

            <button type="button" class="theme-toggle" aria-label="Switch theme" title="Switch theme">
                <i class="fa-solid fa-sun i-sun"></i><i class="fa-solid fa-moon i-moon"></i>
            </button>

            <button id="nav-toggle" class="hamburger" aria-label="Toggle menu" aria-expanded="false">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <aside id="mobile-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Mobile menu">
        <div class="drawer-inner">
            <div class="drawer-logo"><img src="logo.png" alt="Laundry Care" /></div>
            <button id="drawer-close" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
            <ul class="drawer-nav">
                <?php foreach ($nav as $n): ?>
                <li><a href="<?= $n[0] ?>" class="drawer-link"><i class="fa-solid <?= $n[3] ?>"></i> <?= $n[1] ?></a></li>
                <?php endforeach; ?>
                <li><a href="contact.php" class="drawer-link drawer-cta"><i class="fa-solid fa-comments"></i> Get a Quote</a></li>
            </ul>
            <div class="drawer-theme"><button type="button" class="theme-toggle" aria-label="Switch theme"><i class="fa-solid fa-sun i-sun"></i><i class="fa-solid fa-moon i-moon"></i></button><span>Switch light / dark</span></div>
            <p class="drawer-slogan">A Fresh Start In Every Wash</p>
        </div>
    </aside>
    <div id="drawer-overlay" aria-hidden="true"></div>
