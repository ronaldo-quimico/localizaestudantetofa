<?php
require_once('funcoes.php') ;
protegeArquivo(basename(__FILE__)) ;
class banco{
	// propriedades 
	public $servidor       = DBHOST ;
	public $usuario        = DBUSER ;
	public $senha          = DBPASS ;
	public $nomebanco      = DBNAME ;
//	public $nomebanco ;
	public $dataset        = NULL ;
	public $conexao        = NULL ;
	public $linhasafetadas = -1 ;
	public $ultimoRegistro = 0 ;
	public $deuErro        = false ;
    public $sqlText        = "" ;
// métodos
	public function __construct(){
//		$this->conecta() ;
	}//construct
	
	public function __destruct(){
	    if ($this->conexao != NULL):
		     mysql_close($this->conexao) ;
		endif ;		
	}//destruct
	
	public function setNomeBanco($banco){
		$this->nomebanco = $banco ;
	}//setNvars

	public function getNomeBanco(){
		return $this->nomebanco ;
	}//getNvars

	public function gravarTransacao(){
		return ibase_commit($this->conexao) ;
	}//gravarTransacao
	
	public function desfazerTransacao(){
		return ibase_rollback($this->conexao) ;
	}//desfazerTransacao
	
	public function conecta(){
	    $this->conexao = mysql_connect($this->servidor,$this->usuario,$this->senha,TRUE) or die ($this->trataerro(__FILE__,__FUNCTION__,mysql_errno(),mysql_error(),TRUE)) ;
		mysql_select_db($this->nomebanco) or die ($this->trataerro(__FILE__,__FUNCTION__,mysql_errno(),mysql_error(),TRUE)) ;
		mysql_query("SET NAMES 'utf8'") ;	
		mysql_query("SET character_set_connection=utf8") ;
		mysql_query("SET character_set_connection=utf8") ;
		mysql_query("SET character_set_client=utf8") ;
		mysql_query("SET character_set_results=utf8") ;
	}//conecta
	
	public function inserirRegistro($objeto){
		$sql    = " INSERT INTO ".$objeto->tabela." (" ;
		$values = " VALUES (" ;
		
		for ($i=0; $i<count($objeto->campos_valores); $i++):
		      $sql    .= key($objeto->campos_valores) ;
			  $values .= is_numeric($objeto->campos_valores[key($objeto->campos_valores)]) ?
			             $objeto->campos_valores[key($objeto->campos_valores)] :
						 "'".$objeto->campos_valores[key($objeto->campos_valores)]."'" ;
			if ($i<count($objeto->campos_valores)-1):
				$sql    .= ", " ;
				$values .= ", " ;
			else:
				$sql    .= ") " ;
				$values .= ") " ;
			endif;		
			next ($objeto->campos_valores) ;
		endfor ;	
		$sql .= $values ;
		
		$result = $this->executaSql($sql) ;	
		if ($this->linhasafetadas > -1)
			$this->ultimoRegistro = $this->retornarUltimoRegistro('inserir', $objeto->tabela, $objeto->valorpk) ;
		$this->sqlText = $sql ;
		return $result ;
	 	
	}//inserir
	
	public function atualizarRegistro($objeto){
		$sql = " UPDATE ".$objeto->tabela." SET " ;
		for ($i=0; $i<count($objeto->campos_valores); $i++):
		      $sql .= key($objeto->campos_valores)."=" ;
			  $sql .= is_numeric($objeto->campos_valores[key($objeto->campos_valores)]) ?
			          $objeto->campos_valores[key($objeto->campos_valores)] :
					  "'".$objeto->campos_valores[key($objeto->campos_valores)]."'" ;
			if ($i<count($objeto->campos_valores)-1):
				$sql .= ", " ;
			else:
				$sql .= " " ;
			endif;		
			next ($objeto->campos_valores) ;
		endfor ;
		if (is_array($objeto->campopk)):
			for ($i = 0 ; $i < count($objeto->campopk) ; $i++):
				is_numeric($objeto->valorpk[$i]) ? $objeto->valorpk[$i] = $objeto->valorpk[$i] : $objeto->valorpk[$i] = "'".$objeto->valorpk[$i]."'" ;
				if (strstr($sql, "WHERE")):
					$sql .= ' AND '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				else:
					$sql .= 'WHERE '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				endif ;	
			endfor ;
		else:				
			$sql .= " WHERE ".$objeto->campopk."=" ;
			$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'" ;
		endif ;
		$result = $this->executaSql($sql) ;	
		if ($this->linhasafetadas > -1)
			$this->ultimoRegistro = $this->retornarUltimoRegistro('atualizar', $objeto->tabela, $objeto->valorpk) ;
		return $result ;	
	}//atualizar
	
