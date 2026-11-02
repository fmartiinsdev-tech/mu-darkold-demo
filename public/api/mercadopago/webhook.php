<?php
declare(strict_types=1);
// Etapa de validação: NÃO registra compras nem libera créditos.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function respond(int $status, string $message): void {
    http_response_code($status);
    echo json_encode(['status' => $message], JSON_UNESCAPED_SLASHES);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, 'method_not_allowed');
}
$secret = getenv('MP_WEBHOOK_SECRET');
if (!is_string($secret) || $secret === '') {
    respond(503, 'webhook_not_configured');
}
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';
$requestId = $_SERVER['HTTP_X_REQUEST_ID'] ?? '';
if (!preg_match('/(?:^|,)\\s*ts=([0-9]+)(?:,|$)/', $signature, $tsMatch)
    || !preg_match('/(?:^|,)\\s*v1=([a-f0-9]{64})(?:,|$)/i', $signature, $hashMatch)
    || $requestId === '') {
    respond(401, 'invalid_signature');
}
$timestamp = (int)$tsMatch[1];
if ($timestamp <= 0 || abs(time() - $timestamp) > 300) {
    respond(401, 'expired_signature');
}
$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) > 65536) {
    respond(400, 'invalid_body');
}
$body = json_decode($raw, true);
if (!is_array($body)) {
    respond(400, 'invalid_json');
}
// Para Order API, data.id é o identificador a verificar junto ao Mercado Pago.
// data.id da URL é priorizado quando informado, conforme o modelo de assinatura.
$dataId = $_GET['data_id'] ?? $_GET['data.id'] ?? ($body['data']['id'] ?? '');
if (!is_scalar($dataId) || (string)$dataId === '') {
    respond(400, 'missing_order_id');
}
$dataId = (string)$dataId;
if (strlen($dataId) > 128) {
    respond(400, 'invalid_order_id');
}
$manifest = "id:$dataId;request-id:$requestId;ts:$timestamp;";
$expected = hash_hmac('sha256', $manifest, $secret);
if (!hash_equals($expected, strtolower($hashMatch[1]))) {
    respond(401, 'invalid_signature');
}
// Não reconhecer como processado antes de haver persistência durável,
// consulta da Order no Mercado Pago e crédito idempotente no SQL.
respond(503, 'validated_but_processing_not_enabled');
