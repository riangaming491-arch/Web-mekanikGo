<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

csrfPeriksa();

$_SESSION = [];
session_regenerate_id(true);
session_destroy();

header('Location: index.php');
exit;