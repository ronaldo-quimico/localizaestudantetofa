<?php
protegeArquivo(basename(__FILE__)) ;

$incluiu_estilo = FALSE ;

$DB_HOST = $_ENV("DB_HOST") ;
$DB_USER = $_ENV("DB_USER") ;
$DB_PASSWORD = $_ENV("DB_PASSWORD") ;
$DB_NAME = $_ENV("DB_NAME") ;
$DB_PORT = $_ENV("DB_PORT") ;
define("MASKCPF", "999.999.999-99") ;
define("MASKCNPJ", "99.999.999/9999-99") ;
define("MASKFONE", "(99) 9999-9999") ;
define("MASKCELULAR", "(99) 9999-9999") ;
define("MASKCEP", "99999-999") ;
define("MASKDATA", "39/19/9999") ;
define("PULAR_LINHA_REGRA_SCRIPT", "\n                         ") ;
define("PULAR_LINHA_REGRA_STYLE", "\n") ;
define("PULAR_LINHA_PHP", "\n") ;
define("COR_CABECALHO_TABELA", '#B0C4DE') ;
define("COR_LINHA_SELECIONADA_TABELA", '#F5DEB3') ;
define("COR_LINHA_IMPAR_TABELA", 'white') ;
define("COR_LINHA_PAR_TABELA", '#E2E4FF') ;
# quebrar linha '<Br /> \n'

//diretório do sistema
define("INDEXPHP", "painel.php") ;
define("BASEPATH", dirname(__FILE__)."/") ;
//define("BASEURL", "http://localhost/paineladm/") ;
define("BASEURL", $DB_HOST) ;
define("ADMURL", BASEURL."painel.php") ;
define("TEMP", BASEPATH."temp/") ;
define("BLOB", BASEPATH."blob/") ;
define("CLASSESPATH", BASEPATH."classes/") ;
define("MODULOSPATH", BASEPATH."modulos/") ;
define("FUNCOESPATH", BASEPATH."funcoes.php") ;
define("CSSPATH", BASEURL) ;
define("JSPATH", BASEURL."js/") ;

//banco de dados
define("DBHOST", $DB_HOST) ;
//define("DBHOST", "http://192.168.0.6") ;
define("DBUSER", $DB_USER) ;
define("DBPASS", $DB_PASSWORD) ;
define("DBNAME", $DB_NAME) ;
define("DBMENU", "configmenu") ;
?>