	public function deletarRegistro($objeto){
		$sql = " DELETE FROM ".$objeto->tabela." " ;
		if (is_array($objeto->campopk)):
			for ($i = 0 ; $i < count($objeto->campopk) ; $i++):
				is_numeric($objeto->valorpk[$i]) ? $objeto->valorpk[$i] = $objeto->valorpk[$i] : $objeto->valorpk[$i] = "'".$objeto->valorpk[$i]."'" ;
				if (strstr($sql, "WHERE")):
					$sql .= ' AND '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				else:
					$sql .= 'WHERE '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				endif ;	
			endfor ;
		else:				
			$sql .= " WHERE ".$objeto->campopk."=" ;
			$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'" ;
		endif ;
		return $this->executaSql($sql) ;			
	}//deletar
	
	public function executaSql($sql=NULL){
		$this->deuErro = false ;
		$this->conecta() ;
		
		if ($sql !== NULL):
			$this->sqlText = $sql ;
			$query = mysql_query($sql) or $this->trataerro(__FILE__, __FUNCTION__,mysql_errno(),mysql_error(),TRUE) ;
			$this->linhasafetadas = mysql_affected_rows($this->conexao) ;
		    if (substr(trim(strtoupper($sql)),0,6) === 'SELECT'):
			 	$this->dataset = $query ;
			    return $query ;
			else:
				return $this->linhasafetadas ;
			endif ;	 
        else:
        	$this->sqlText = "" ;
			$this->trataerro(__FILE__, __FUNCTION__,NULL,'Comando SQL não informado na rotina', TRUE) ;
		endif ;	 
	}//executaSql
	
	public function selecionarTudo($objeto){
		if ($objeto->distinct):	
			$sql = "SELECT DISTINCT * FROM ".$objeto->tabela ;
		else:	 
			$sql = "SELECT * FROM ".$objeto->tabela ;
		endif ;	
		if ($objeto->indice != NULL):
			$sql .= " USE INDEX(".$objeto->indice.") " ;
		endif ;
		if ($objeto->extras_select != NULL):
			$sql .= " ".$objeto->extras_select ;
		endif ;
		if (is_array($objeto->campopk)):
			for ($i = 0 ; $i < count($objeto->campopk) ; $i++):
				if (is_array($objeto->valorpk)):
					$objeto->valorpk[$i] = $objeto->valorpk[$i] ;
				else:
					$objeto->valorpk[$i] = $objeto->valorpk ;
				endif ;	
				is_numeric($objeto->valorpk[$i]) ? $objeto->valorpk[$i] = $objeto->valorpk[$i] : $objeto->valorpk[$i] = "'".$objeto->valorpk[$i]."'" ;
				if ((strstr($sql, "WHERE")) &&
					(trim($objeto->valorpk[$i]) !== "") &&
					(trim($objeto->valorpk[$i]) !== "''") &&
					($objeto->valorpk[$i] !== NULL)):
					$sql .= ' AND '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				elseif ((trim($objeto->valorpk[$i]) !== '') &&
						(trim($objeto->valorpk[$i]) !== "''") &&
						($objeto->valorpk[$i] !== NULL)):
					$sql .= ' WHERE '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				endif ;	
			endfor ;
		else:				
			if ((strstr($sql, "WHERE")) &&
				(trim($objeto->valorpk) !== '') &&
				(trim($objeto->valorpk) !== "''") &&
				($objeto->valorpk !== NULL)):
				$sql .= " AND ".$objeto->campopk."=" ;
				$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'" ;
			elseif ((trim($objeto->valorpk) !== '') &&
					(trim($objeto->valorpk) !== "''") &&
					($objeto->valorpk !== NULL)):
				$sql .= " WHERE ".$objeto->campopk."=" ;
				$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'" ;
			endif ;		
		endif ;
		if ($objeto->ordenar != NULL):
			$Pos = strpos(strtoupper($objeto->ordenar), 'ORDER BY') ;
			if ($Pos === FALSE)  
				$sql .= " ORDER BY ".$objeto->ordenar." " ;
			else
				$sql .= " ".$objeto->ordenar." " ;
		endif ;
		return $this->executaSql($sql) ;
	}//selecionarTudo
	
