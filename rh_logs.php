<?php
require_once __DIR__ . '/inc/functions.php';
rh_authenticate();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$action_filter = isset($_GET['action']) ? $_GET['action'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

include __DIR__ . '/templates/rh_logs.php';
