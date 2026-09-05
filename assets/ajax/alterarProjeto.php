<?php require __DIR__ . "/config.php";

$tabela     = $_POST['tabela']     ?? '';
$nomeCampo  = $_POST['nomeCampo']  ?? '';
$valorCampo = $_POST['valorCampo'] ?? '';
$idCampo    = $_POST['idCampo']    ?? '';
$idprojeto  = $_POST['idprojeto']  ?? '';

if (empty($idprojeto)) exit();

$rquery = atualizar_campo($vai, $campos_permitidos, $tabela, $idCampo, $nomeCampo, $valorCampo, $idprojeto);

if (!$rquery) exit();
?>