	public function selecionarCampos($objeto){
		$sql     = '' ;
		$join    = '' ;
		$Auxjoin = '' ;
		foreach($objeto->camposPrinc as $Key):
			if (($sql != '')&&
				($sql != NULL)):
				$sql .= ", " ;
			endif;
			if ((isset($Key['BUSCAR_CAMPO']))&&
			    ($Key['BUSCAR_CAMPO'] != '')&&
			 	($Key['BUSCAR_CAMPO'] != NULL)):
				if (($Key['TABELA'] != '')&&
					($Key['TABELA'] != NULL)):
					if (strpos($Key['CAMPO'], '+')):
						$Keyarr = explode('+', $Key['CAMPO']) ;
						$i = 0 ;
						foreach($Keyarr as $arr):
							$i = $i + 1 ;
							if ($i>1)
								$sql  .= ', '.strtoupper($Key['TABELA']).'.'.
						   	 			 strtoupper($Key['BUSCAR_CAMPO']).' '.
							 			 strtoupper($arr) ;
							else	 
								$sql  .= strtoupper($Key['TABELA']).'.'.
						   	 			 strtoupper($Key['BUSCAR_CAMPO']).' '.
							 			 strtoupper($arr) ; 
						endforeach ;
					else:	
						$sql  .= strtoupper($Key['TABELA']).'.'.
						   	 	 strtoupper($Key['BUSCAR_CAMPO']).' '.
							 	 strtoupper($Key['CAMPO']) ;
						$Auxjoin .= ' LEFT JOIN '.strtoupper($Key['TABELA']).' ON '.
									strtoupper($objeto->tabela).'.'.strtoupper($Key['CAMPO_CONSULTA']).'='.
							    	strtoupper($Key['TABELA']).'.'.strtoupper($Key['CAMPO_TABELA']) ;
						$Pos = strpos($join, $Auxjoin) ;
						if ($Pos === FALSE):  
					    	$join .= $Auxjoin ;
						endif ;
					endif ;		
				else:		
				 	if (strpos($Key['CAMPO'], '+')):
						$Keyarr = explode('+', $Key['CAMPO']) ;
						$i = 0 ;
						foreach($Keyarr as $arr):
							$i = $i + 1 ;
							if ($i>1)
								$sql  .= ', '.strtoupper($objeto->tabela).'.'.
							 			 strtoupper($arr) ;
							else	 
								$sql  .= strtoupper($objeto->tabela).'.'.
							 			 strtoupper($arr) ; 
						endforeach ;
					else:	
						$sql .= strtoupper($objeto->tabela).'.'.strtoupper($Key['CAMPO']) ;
					endif ;	
				endif;
			else:			
	     	 	if (strpos($Key['CAMPO'], '+')):
					$Keyarr = explode('+', $Key['CAMPO']) ;
					$i = 0 ;
					foreach($Keyarr as $arr):
						$i = $i + 1 ;
						if ($i>1)
							$sql  .= ', '.strtoupper($objeto->tabela).'.'.
						 			 strtoupper($arr) ;
						else	 
							$sql  .= strtoupper($objeto->tabela).'.'.
						 			 strtoupper($arr) ; 
					endforeach ;
				else:	
					$sql .= strtoupper($objeto->tabela).'.'.strtoupper($Key['CAMPO']) ;
				endif ;	
			endif ;			
		endforeach ;	
		if ($sql !== ''):
			if ($objeto->distinct):	
		        $sql = 'SELECT DISTINCT '.$sql.' ' ;
			else:	
				$sql = 'SELECT '.$sql.' ' ;
			endif ;	
		else:
			if ($objeto->distinct):	
				$sql = 'SELECT DISTINCT * ' ;
			else:
				$sql = 'SELECT * ' ;
			endif ;		
		endif ;		
		$sql .= " FROM ".$objeto->tabela ;
		if ($objeto->indice != NULL):
			$sql .= " USE INDEX(".$objeto->indice.") " ;
		endif ;
		$sql .= $join ;
		if ($objeto->extras_select != NULL):
			$sql .= " ".$objeto->extras_select ;
		endif ;
		if (is_array($objeto->campopk)):
			for ($i = 0 ; $i < count($objeto->campopk) ; $i++):
				if (is_array($objeto->valorpk)):
					$objeto->valorpk[$i] = $objeto->valorpk[$i] ;
				else:
					$objeto->valorpk[$i] = $objeto->valorpk ;
				endif ;	
				is_numeric($objeto->valorpk[$i]) ? $objeto->valorpk[$i] = $objeto->valorpk[$i] : $objeto->valorpk[$i] = "'".$objeto->valorpk[$i]."'" ;
				if ((strstr($sql, "WHERE")) &&
					(trim($objeto->valorpk[$i]) !== "") &&
					(trim($objeto->valorpk[$i]) !== "''") &&
					($objeto->valorpk[$i] !== NULL)):
					$sql .= ' AND '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				elseif ((trim($objeto->valorpk[$i]) !== '') &&
						(trim($objeto->valorpk[$i]) !== "''") &&
						($objeto->valorpk[$i] !== NULL)):
					$sql .= ' WHERE '.$objeto->campopk[$i].' = '.$objeto->valorpk[$i] ;
				endif ;	
			endfor ;
		else:				
			if ((strstr($sql, "WHERE")) &&
				(trim($objeto->valorpk) !== '') &&
				(trim($objeto->valorpk) !== "''") &&
				($objeto->valorpk !== NULL)):
				$sql .= " AND ".$objeto->campopk."=" ;
				$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'" ;
			elseif ((trim($objeto->valorpk) !== '') &&
					(trim($objeto->valorpk) !== "''") &&
					($objeto->valorpk !== NULL)):
				$sql .= " WHERE ".$objeto->campopk."=" ;
				$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'" ;
			endif ;		
		endif ;
		if ($objeto->ordenar != NULL):
			$Pos = strpos(strtoupper($objeto->ordenar), 'ORDER BY') ;
			if ($Pos === FALSE)  
				$sql .= " ORDER BY ".$objeto->ordenar." " ;
			else
				$sql .= " ".$objeto->ordenar." " ;
		endif ;
		return $this->executaSql($sql) ;
	}//selecionarCampos
	
