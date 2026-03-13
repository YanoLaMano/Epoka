<?php
/**
 * Fonctions utilitaires
 */

function redirect($page, $params = []) {
    $url = 'index.php?page=' . $page;
    foreach ($params as $key => $value) {
        $url .= '&' . urlencode($key) . '=' . urlencode($value);
    }
    header('Location: ' . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['salarie_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login');
    }
}

function canValidate() {
    return isset($_SESSION['peut_valider']) && $_SESSION['peut_valider'];
}

function canPay() {
    return isset($_SESSION['peut_payer']) && $_SESSION['peut_payer'];
}

function isAdmin() {
    return canValidate() && canPay();
}

function flash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDate($date) {
    if (empty($date)) return '';
    return date('d/m/Y', strtotime($date));
}

function formatMoney($amount) {
    return number_format($amount, 2, ',', ' ') . ' €';
}
