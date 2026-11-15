.then(r=>r.text()).then(t=>{try{return JSON.parse(t)}catch(e){return{ok:false,msg:'Server: '+t.slice(0,200)}}});<?php
// ============================================================
//  php/db.php — Invoice App database connection
//  Reads credentials from Invoice App/config.php
//  DO NOT edit credentials here — edit config.php instead
// ============================================================

require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO(
        "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_PERSISTENT => false, PDO::ATTR_EMULATE_PREPARES => true]
    );
} catch (Exception $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
