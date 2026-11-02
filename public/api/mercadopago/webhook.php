<?php
// Mu DarkOld — endpoint reservado para a integração segura do Mercado Pago.
// Nenhuma notificação gera créditos nesta etapa.
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
http_response_code(503);
echo json_encode(['status'=>'not_configured','message'=>'Webhook ainda nao habilitado. Nenhum credito sera liberado.']);
