<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teste extends CI_Controller {
	
	public function index(){
	    redirect('/dashboard/');
	}
	
	function confirmacao($chave = 7){
		pexit('Numero de Confirmação de Usuário Gerado', gera_confirmacao($chave));
	}
	
	function progress(){
		echo '<progress value="22" max="100"></progress>';
	}
	
	function binario(){
		$dados['view'] = '<img src="'. base_url('assets/images/sistema/fundo_binario.png') .'" style="width:100%;"/>';
		$this->load->view('includes/interna', $dados);
	}
	
	# -----  TESTE COM MODAL  ----- #
	function modal(){
		$etapa_aberta = $this->etapa_model->etapa_aberta($this->session->userdata('id'));
		
		if($etapa_aberta->num_rows() > 0){
			redirect('/etapa/retornar/');
		}
		
		$dados['idcresult'] = $this->get_projetos_value($this->input->post('idcliente'));
		
		$dados['clientes'] 	= $this->cliente_model->get_clientes();
		$dados['fases'] 	= $this->etapa_model->get_fases();
		
		$dados['cliente_id'] 	= "";
		$dados['projeto'] 		= "";
		
		if(!empty($idprojeto)){
			$projeto = $this->projeto_model->get_projeto($idprojeto);
			
			if ($projeto->num_rows() > 0){
			   $pro = $projeto->row(); 
			   $idcliente = $pro->idcliente;
			}
			
			$dados['projeto'] 		= $idprojeto;
			$dados['cliente_id'] 	= $idcliente;
			
			$dados['idcresult'] 	= $this->get_projetos_value($idcliente, $idprojeto);
		}
		
		$dados['background'] = TRUE;
		$dados['view'] = $this->load->view('etapa/lancar', $dados, TRUE);
		$this->load->view('includes/modal', $dados);
	}
	
	function get_projetos_value($idcliente, $idprojeto="") {
		$projetos = $this->etapa_model->get_projeto_cliente($idcliente)->result();
		
		$result = "";
		
		if(empty($projetos)){
			$result .= '<option value="0">Não há projetos em desenvolvimento deste cliente.</option>';
		}else{
			
			$result .= '<option value="">Selecione o Projeto</option>';
			
			foreach ($projetos as $projeto) {
				$prazo = fdatahora($projeto->prazo, "/");
				
				$data = $prazo['data'];
				$hora = $prazo['hora'];
				
				$horaprojeto = explode(":", $hora);
				$hora = $horaprojeto[0] . ':' . $horaprojeto[1];
				
				$selected = "";
				if(set_value('idprojeto', $idprojeto) == $projeto->idprojeto){
					$selected = 'selected="selected"';
				}
				
				$result .=  '<option value="'.$projeto->idprojeto .'" '. $selected .'>'.$projeto->nome.' - Prazo: '. $data .' '. $hora .' - Prioridade: '. $projeto->prioridade .'</option>';
			}
		}
		
		return $result;
	}
	
}