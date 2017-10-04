<?php

$banco  = "brunovid_ptime_sce";
$usuar  = "root";
$passw  = "";
$servi  = "localhost";  
  
$vai = @mysql_connect($servi, $usuar, $passw);
$id = @mysql_select_db($banco, $vai);

while (list($chave,$valor) = each($_POST))
  $$chave = str_replace("'", "''", $valor);

while (list($chave,$valor) = each($_GET))
  $$chave = str_replace("'", "''", $valor);

while (list($chave,$valor) = each($_POST))
  $$chave = str_replace('"', '', $valor);

while (list($chave,$valor) = each($_GET)) 
  $$chave = str_replace('"', '', $valor);

?>