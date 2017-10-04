<?php  

class Relatorio_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
	
	function post(){
	    if ($this->input->server('REQUEST_METHOD') == 'POST'){
            $this->idprojeto	= $this->input->post('idprojeto', TRUE);
            $this->relatorio 	= $this->input->post('relatorio');
			$this->data 		= date('Y-m-d H:i:s');
		}
	}
	
	function insert(){
	    if ($this->db->insert("relatorio", $this)){
            return $this->id = $this->db->insert_id();
        }
        return 0;
	}
	
	function update($id) {
        $id = (int) $id;
        if ($id > 0) {
            $this->db->where('idrelatorio', $id);
			if($this->db->update('relatorio', $this)){
				return TRUE;
			}
			return FALSE;
        }
        return FALSE;
	}
	
	function get_relatorio($idrelatorio){
		$sql = "SELECT * FROM relatorio WHERE idrelatorio='{$idrelatorio}' LIMIT 1";
		
		return $this->db->query($sql);
	}
	
	function get_relatorios(){
		$sql = "SELECT	
					R.*, 
					P.nome as nomeprojeto,
					C.nome as nomecliente
				FROM 
					relatorio as R 
				RIGHT JOIN 
					projeto as P ON R.idprojeto = P.idprojeto
				LEFT JOIN 
					cliente as C ON P.idcliente = C.idcliente
				ORDER BY 
					R.data DESC
		";
		
		return $this->db->query($sql);
	}
	
	function get_relatorios_lista($maximo, $inicio){
		$sql = "SELECT	
					R.*, 
					P.nome as nomeprojeto,
					C.nome as nomecliente
				FROM 
					relatorio as R
				RIGHT JOIN 
					projeto as P ON R.idprojeto = P.idprojeto
				LEFT JOIN 
					cliente as C ON P.idcliente = C.idcliente
				ORDER BY 
					R.data DESC
				LIMIT 
					{$inicio}, {$maximo}
		";
		
		return $this->db->query($sql);
	}
	
	function get_informacoes($idprojeto){
		$sql = "SELECT 	
					P.*, 
					PT.tipo as TipoProjeto, 
					U.nome as NomeUsuario, 
					U.email as EmailUsuario, 
					C.nome as NomeCliente, 
					C.email as EmailCliente	
				FROM 
					projeto as P
				LEFT JOIN 
					projeto_tipo as PT ON PT.idtipo = P.idtipo
				LEFT JOIN 
					usuario as U ON U.idusuario = P.idresponsavel
				LEFT JOIN 
					cliente as C ON C.idcliente = P.idcliente
				WHERE 
					P.idprojeto='{$idprojeto}'";
		
		return $this->db->query($sql);
	}
	
	function gera_relatorio_html($idprojeto){
		
		# Dados da Etapa
		$dados_etapa = "<tr>
							<th align='right'>Fase</th>
							<th>Descrição Técnica</th>
							<!--<th>Descrição Cliente</th>-->
							<th>Responsável</th>
							<th>Data</th>
							<th>Tempo</th>
						</tr>";

		$etapas = $this->etapa_model->get_etapas_relatorio($idprojeto);
		$total_horas = array();
		
		foreach($etapas->result() as $etapa){
			$dados_etapa .= '
			<tr>
				<td>'. $etapa->fase .'</td>
				<td>'. $etapa->descricao_tecnica .'</td>
				<!--<td>'. $etapa->descricao_cliente .'</td>-->
				<td>'. fnome($etapa->responsavel, 2) .'</td>
				<td>'. fdata($etapa->data, "/") .'</td>
				<td>'. calcular_horas($etapa->fim, $etapa->inicio) .'</td>
			</tr>
			';
			
			$total_horas[] = calcular_horas_total($etapa->fim, $etapa->inicio);
		}
		
		# Dados Financeiro
		$dados_financeiro = "<tr><th>Descrição</th><th>Status</th><th>Valor</th><th>Pago</th><th>Cobrado</th><th>Pago</th></tr>";
		$financeiros = $this->financeiro_model->get_financeiro_projeto($idprojeto);
		
		foreach($financeiros->result() as $financeiro){
			
			if($financeiro->status == 'nao_pago')
				$pago = 'Não Pago';
			elseif($financeiro->status == 'cobrado')
				$pago = 'Cobrado';
			elseif($financeiro->status == 'parcialmente_pago')
				$pago = 'Parcialmente Pago';
			elseif($financeiro->status == 'pago')
				$pago = 'Pago';
			
			$dados_financeiro .= '
			<tr>
				<td>'. $financeiro->descricao .'</td>
				<td>'. $pago .'</td>
				<td>'. moeda($financeiro->valor) .'</td>
				<td>'. moeda($financeiro->valor_pago) .'</td>
				<td>'. fdatetime($financeiro->data_cobrado, "/") .'</td>
				<td>'. fdatetime($financeiro->data_pago, "/") .'</td>
			</tr>
			';
		}
		
		$informacoes = $this->relatorio_model->get_informacoes($idprojeto);

		foreach($informacoes->result() as $inf){
			$idcliente = $inf->idcliente;
			$idresponsavel = $inf->idresponsavel;
			$dados_tipo = $inf->TipoProjeto;
			$responsavel_nome = $inf->NomeUsuario;
			$responsavel_email = $inf->EmailUsuario;
			$cliente_nome = $inf->NomeCliente;
			$cliente_email = $inf->EmailCliente;
		}
		
		$projetos = $this->projeto_model->get_projeto($idprojeto);
		
		if($projetos->num_rows() > 0){
		
			foreach($projetos->result() as $projeto){
				
				$relatorio = new stdClass();
				$relatorio->titulo = "Relatório - " . $projeto->nome . " - " . date('d/m/Y');
				
				$relatorio->html = '
				
				<div style="background:#eee; border:1px solid #ccc; padding:5px 10px"><strong>Relatório do Projeto - '. $projeto->nome .'</strong></div><hr />
				
				<table style="width:100%;">
					
					<tr>
						<td><strong>Projeto:</strong></td>
						<td>'. $projeto->nome .' ('. $dados_tipo .') ['. $idprojeto .']</td>
						
						<td><strong>Iniciado:</strong></td>
						<td>'. fdata($projeto->data_inicio , "/") .'</td>
					</tr>
					
					<tr>
						<td><strong>Cliente:</strong></td>
						<td>'. $cliente_nome .' ['. $idcliente .']</td>
						
						<td><strong>Data Atual:</strong></td>
						<td>'. date('d/m/Y') .'</td>
					</tr>
					
					<tr>
						<td><strong>Email:</strong></td>
						<td>'. $cliente_email .'</td>
						
						<td><strong>Prazo:</strong></td>
						<td>'. fdatetime($projeto->prazo,"/") .'</td>
					</tr>
					
					<tr>
						<td><strong>Responsável: </strong></td>
						<td>'. $responsavel_nome . ' - ' . $responsavel_email . ' [' . $idresponsavel .']</td>
						
						<td><strong>Finalizado:</strong></td>
						<td>'. fdata($projeto->data_fim , "/") .'</td>
					</tr>
				
				</table>
				
				<hr><div style="background:#eee; border:1px solid #ccc; padding:5px 10px"><strong>Etapas do Projeto Concluídas</strong></div><hr>
				
				<table style="width:100%;">
					'. $dados_etapa .'
					<tr>
					<td></td><td></td><td></td><td></td><td><strong>'. somar_horas($total_horas) .' Horas</strong></td>
					</tr>
				</table>
				
				<hr><div style="background:#eee; border:1px solid #ccc; padding:5px 10px"><strong>Controle Financeiro</strong></div><hr>
				
				<table style="width:100%;">
					'. $dados_financeiro .'
				</table>
				
				<hr>';
			}
		
		}else{
			$relatorio = new stdClass();
			$relatorio->titulo = "ERRO";
			$relatorio->html = '
				<blockquote>
				<p><span class="marker">ERRO (Relat&oacute;rio, projeto ou cliente n&atilde;o selecionado)&nbsp;Volte a p&aacute;gina e selecione um relat&oacute;rio existente.</span></p>
				</blockquote>
			';
		}
		
		return $relatorio;
	}
	
	function gera_relatorio_pdf($html="", $titulo=""){
		
		if(empty($html)){
			$html 	= $this->input->post('relatorio');
		}
		
		if(empty($titulo)){
			$titulo = $this->input->post('titulo');
		}
		
		require_once("assets/pdf/dompdf_config.inc.php");
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'portrait');
		$dompdf->render();
		$dompdf->stream($titulo.".pdf");
	
	}
	
	function gera_relatorio_mpdf($html="", $titulo=""){
		
		if(empty($html)){
			$html 	= $this->input->post('relatorio');
		}
		
		if(empty($titulo)){
			$titulo = $this->input->post('titulo');
		}
		
		include("assets/mpdf/mpdf.php");
		
		$stylesheet = file_get_contents('assets/mpdf/mpdf_style.css');

		$mpdf = new mPDF();
		$mpdf->Bookmark('Start of the document');
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->WriteHTML($html, 2);
		$mpdf->Output();
		// $mpdf->Output($titulo.'.pdf', 'D');

		exit;
	
	}
	
}