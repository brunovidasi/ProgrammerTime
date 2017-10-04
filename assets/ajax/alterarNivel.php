<?php include("config.php");

$controller = (!empty($nomes_controller[$tabela])) ? $nomes_controller[$tabela] : $tabela;

if (!empty($idnivel)) {
		$query = "UPDATE " . $tabela . " SET " . $nomeCampo . " = '" . $valorCampo . "' WHERE " . $idCampo . " = '" . $idnivel . "'";
        $rquery = mysql_query($query, $vai);
		
		$query2 = "UPDATE usuario SET recarregar = 'sim' WHERE nivel_acesso = '" . $idnivel . "'";
        $rquery2 = mysql_query($query2, $vai);
		
        if(!$rquery) exit();
}else
	exit();
?>