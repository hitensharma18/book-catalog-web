<?php
session_start();
require_once 'server/db_connect.php';

// Redirect to login if not authenticated, or to catalog if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: pages/catalog.php');
} else {
    header('Location: pages/login.php');
}
exit();
?>