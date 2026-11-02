# Mu DarkOld — Demonstração no Render

Demonstração PHP 8.2 + Apache com Dockerfile na raiz.

## Atenção
- Apenas dados fictícios, sem Mercado Pago, SQL Server ou PIX real.
- O painel de administração é público e as alterações ficam somente na sessão de cada visitante.
- Não utilize credenciais reais, senhas, informações financeiras ou dados de jogadores.

## Publicação
No GitHub, crie um repositório `mu-darkold-demo` e envie o conteúdo desta pasta para a raiz (Dockerfile, README.md e pasta public). No Render, crie um Web Service usando esse repositório, runtime Docker, plano Free. O serviço Apache expõe a porta 80.


## Valores VIP — modo demonstração
- VIP Prata (vip=1): 30 dias, 20 créditos simulados (equivalentes a R$20) **ou** 2.000 Cash fictícios.
- VIP Ouro (vip=2): 30 dias, 40 créditos simulados (equivalentes a R$40); Cash não é aceito.
- O jogador fictício começa com 0 créditos e 2.500 Cash para testar os dois fluxos.
- O painel admin permite ajustar preços e dias apenas na sessão do navegador.
- Esta versão **não acessa** `MEMB_INFO`, não processa PIX e não movimenta Cash real.
- Não usar o protótipo como sistema de produção; o painel admin não tem autenticação real.
