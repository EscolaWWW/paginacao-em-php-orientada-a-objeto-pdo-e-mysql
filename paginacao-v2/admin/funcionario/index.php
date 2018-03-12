<?php
//BUSCANDO A CLASSE
require_once "../../classes/Funcionario.class.php";
require_once "../../classes/Funcoes.class.php";
//ESTANCIANDO A CLASSE
$objFunc = new Funcionario();
$objFc = new Funcoes();
//VALIDANDO USUARIO
session_start();
if($_SESSION["logado"] == "sim"){
	$objFunc->funcionarioLogado($_SESSION['func']);
}else{
	header("location: /paginacao-v2"); 
}
if(isset($_GET['sair']) == "sim"){
	$objFunc->sairFuncionario();
}
//CADASTRANDO O FUNCIONARIO
if(isset($_POST['btCadastrar'])){
	if($objFunc->queryInsert($_POST) == 'ok'){
		header('location: /paginacao-v2/admin/funcionario');
	}else{
		echo '<script type="text/javascript">alert("Erro em cadastrar");</script>';
	}
}
//ALTERANDO OS DADOS DO FUNCIONARIO
if(isset($_POST['btAlterar'])){
	if($objFunc->queryUpdade($_POST) == 'ok'){
		$get = (!empty($_GET['pag']))?('&pag='.$_GET['pag']):('');
		header('location: ?acao=edit&func='.$_GET['func'].''.$get);
	}else{
		echo '<script type="text/javascript">alert("Erro em atualizar");</script>';
	}
}
//SELECIONADO O FUNCIONARIO
if(isset($_GET['acao'])){
	switch($_GET['acao']){
		case 'edit': $func = $objFunc->querySeleciona($_GET['func']); break;
		case 'delet': 
			if($objFunc->queryDelete($_GET['func']) == 'ok'){
				header('location: /paginacao-v2/admin/funcionario');
			}else{
				echo '<script type="text/javascript">alert("Erro em deletar");</script>';
			}
				break;
	}
}
?>
<!DOCTYPE HTML>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<title>Formulário de cadastro</title>
    <link href="../../bootstrap-3.3.7-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
	<link href="../../bootstrap-3.3.7-dist/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" media="all">
    <link href="../css/estilo-funcionario.css" rel="stylesheet" type="text/css" media="all">
	<script src="../../bootstrap-3.3.7-dist/js/bootstrap.min.js" type="text/javascript"></script>
</head>
<body>

<nav class="navbar navbar-inverse navbar-radius">
  <div class="container-fluid">
    <ul class="nav navbar-nav">
      <li><a href="../../../paginacao-v2/admin">Home</a></li>
      <li class="active"><a href="#">Funcionários</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="#"><span class="glyphicon glyphicon-user"></span> <?=$_SESSION['nome']?></a></li>
      <li><a href="/paginacao-v2/admin/funcionario/?sair=sim"><span class="glyphicon glyphicon-log-out"></span> Sair</a></li>
    </ul>
  </div>
</nav>
<!-- PAINEL LISTA DE FUNCIONARIO -->
<div id="lista">
    <div class="panel panel-primary">
        <!-- CABECARIO -->
        <div class="panel-heading">
        	<h3 class="panel-title">Lista</h3>
        	<a href="/paginacao-v2/admin/funcionario" title="Cadastrar Funcionário"><img src="../../img/if_user-add_216490.png" width="32" height="32" alt="Cadastrar"></a>
        </div>	
        <!-- //CABECARIO -->
        <!-- OPERADOR CONDICIONAL TERNÁRIO -->
        <?=$objFunc->paginacaoFunc((!empty($_GET['pag']))?$_GET['pag']:1)?>             
    </div>
</div>
<!-- PAINEL LISTA DE FUNCIONARIO -->

<div id="formulario">
	<form name="formCad" action="" method="post">
        <input class="form-control" name="nome" type="text" required  placeholder="Nome:" value="<?=$objFc->tratarCaracter((isset($func['nome']))?($func['nome']):(''), 2)?>"><br>        
        <input type="mail" name="email" class="form-control" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"  placeholder="E-mail:" value="<?=$objFc->tratarCaracter((isset($func['email']))?($func['email']):(''), 2)?>"><br>
        <?php if(isset($_GET['acao']) <> 'edit'){ ?>
        <input type="password" name="senha" class="form-control" required placeholder="Senha:"><br>
        <?php } ?>
        <button type="submit" name="<?=(isset($_GET['acao']) == 'edit')?('btAlterar'):('btCadastrar')?>" class="btn btn-primary btn-block"><?=(isset($_GET['acao']) == 'edit')?('Alterar'):('Cadastrar')?></button>        
        <input type="hidden" name="func" value="<?=(isset($func['idFuncionario']))?($objFc->base64($func['idFuncionario'], 1)):('')?>">
    </form>
</div>
 
</body>
</html>
