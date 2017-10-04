<?php include("config.php");

$controller = (!empty($nomes_controller[$tabela])) ? $nomes_controller[$tabela] : $tabela;

if(!empty($idprojeto)){
		$query = "UPDATE " . $tabela . " SET " . $nomeCampo . " = '" . $valorCampo . "' WHERE " . $idCampo . " = '" . $idprojeto . "'";
        $rquery = mysql_query($query, $vai);
		
        if(!$rquery) exit();
}else
	exit();
?>