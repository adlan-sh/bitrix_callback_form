<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule('form');

header('Content-Type: application/json');

if (!check_bitrix_sessid()) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid session']);
    exit;
}

$formId = (int)($_POST['FORM_ID'] ?? 0);
$data = $_POST['data'] ?? [];

if (!$formId || empty($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

$result = CFormResult::Add($formId, $data);

if ($result) {
    echo json_encode(['success' => true, 'resultId' => $result]);
} else {
    global $strError;
    http_response_code(500);
    echo json_encode(['error' => $strError ?: 'Ошибка отправки']);
}