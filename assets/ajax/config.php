<?php

$banco  = "brunovid_ptime_sce";
$usuar  = "root";
$passw  = "";
$servi  = "localhost";

mysqli_report(MYSQLI_REPORT_OFF);
$vai = @mysqli_connect($servi, $usuar, $passw, $banco);

// Whitelist of table => allowed id column + allowed value columns.
// Keep in sync with the calls in assets/js/atualizaAjax.js and the
// views that invoke nivel()/prioridade()/status()/tstatus().
$campos_permitidos = array(
	'projeto' => array(
		'idCampo'   => 'idprojeto',
		'nomeCampo' => array('status', 'prioridade'),
	),
	'projeto_tarefa' => array(
		'idCampo'   => 'idtarefa',
		'nomeCampo' => array('status'),
	),
	'usuario_nivel_acesso' => array(
		'idCampo'   => 'id',
		'nomeCampo' => array(
			'cadastra_projeto', 'edita_projeto',
			'cadastra_cliente', 'edita_cliente',
			'lanca_etapa', 'lanca_pagamento',
			'envia_relatorio',
			'cadastra_usuario', 'edita_usuario',
		),
	),
);

// Only lowercase word-style values are ever sent by the app
// (e.g. "sim", "nao", "desenvolvimento", "urgente").
function valor_permitido($valorCampo){
	return is_string($valorCampo) && preg_match('/^[a-z_]{1,50}$/', $valorCampo) === 1;
}

// Validates table/column/value against the whitelist above, then runs
// the UPDATE as a prepared statement. Table/column names never come
// from user input at this point -- only from the whitelist array --
// so it's safe to interpolate them once validated.
function atualizar_campo($vai, $campos_permitidos, $tabela, $idCampo, $nomeCampo, $valorCampo, $idValor){

	if (!$vai) return false;
	if (!is_string($tabela) || !isset($campos_permitidos[$tabela])) return false;

	$permitido = $campos_permitidos[$tabela];

	if ($idCampo !== $permitido['idCampo']) return false;
	if (!is_string($nomeCampo) || !in_array($nomeCampo, $permitido['nomeCampo'], true)) return false;
	if (!valor_permitido($valorCampo)) return false;
	if (!is_numeric($idValor)) return false;

	$sql = "UPDATE `" . $tabela . "` SET `" . $nomeCampo . "` = ? WHERE `" . $idCampo . "` = ?";
	$stmt = mysqli_prepare($vai, $sql);
	if (!$stmt) return false;

	$idValor = (int) $idValor;
	mysqli_stmt_bind_param($stmt, "si", $valorCampo, $idValor);
	$ok = mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);

	return $ok;
}

?>
