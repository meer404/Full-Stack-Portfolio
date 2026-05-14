<?php
require_once __DIR__ . '/config.php';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ku'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$lang = $_SESSION['lang'] ?? 'en';
$strings = require __DIR__ . '/lang/' . $lang . '.php';
$dir = $lang === 'ku' ? 'rtl' : 'ltr';
