<?php
session_start();
$_SESSION['balance'] ??= 0;
$_SESSION['cash'] ??= 2500;
$_SESSION['vip'] ??= 0;
$_SESSION['expiry'] ??= null;
$_SESSION['history'] ??= [];
$_SESSION['plans'] ??= [['name'=>'VIP Prata','level'=>1,'days'=>30,'cost'=>20,'cash_cost'=>2000],['name'=>'VIP Ouro','level'=>2,'days'=>30,'cost'=>40,'cash_cost'=>null]];
$_SESSION['rate'] ??= 1;
$_SESSION['demo_user'] ??= false;
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
$page=$_GET['p']??'home';$pages=['home'=>'Principal','login'=>'Login','register'=>'Cadastro','downloads'=>'Download','ranking'=>'Ranking','guilds'=>'Guilds','panel'=>'Painel VIP','admin'=>'Administração'];if(!isset($pages[$page]))$page='home';
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!hash_equals($_SESSION['csrf'],(string)($_POST['csrf']??''))){$msg='Sessão inválida. Atualize a página.';}
 else{
 $action=$_POST['action']??'';
 if($action==='login'){$_SESSION['demo_user']=true;$msg='Login demonstrativo realizado.';$page='panel';}
 elseif($action==='logout'){$_SESSION['demo_user']=false;$page='home';}
 elseif($action==='pix'&&$_SESSION['demo_user']){$credits=max(0,min(100000,(int)($_POST['credits']??0)));if($credits>0){$_SESSION['balance']+=$credits;$_SESSION['history'][]='PIX simulado: +'.$credits.' créditos';$msg='Pagamento SIMULADO aprovado: +'.$credits.' créditos. Nenhum PIX real foi gerado.';}}
 elseif($action==='vip'&&$_SESSION['demo_user']){
  $i=filter_var($_POST['plan']??null,FILTER_VALIDATE_INT);
  $plan=($i!==false && $i!==null)?($_SESSION['plans'][$i]??null):null;
  $currency=$_POST['currency']??'credits';
  if(!$plan){$msg='Plano inválido.';}
  elseif(!in_array($currency,['credits','cash'],true)){$msg='Forma de pagamento inválida.';}
  elseif($currency==='cash' && ($plan['level']!==1 || $plan['cash_cost']===null)){$msg='Cash disponível somente para VIP Prata.';}
  else{
   $cost=$currency==='cash'?(int)$plan['cash_cost']:(int)$plan['cost'];
   $wallet=$currency==='cash'?'cash':'balance';
   if($_SESSION[$wallet]<$cost){$msg='Saldo insuficiente de '.($currency==='cash'?'Cash':'créditos').'.';}
   else{
    $oldVip=(int)$_SESSION['vip'];
    $oldExpiry=$_SESSION['expiry'];
    $base=($oldVip===(int)$plan['level'] && $oldExpiry && strtotime($oldExpiry)>time())?strtotime($oldExpiry):time();
    $_SESSION[$wallet]-=$cost;
    $_SESSION['vip']=$plan['level'];
    $_SESSION['expiry']=date('Y-m-d',strtotime('+'.$plan['days'].' days',$base));
    $_SESSION['history'][]=$plan['name'].' -'.$cost.' '.($currency==='cash'?'Cash':'créditos').' (simulado)';
    $msg=$plan['name'].' ativado em modo demonstração!';
   }
  }
 }
 elseif($action==='admin'){$i=(int)($_POST['index']??-1);if(isset($_SESSION['plans'][$i])){$_SESSION['plans'][$i]['cost']=max(1,min(100000,(int)($_POST['cost']??1)));$_SESSION['plans'][$i]['days']=max(1,min(3650,(int)($_POST['days']??1)));if($i===0){$_SESSION['plans'][$i]['cash_cost']=max(1,min(10000000,(int)($_POST['cash_cost']??2000)));}$msg='Plano atualizado nesta sessão de demonstração.';}}
 }
}
$logged=$_SESSION['demo_user'];$csrf=$_SESSION['csrf'];
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Mu DarkOld 97d — <?=e($pages[$page])?></title><link rel="stylesheet" href="assets/style.css"><style>.demo-tag{background:#ffe7a0;border:1px solid #c79b28;padding:8px;text-align:center;font-weight:bold}.button-link{display:inline-block;padding:9px 14px;background:#7b1515;color:white;text-decoration:none}.mini{font-size:11px;color:#555}.admin-form{display:flex;gap:8px;align-items:end;flex-wrap:wrap}.admin-form label{flex:1;min-width:85px}.admin-form button{margin-bottom:12px}.price{font-size:17px;font-weight:bold;color:#971414}.plan{border:1px solid #aaa;padding:10px;margin:10px 0;background:#f6f6f6}.banner{max-width:100%;display:block;margin:auto}.hero{background:linear-gradient(90deg,#121212dd,#121212aa),url('assets/fantasy.jpg') center 35%/cover}.hero h1{font-size:46px}.hero p{color:#ddd}.panel-stat{background:#eee;padding:10px;margin-bottom:10px;border:1px solid #ccc}</style></head><body><div class="site"><header class="hero"><span class="sr-only">Mu DarkOld — MU Online Private Server 97d</span></header><nav><?php foreach(['home','register','downloads','ranking','guilds','login','panel'] as $p):?><a class="<?=($page===$p?'active':'')?>" href="?p=<?=e($p)?>"><?=e($pages[$p])?></a><?php endforeach;?></nav><div class="demo-tag">DEMONSTRAÇÃO — créditos e Cash fictícios • sem PIX real • sem SQL Server</div><?php if($page!=='home'):?><div class="section-title"><?=e($pages[$page])?></div><?php endif;?><?php if($msg):?><div class="notice"><?=e($msg)?></div><?php endif;?><div class="columns"><aside><h3>::: Dados do servidor</h3><p>» Nome: <b>Mu DarkOld</b></p><p>» Versão: <b class="red">97d</b></p><p>» Experiência: <b class="red">A definir</b></p><p>» Drop: <b class="red">A definir</b></p><p>» Servidor I: <b class="green">DEMO</b></p><p>» Jogadores online: <b class="red">—</b></p><h3 class="side-head">Contas VIP e VIP Gold</h3><p><a href="?p=panel">Meu painel / Comprar VIP</a></p><p><a href="?p=ranking">Ranking de jogadores</a></p><p><a href="?p=downloads">Central de downloads</a></p><h3>» Área administrativa</h3><p><a href="?p=admin">Configurar preços VIP</a></p><p class="mini">Acesso administrativo apenas demonstrativo; não use este pacote com dados reais.</p></aside><main>
<?php if($page==='home'):?><a class="shop" href="?p=panel"><span class="cart">🛒</span><span>Clique aqui para acessar<br>a nossa Loja de Itens</span></a><h3>::: Mural de Notícias</h3><article><h4>Bem-vindo ao Mu DarkOld!</h4><p>Experimente o painel de jogador, a compra de créditos simulada, VIP Prata por R$ 20 ou 2.000 Cash e VIP Ouro por R$ 40, ambos por 30 dias.</p></article><article><h4>Teste todas as funções</h4><p>Faça login demonstrativo e configure os preços no painel administrativo. Os dados são temporários e ficam somente na sessão.</p></article><a class="button-link" href="?p=panel">Testar painel VIP</a>
<?php elseif($page==='login'||$page==='register'):?><h3>» Acesso de demonstração</h3><p>Use o botão abaixo para entrar em uma conta fictícia. Nenhuma senha é necessária.</p><form method="post"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="login"><button>Entrar como jogador de teste</button></form>
<?php elseif($page==='panel'):?><h3>» Painel do Jogador</h3><?php if(!$logged):?><p>Faça login na conta de demonstração para testar compras.</p><a class="button-link" href="?p=login">Entrar</a><?php else:?><div class="panel-stat">Conta: <b>DarkOldTeste</b><p>Créditos disponíveis: <span class="price"><?=e($_SESSION['balance'])?></span></p><p>Cash do jogo (fictício): <span class="price"><?=e($_SESSION['cash'])?></span></p><p>VIP: <b><?=['Normal','Prata','Ouro'][$_SESSION['vip']]?></b><?php if($_SESSION['expiry']):?> • Vence em <?=e($_SESSION['expiry'])?><?php endif;?></p></div><h3>» Simular compra de créditos por PIX</h3><p class="mini">Aprovação instantânea fictícia, sem QR Code ou transferência financeira.</p><form method="post"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="pix"><label>Créditos<select name="credits"><option value="20">R$ 20,00 → 20 créditos</option><option value="40">R$ 40,00 → 40 créditos</option><option value="60">R$ 60,00 → 60 créditos</option><option value="100">R$ 100,00 → 100 créditos</option></select></label><button>Simular pagamento PIX</button></form><h3>» Comprar VIP</h3><?php foreach($_SESSION['plans'] as $i=>$plan):?><div class="plan"><b><?=e($plan['name'])?></b><p><?=e($plan['days'])?> dias — <span class="price">R$ <?=e(number_format($plan['cost'],2,',','.'))?> (<?=e($plan['cost'])?> créditos)</span><?php if($plan['level']===1):?><p>Ou <?=e($plan['cash_cost'])?> Cash do jogo</p><?php endif;?></p><form method="post"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="vip"><input type="hidden" name="plan" value="<?=$i?>"><input type="hidden" name="currency" value="credits"><button>Comprar com créditos</button></form><?php if($plan['level']===1):?><form method="post"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="vip"><input type="hidden" name="plan" value="<?=$i?>"><input type="hidden" name="currency" value="cash"><button>Comprar com <?=e($plan['cash_cost'])?> Cash</button></form><?php endif;?></div><?php endforeach;?><h3>» Histórico</h3><?php foreach(array_reverse($_SESSION['history']) as $entry):?><p>• <?=e($entry)?></p><?php endforeach;?><form method="post"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="logout"><button>Sair</button></form><?php endif;?>
<?php elseif($page==='admin'):?><h3>» Administração demonstrativa</h3><p>Altere os preços e prazos para testar como aparecem no painel do jogador.</p><?php foreach($_SESSION['plans'] as $i=>$plan):?><div class="plan"><b><?=e($plan['name'])?></b><form class="admin-form" method="post"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="action" value="admin"><input type="hidden" name="index" value="<?=$i?>"><label>Preço (créditos)<input type="number" min="1" name="cost" value="<?=e($plan['cost'])?>"></label><?php if($i===0):?><label>Preço em Cash<input type="number" min="1" name="cash_cost" value="<?=e($plan['cash_cost'])?>"></label><?php endif;?><label>Duração (dias)<input type="number" min="1" max="3650" name="days" value="<?=e($plan['days'])?>"></label><button>Salvar</button></form></div><?php endforeach;?><p class="mini">Sem autenticação administrativa real. Publicar apenas como demonstração sem dados reais.</p>
<?php elseif($page==='ranking'):?><h3>» Top 100 Players (demonstração)</h3><table><tr><th>#</th><th>Personagem</th><th>Classe</th><th>Level</th></tr><?php foreach([['DarkKnight','DK',400],['SoulMaster','SM',389],['FairyElf','Elf',371],['MagicGlad','MG',350]] as $i=>$r):?><tr><td><?=$i+1?></td><td><?=e($r[0])?></td><td><?=e($r[1])?></td><td><?=e($r[2])?></td></tr><?php endforeach;?></table>
<?php elseif($page==='guilds'):?><h3>» Guilds (fictícias)</h3><table><tr><th>#</th><th>Guild</th><th>Master</th></tr><tr><td>1</td><td>DarkOld</td><td>DarkKnight</td></tr><tr><td>2</td><td>Legends</td><td>SoulMaster</td></tr></table>
<?php elseif($page==='downloads'):?><h3>» Central de Downloads</h3><article><h4>Cliente Mu DarkOld 97d</h4><p>Link do cliente será configurado quando o site for conectado ao servidor real.</p></article><?php endif;?></main></div><footer><div class="footer-art" role="img" aria-label="Mu DarkOld — The Best MMORPG Game"></div><div class="copyright">© 2026 Mu DarkOld • MU Online 97d • Protótipo de demonstração</div></footer></div></body></html>
