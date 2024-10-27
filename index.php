<?php
require_once('funcoes.php') ;
require_once('conexao.php') ;
		$conexao = new banco() ;
		$conexao->conecta() ;
		$pesquisa = NULL ;
		if (isset($_GET['busca']))
		  $pesquisa = $_GET['busca'] ;

		$sqlcode = 'SELECT A.CGM, A.ALUNO, A.E_MAIL, B.Lv2_Login, B.Lv2_Password, B.Lv2_Login1, B.Lv2_Password1, B.Lv2_Login2, B.Lv2_Password2, B.Lv3_Login, B.Lv3_Password, B.Lv3_Login1, B.Lv3_Password1, B.Lv3_Login2, B.Lv3_Password2, C.Lv4_Login, C.Lv4_Password, C.Lv4_Login1, C.Lv4_Password1, C.Lv4_Login2, C.Lv4_Password2   FROM `estudanteseriacao`A
        	       INNER JOIN estudanteloginlv2lv3 B 
                   ON B.ALUNO=A.ALUNO 
            	   LEFT JOIN estudanteloginlv4 C 
            	   ON C.ALUNO=A.ALUNO
            	   WHERE A.E_MAIL="'.$pesquisa.'"' ;
		$dados = null ;
		if ((isset($pesquisa)) &&
			($pesquisa != '') &&
			($pesquisa != NULL)){
		  $conexao->executaSql($sqlcode) ;
		  $dados = $conexao->retornarDados('OBJECT') ;
		} ;  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt_BR" lang="pt_BR">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
        <meta name="keywords" content="" />

		<title>Localiza Estudante TOFAS PR</title>
		
		<link rel="shortcut icon" href="image/favicon.ico" />
		<?php
        loadCSS('style') ;
		/*
		loadCSS('reset') ;
		loadCSS('style') ;
		loadJS('jquery') ;  
		loadJS('geral') ;  
		//loadJS('http://ajax.googleapis.com.ajax/libs/jquery/1.11.1/jquery.min.js', TRUE) ;  
*/		
		?>

	</head>

	<body>
		<h1 align="center">TOFAS MARINGÁ - PR</h1>
		<h2 align="left">Estudante:</h2>
		<form align="center" action="">
			<input name="busca" placeholder="Digite seu email @escola" type="text">
			<button type="subimit">Pesquisar</button>
		</form>	
		<table width="600px" align=center border=1>
			<tr>
				<th>Level 2</th>
			</tr>
			<tr>
				<th>Login</th>
				<th>Password</th>
				<th>Login1</th>
				<th>Password1</th>
				<th>Login2</th>
				<th>Password2</th>
			</tr>
			<tr>
			<?php
			if (($dados == NULL) or
			    ($dados->ALUNO == NULL)){
			
			?>   	
				<td colspan=6> Nenhum registro para apresentar... </td> 
			<?php
				}			
			else {
			?>	
				<td><?php echo $dados->Lv2_Login ; ?></td>
				<td><?php echo $dados->Lv2_Password ; ?></td>
				<td><?php echo $dados->Lv2_Login1 ; ?></td>
			    <td><?php echo $dados->Lv2_Password1 ; ?></td>
				<td><?php echo $dados->Lv2_Login2 ; ?></td>
			    <td><?php echo $dados->Lv2_Password2 ; ?></td>
			<?php
			}
			?>	
			</tr>
			<tr>
				<th>Level 3</th>
			</tr>
			<tr>
				<th>Login</th>
				<th>Password</th>
				<th>Login1</th>
				<th>Password1</th>
				<th>Login2</th>
				<th>Password2</th>
			</tr>
			<tr>
			<?php
			if (($dados == NULL) or
			   ($dados->ALUNO == NULL)){
			?>   	
				<td colspan=6> Nenhum registro para apresentar... </td> 	
			<?php
				}			
			else {
			?>	
				<td><?php echo $dados->Lv3_Login ; ?></td>
				<td><?php echo $dados->Lv3_Password ; ?></td>
				<td><?php echo $dados->Lv3_Login1 ; ?></td>
			    <td><?php echo $dados->Lv3_Password1 ; ?></td>
				<td><?php echo $dados->Lv3_Login2 ; ?></td>
			    <td><?php echo $dados->Lv3_Password2 ; ?></td>
			<?php
			}
			?>	
			</tr>
			<tr>
				<th>Level 4</th>
			</tr>
			<tr>
				<th>Login</th>
				<th>Password</th>
				<th>Login1</th>
				<th>Password1</th>
				<th>Login2</th>
				<th>Password2</th>
			</tr>
			<tr>
			<?php
			if (($dados == NULL) or
			    ($dados->ALUNO == NULL)){
			?>   	
				<td colspan=6> Nenhum registro para apresentar... </td>	
			<?php
				}			
			else {
			?>	
				<td><?php echo $dados->Lv4_Login ; ?></td>
				<td><?php echo $dados->Lv4_Password ; ?></td>
				<td><?php echo $dados->Lv4_Login1 ; ?></td>
			    <td><?php echo $dados->Lv4_Password1 ; ?></td>
				<td><?php echo $dados->Lv4_Login2 ; ?></td>
			    <td><?php echo $dados->Lv4_Password2 ; ?></td>
			<?php
			}
			?>	
				
			</tr>
			
		</table>
			</body>
</html>
