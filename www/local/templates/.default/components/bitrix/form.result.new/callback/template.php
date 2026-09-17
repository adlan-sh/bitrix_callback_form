<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>

<?php
use Bitrix\Main\Loader;
Loader::includeModule('form');

$formId = (int)(
    $arParams['WEB_FORM_ID']
    ?? $arResult['arForm']['ID']
    ?? $arResult['FORM_ID']
    ?? 0
);
print_r($formId);

$questions = [];
$res = CFormField::GetList($formId, 'N', 's_id', 'asc');

while ($field = $res->Fetch()) {
    $questions[] = [
        'id'       => (int)$field['ID'],
        'sid'      => $field['SID'],
        'caption'  => $field['TITLE'],
        'required' => ($field['REQUIRED'] ?? 'N') === 'Y',
        'type'     => strtolower($field['FIELD_TYPE'] ?? 'text'),
    ];
}
?>

<link rel="stylesheet" href="/local/js/callback-form/dist/style.css">

<div id="callback-form-root"></div>

<script>
    window.CALLBACK_FORM_DATA = <?=json_encode([
        'formId'    => $formId,
        'sessid'    => bitrix_sessid(),
        'submitUrl' => '/local/ajax/submit-callback.php',
        'questions' => $questions,
    ], JSON_UNESCAPED_UNICODE)?>;
</script>

<script src="/local/js/callback-form/dist/callback-form.js"></script>