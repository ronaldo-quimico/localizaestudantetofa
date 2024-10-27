<?php
require_once('config.php') ;
protegeArquivo(basename(__FILE__)) ;

function loadCSS($arquivo=NULL,$media='screen',$import=FALSE){
	if ($arquivo != NULL):
	    if ($import==TRUE):
		    echo '<style type="text/css">@import url("'.CSSPATH.$arquivo.'.css");</style>'."\n        " ;
		else:
			echo '<link rel="stylesheet" type="text/css" href="'.CSSPATH.$arquivo.'.css" media="'.$media.'" />'."\n        " ;	
		endif ;
	endif ;	
}//loadCSS

function printMSG($msg=NULL, $tipo=NULL, $Botao1=NULL, $Botao2=NULL){
	if ($msg!=NULL):
		//$msg = utf8_decode($msg) ;
		if ($Botao1!=NULL):
			$Botao1 = '<input type="button" onclick="'.$Botao1.'" value="Sim" />' ;
		endif ;	
		if ($Botao2!=NULL):
			$Botao2 = '<input type="button" onclick="'.$Botao2.'" value="N�o" />' ;	
		endif ;	
		switch ($tipo):
			case 'erro':
				echo '<div class="erro">'.$msg.'</div>' ;
				break ;
			case 'alerta':
				echo '<div class="alerta">'.$msg.'</div>' ;
				break ;
			case 'pergunta':
				echo '<div class="pergunta">'.$msg.$Botao1.$Botao2.'</div>' ;
				break ;
			case 'sucesso':
				echo '<div class="sucesso">'.$msg.'</div>' ;
				break ;
			default:
				echo '<div class="sucesso">'.$msg.'</div>' ;
				break ;
		endswitch ;
	endif ;	
}//printMSG

function redireciona($url="", $base = "BASEURL"){
	if ($base == "BASEURL")
		header("location: ".BASEURL.$url) ; 
	else if ($base == "BASEPAINEL")
		header("location: ".ADMURL.$url) ; 
	else if ($base == "BASETEMP")
		header("location: ".TEMP.$url) ;
}//redireciona

function protegeArquivo($nomeArquivo,$redirPara='index.php?erro=3'){
	$url = $_SERVER["PHP_SELF"] ;
	if (preg_match("/$nomeArquivo/i", $url)):
	    redireciona($redirPara) ; 
    endif ;
}//protegeArquivo


?>