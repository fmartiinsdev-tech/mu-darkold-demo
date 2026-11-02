Mu DarkOld — Preparação para Webhooks Mercado Pago

Este pacote preserva a demonstração e adiciona um endpoint de reserva:
https://mu-darkold-demo.onrender.com/api/mercadopago/webhook.php

IMPORTANTE: o endpoint responde HTTP 503 e NÃO processa notificações, NÃO consulta a API Orders e NÃO libera créditos. NÃO configure esta URL no Mercado Pago ainda.

Próxima etapa: implementar verificação da assinatura x-signature, confirmação do pedido via API Orders, registro persistente e idempotente da transação, associação segura à conta e lançamento de créditos no banco real. O banco SQL Server do computador não está conectado ao Render.

Não coloque tokens, senhas ou chaves secretas nos arquivos GitHub.