	public function retornarDados($tipo=NULL){
		switch (strtoupper($tipo)):
			case "ARRAY":
				 return mysql_fetch_array($this->dataset) ;
				 break;
			case "ASSOC":
				 return mysql_fetch_assoc($this->dataset) ;
 				 break;
			case "OBJECT":
				 return mysql_fetch_object($this->dataset) ;
 				 break;
			default:
			     return mysql_fetch_object($this->dataset) ;
				 break;
		endswitch ;
	}//retornarDados
	
	public function trataerro($arquivo=NULL,$rotina=NULL,$numero=NULL,$msgerro=NULL,$geraexcept=FALSE){
		$this->deuErro = true ;
		if ($arquivo==NULL)
		    $arquivo= "não informado" ;
		if ($rotina==NULL)
		    $rotina= "não informada" ;
		if ($numero==NULL)
		    $numero= mysql_errno($this->conexao) ;
		if ($msgerro==NULL)
		    $msgerro= mysql_error($this->conexao) ;
		$resultado = '<br />Ocorreu um erro com os seguintes detalhes:<br />
		              <atrong>Arquivo:</atrong> '.$arquivo.'<br />
                      <atrong>Rotina:</atrong> '.$rotina.'<br />
				      <atrong>Código:</atrong> '.$numero.'<br />
		              <atrong>Mensagem:</atrong> '.$msgerro.'<br />
		             ' ;
		if ($geraexcept==FALSE):
			printMSG($resultado, 'erro') ;
		else:
	    	die (printMSG($resultado, 'erro')) ;	
		endif;			 
	}//trataerro

	public function retornarUltimoRegistro($tipo, $tabela, $valorpk){
//		$result = $this->executaSql("SHOW TABLE STATUS LIKE '$tabela'") ;
 		if ($tipo === 'inserir'):
 			$result = $this->executaSql("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES ".
 										"WHERE TABLE_NAME=".'"'.$tabela.'"'." AND TABLE_SCHEMA=".'"'.$this->nomebanco.'"') ;
			$result = mysql_fetch_array($result) ;							  
			return $result['AUTO_INCREMENT']-1 ;
		elseif ($tipo === 'atualizar'):
			if (is_array($valorpk)):
				return implode(',', $valorpk) ;
			else:
				return $valorpk ;
			endif ;	
		else:
			return 0 ;	
 		endif ;	
/*
			$result = $this->executaSql("SELECT COLUMN_NAME AS CODIGO FROM information_schema.COLUMNS ".
										"WHERE TABLE_SCHEMA = ".'"'."$this->nomebanco".'" '.
										"AND TABLE_NAME = ".'"'.$tabela.'"'." AND EXTRA=".'"'.
										'auto_increment'.'" ') ;									
*/		
	}
}// fim de banco de dados
?>

