<?php
//BUSCANDO AS CLASSES
include_once "Conexao.class.php";
include_once "Funcoes.class.php";
//CRIANDO A CLASSE
class Funcionario{
	//ATRIBUTOS
	private $con;
	private $objfc;
	private $idFuncionario;
	private $nome;
	private $email;
	private $senha;
	private $dataCadastro;
	//CONSTRUTOR
	public function __construct(){
		$this->con = new Conexao();
		$this->objfc = new Funcoes();
	}
	//METODOS MAGICO
	public function __set($atributo, $valor){
		$this->$atributo = $valor;
	}
	public function __get($atributo){
		return $this->$atributo;
	}
	//METODOS
	
	public function querySeleciona($dado){
		try{
			$this->idFuncionario = $this->objfc->base64($dado, 2);
			$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `nome`, `email`, `data_cadastro` FROM `funcionario` WHERE `idFuncionario` = :idFunc;");
			$cst->bindParam(":idFunc", $this->idFuncionario, PDO::PARAM_INT);
			if($cst->execute()){
				return $cst->fetch();
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function querySelect(){
		try{
			$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `nome`, `email`, `data_cadastro` FROM `funcionario`");
			$cst->execute();
			return $cst->fetchAll();
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function queryInsert($dados){
		try{
			$this->nome = $this->objfc->tratarCaracter($dados['nome'], 1);
			$this->email = $this->objfc->tratarCaracter($dados['email'], 1);
			$this->senha = sha1($dados['senha']);
			$this->dataCadastro = $this->objfc->dataAtual(2);
			$cst = $this->con->conectar()->prepare("INSERT INTO `funcionario` (`nome`, `email`, `senha`, `data_cadastro`) VALUES (:nome, :email, :senha, :data);");
			$cst->bindParam(":nome", $this->nome, PDO::PARAM_STR);
			$cst->bindParam(":email", $this->email, PDO::PARAM_STR);
			$cst->bindParam(":senha", $this->senha, PDO::PARAM_STR);
			$cst->bindParam(":data", $this->dataCadastro, PDO::PARAM_STR);
			if($cst->execute()){
				return 'ok';
			}else{
				return 'Error ao cadastrar';
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function queryUpdade($dados){
		try{
			$this->idFuncionario = $this->objfc->base64($dados['func'], 2);
			$this->nome = $this->objfc->tratarCaracter($dados['nome'], 1);
			$this->email = $dados['email'];
			$cst = $this->con->conectar()->prepare("UPDATE `funcionario` SET `nome` = :nome, `email` = :email WHERE `idFuncionario` = :idFunc;");
			$cst->bindParam(":idFunc", $this->idFuncionario, PDO::PARAM_INT);
			$cst->bindParam(":nome", $this->nome, PDO::PARAM_STR);
			$cst->bindParam(":email", $this->email, PDO::PARAM_STR);
			if($cst->execute()){
				return 'ok';
			}else{
				return 'Error ao alterar';
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function queryDelete($dado){
		try{
			$this->idFuncionario = $this->objfc->base64($dado, 2);
			$cst = $this->con->conectar()->prepare("DELETE FROM `funcionario` WHERE `idFuncionario` = :idFunc;");
			$cst->bindParam(":idFunc", $this->idFuncionario, PDO::PARAM_INT);
			if($cst->execute()){
				return 'ok';
			}else{
				return 'Erro ao deletar';
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMessage();
		}
	}
	
	public function logaFuncionario($dados){
		$this->email = $dados['email'];
		$this->senha = sha1($dados['senha']);
		try{
			$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `email`, `senha` FROM `funcionario` WHERE `email` = :email AND `senha` = :senha;");
			$cst->bindParam(':email', $this->email, PDO::PARAM_STR);
			$cst->bindParam(':senha', $this->senha, PDO::PARAM_STR);
			$cst->execute();
			if($cst->rowCount() == 0){
				header('location: ?login=error');
			}else{
				session_start();
				$rst = $cst->fetch();
				$_SESSION['logado'] = "sim";
				$_SESSION['func'] = $rst['idFuncionario'];
				header("location: admin");
			}
		}catch(PDOException $e){
			return 'Error: '.$e->getMassage();
		}
	}
	
	public function funcionarioLogado($dado){
		$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `nome`, `email` FROM `funcionario` WHERE `idFuncionario` = :idFunc;");
		$cst->bindParam(':idFunc', $dado, PDO::PARAM_INT);
		$cst->execute();
		$rst = $cst->fetch();
		$_SESSION['nome'] = $rst['nome'];
	}
	
	public function sairFuncionario(){
		session_destroy();
		header('location: http://localhost/paginacao-v2');
	}
	
	public function paginacaoFunc($pagina){
		try{
			$html = '';
			// QUANTIDADE DE REGISTRO POR PÁGINA
			$limete = 5;
			// VALOR INICIAL PARA CADA PÁGINA MOSTRAR O REGISTRO
			$inicio = ($limete*$pagina) - $limete;
			// BUSCANDO O TOTAL DE REGISTRO E DIVINDINDO PELO LIMITE DE REGISTRO POR PÁGINA PARA DAR O NÚMERO DE PÁGINAS 
			$ultima_pag = ceil(count($this->querySelect()) / $limete);
			// OPERADOR CONDICIONAL TERNÁRIO
			$get = ($pagina > 1)?('&pag='.$pagina):('');
			$adjacentes = 2;
			// BUSCANDO OS REGISTROS COM INICIO E LIMITE PARA MOSTRAR OS REGISTROS
			$cst = $this->con->conectar()->prepare("SELECT `idFuncionario`, `nome`, `email` FROM `funcionario` ORDER BY `nome` LIMIT :inicio, :limite;");
			$cst->bindParam(":inicio", $inicio, PDO::PARAM_INT);
			$cst->bindParam(":limite", $limete, PDO::PARAM_INT);
			$cst->execute();
			// MOSTRANDO OS REGISTRO 5 POR PÁGINA
			$html .= '<div class="panel-body">';
			foreach($cst->fetchAll() as $rst){
				$html .= '<div class="funcionario">';
                	$html .= '<div class="nome">'.$this->objfc->tratarCaracter($rst['nome'], 2).'</div>';
                	$html .= '<div class="editar"><a href="?acao=edit&func='.$this->objfc->base64($rst['idFuncionario'], 1).''.$get.'" title="Editar dados"><img src="../../img/ico-editar.png" width="16" height="16" alt="Editar"></a></div>';
                	$html .= '<div class="excluir"><a href="?acao=delet&func='.$this->objfc->base64($rst['idFuncionario'], 1).'" title="Excluir esse dado"><img src="../../img/ico-excluir.png" width="16" height="16" alt="Excluir"></a></div>';
            	$html .= '</div>';	
			}
			$html .= '</div>';
			// MONTANDO A PÁGINAÇÃO
			$html .= '<div class="panel-footer altr">';
            	$html .= '<nav aria-label="Page navigation">';
				if($pagina > 1){
					//BT VOLTAR
                	$html .= '<ul class="pagination pagination-sm">';
                    	$html .= '<li><a href="/paginacao-v2/admin/funcionario/?pag='.($pagina - 1).'" title="Voltar" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
					$html .= '</ul>';
				}
				//BT NM PAGINA
                $html .= '<div class="alnNm">';
					if($ultima_pag <= 5){
						// SÓ IRÁ APARECER A QUANTIDADE DE PÁGINAS SE FOR MENOR QUE 5 PÁGINAS (1º PASSO)
						$html .= '<ul class="pagination pagination-sm">';
						for($a = 1; $a < $ultima_pag + 1; $a++){
							if($a == $pagina){
								$html .= '<li class="active"><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';
							}else{
								$html .= '<li><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';	
							}
						}
						$html .= '</ul>';
					}else{
						if($pagina < 1 + (2 * $adjacentes)){
							// SÓ IRÁ APAPACER PARA AS 5 PRIMEIRAS PAGINAS DO TOTAL DE PÁGINAS 
							$html .= '<ul class="pagination pagination-sm">';
							for($a = 1; $a < 2 + (2 *$adjacentes); $a++){
								if($a == $pagina){
									$html .= '<li class="active"><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';
								}else{
									$html .= '<li><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';	
								}
							}
							$html .= '</ul>';
						}else if($pagina > (2 * $adjacentes) && $pagina < $ultima_pag - 3){
							// SÓ IRÁ APARECER O NUMERO DE PÁGINA SE MAIOR QUE 4 PÁGINAS E MENOR QUE 9 PÁGINAS
							$html .= '<ul class="pagination pagination-sm">';
							for($a = $pagina - $adjacentes; $a <= $pagina + $adjacentes; $a++){
								if($a == $pagina){
									$html .= '<li class="active"><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';
								}else{
									$html .= '<li><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';	
								}
							}
							$html .= '</ul>';
						}else{
							// SÓ IRÁ APARECER QUANDO A PÁGINA FOR IGUAL E MAIOR QUE 9
							$html .= '<ul class="pagination pagination-sm">';
							for($a = $ultima_pag - (2 + $adjacentes); $a <= $ultima_pag; $a++){
								if($a == $pagina){
									$html .= '<li class="active"><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';
								}else{
									$html .= '<li><a href="/paginacao-v2/admin/funcionario/?pag='.$a.'">'.$a.'</a></li>';	
								}	
							}
							$html .= '</ul>';
						}
					}
				$html .= '</div>';
                //BT NM PAGINA
				if($pagina < $ultima_pag){
					//BT PROXIMO
                	$html .= '<ul class="pagination pagination-sm mgn"> '; 
                    	$html .= '<li><a href="/paginacao-v2/admin/funcionario/?pag='.($pagina + 1).'" title="Próximo" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
					$html .= '</ul>';
				}
				$html .= '</nav>';
        	$html .= '</div>'; 
			
			return $html;
			
		}catch(PDOException $ex){
			return 'Error: '.$ex->getMassage();
		}
	}
	
}
?>