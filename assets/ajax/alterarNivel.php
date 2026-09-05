<?php require __DIR__ . "/config.php";

$tabela     = $_POST['tabela']     ?? '';
$nomeCampo  = $_POST['nomeCampo']  ?? '';
$valorCampo = $_POST['valorCampo'] ?? '';
$idCampo    = $_POST['idCampo']    ?? '';
$idnivel    = $_POST['idnivel']    ?? '';

if (empty($idnivel)) exit();

$rquery = atualizar_campo($vai, $campos_permitidos, $tabela, $idCampo, $nomeCampo, $valorCampo, $idnivel);

if ($rquery && is_numeric($idnivel) && $vai) {
	$stmt = mysqli_prepare($vai, "UPDATE `usuario` SET `recarregar` = 'sim' WHERE `nivel_acesso` = ?");
	if ($stmt) {
		$idnivel_int = (int) $idnivel;
		mysqli_stmt_bind_param($stmt, "i", $idnivel_int);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
	}
}

if (!$rquery) exit();
?>
