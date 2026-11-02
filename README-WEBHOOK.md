# Mu DarkOld — Webhook: validação preparada

Esta versão preserva a demonstração e adiciona verificação HMAC SHA-256 das notificações de Orders.

**Ainda não configurar a URL no Mercado Pago.** Mesmo uma notificação válida recebe HTTP 503,
pois não há armazenamento persistente, consulta da Order ao Mercado Pago, nem liberação
idempotente de créditos. Isso evita confirmar um pagamento que não foi processado.

Quando for implementar o processamento:
1. Definir `MP_WEBHOOK_SECRET` nas variáveis secretas do Render (nunca no GitHub).
2. Confirmar o formato do webhook real da API de Orders e testar a assinatura.
3. Buscar a Order pela API do Mercado Pago com token protegido, conferir status,
   moeda, valor, conta destinatária e referência da compra.
4. Registrar cada Order ID como único em banco persistente, com transação e controle
   de repetição antes de creditar a conta.
5. Somente depois responder HTTP 2xx e cadastrar o endereço do webhook no painel.

A página pública e os saldos permanecem fictícios. Nenhum token ou segredo está neste pacote.
