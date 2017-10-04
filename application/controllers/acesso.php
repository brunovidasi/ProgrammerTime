<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acesso extends CI_Controller {

	public function index($mensagem = null){
		
		if($this->session->userdata('logado') == TRUE) redirect('/dashboard/');
		
		$usuarios = $this->usuario_model->get_usuarios()->num_rows();
		if($usuarios == 1) redirect('/acesso/primeira_vez/');

		$dados = array();
		$dados['mensagem'] = $mensagem;
		
		$this->load->view('acesso/login', $dados);
	}
	
	public function logar($nome_usuario = null, $senha = null, $token = '2344'){

		// Verificar Licença

		// $licenca = new Licenca();
		// $sistema_ativo = $licenca->verificar();

		$sistema_ativo = TRUE;

		if(!$sistema_ativo){
			$this->session->set_flashdata('mensagem', lang('msg_sistema_inativo'));
			redirect('/acesso/');
		}

		if(empty($nome_usuario)){
			$nome_usuario = $this->input->post('login');
			$senha = cripto($this->input->post('senha'));
		}else{
			if($token != '#432!@0*9tpime&first_access!#')
				$nome_usuario = null;
		}

		#senha admin: 529617fc278c80e375f9a3a9f20a329f
		
		$usuario = $this->acesso_model->get_informacao($nome_usuario)->row();

		if((!empty($usuario->idusuario)) && ($usuario->idusuario > 0)){
			if(($senha == $usuario->senha) && ($usuario->status == 'ativo')){
				$navegador = $this->verificar_navegador();
				
				$usuario_data = array(
					'nome'			=> fnome($usuario->nome, 0),
					'nome_duplo'	=> fnome($usuario->nome, 1),
					'nome_completo'	=> $usuario->nome,
					'id'			=> $usuario->idusuario,
					'login'			=> $usuario->login,
					'email'			=> $usuario->email,
					'imagem'		=> $usuario->imagem,
					'numero_acesso'	=> $usuario->numero_acesso,
					'nivel_acesso'	=> $usuario->nivel_acesso,
					'status'		=> $usuario->status,
					'cor'			=> $usuario->cor,
					'confirmado'	=> $usuario->usuario_confirmado,
					'matricula'		=> $usuario->matricula,
					'email_senha'	=> $usuario->email_senha,
					'navegador'		=> $navegador,
					'logado'		=> TRUE,
					'bloqueado'		=> FALSE
				);
				
				$this->session->set_userdata($usuario_data);
				$this->session->set_userdata("usuario", $usuario);

				$this->session->set_flashdata("logou", TRUE);
			
				$this->acesso_model->log($usuario->numero_acesso, $usuario->idusuario);
				$this->acesso_model->nivel_acesso($usuario->nivel_acesso);
				
				if($usuario->numero_acesso == 1) redirect('/usuario/editar/');
				elseif($usuario->usuario_confirmado == 'nao') redirect('/usuario/visualizar/');
				else redirect('/dashboard/');
				
			}else{
				
				if(($usuario->status == 'inativo') && ($senha == $usuario->senha)){
					$this->session->set_flashdata('mensagem', lang('msg_entre_em_contato_adm'));
					$this->session->set_flashdata('controle', 'usuario_inativo');
					$this->enviar_email->inativo($email, "contato@programmertime.com", $usuario->nome, $usuario->login);
				}else{
					$this->session->set_flashdata('controle', 'senha_incorreta');
					$this->session->set_flashdata('login', $usuario->login);
				}
				redirect('/acesso/');
			}
		}else{
			$this->session->set_flashdata('controle', 'usuario_incorreto');
			redirect('/acesso/');
		}
	}
	
	public function primeira_vez(){
		$usuarios = $this->usuario_model->get_usuarios()->num_rows();

		if($usuarios == '1') 
			$this->load->view('acesso/primeira_vez');
		else 
			redirect('/acesso/');
	}
	
	public function cadastrar(){
		$usuarios = $this->usuario_model->get_usuarios()->num_rows();
		if($usuarios == '1'){
		
			if ($this->valida_form()){
			
				$dados = $this->usuario_model->post_primeira_vez();
				$idinsert = $this->usuario_model->insert($dados);
				
				if($idinsert > 0){
					$usuario = $this->usuario_model->get_usuario($idinsert)->row();
					$this->logar($usuario->login, $usuario->senha, '#432!@0*9tpime&first_access!#');
				}else{
					$this->session->set_flashdata('mensagem_erro', lang('msg_usuario_nao_cadastrado'));
					redirect('/acesso/primeira_vez/');
				}
				
			}else{
				$this->primeira_vez();	
			}
			
		}else{
			redirect('/acesso/');
		}
	}
	
	private function valida_form(){
		$this->form_validation->set_rules('nome', 				lang('lbl_nome'), 			'trim|required');
		$this->form_validation->set_rules('email', 				lang('lbl_email'), 			'trim|required|valid_email|is_unique[usuario.email]');
		$this->form_validation->set_rules('login', 				lang('lbl_nome_usuario'), 	'trim|required');
		$this->form_validation->set_rules('senha', 				lang('lbl_senha'), 			'trim|required|matches[confirmacao_senha]');
		$this->form_validation->set_rules('confirmacao_senha', 	lang('lbl_confirma_senha'), 'trim');
		
		return $this->form_validation->run();
	}
	
	private function verificar_navegador(){
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		$navegador = new stdClass();
	
		if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)){
			$browser = 'Internet Explorer';
			$browser_version = $matched[1];
		}elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$useragent,$matched)){
			$browser = 'Opera';
			$browser_version = $matched[1];
		}elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)){
			$browser = 'Mozilla Firefox';
			$browser_version = $matched[1];
		}elseif(preg_match('|Chrome/([0-9\.]+)|',$useragent,$matched)){
			$browser = 'Google Chrome';
			$browser_version = $matched[1];
		}elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)){
			$browser = 'Safari';
			$browser_version = $matched[1];
		}else{
			$browser= 'Desconhecido';
			$browser_version = '';
		}
		
		$navegador->useragent = $useragent;
		$navegador->browser = $browser;
		$navegador->version  = $browser_version;
		$navegador->browser_version  = $browser . ' - ' . $browser_version;
		
		return $navegador;
	}
	
	public function confirma_email($senha){
		
		$id = (int) $this->usuario_model->get_senha_email($senha);

		$this->session->sess_destroy();
		
		if(!empty($id) && $id > 0){
			$confirmado = $this->usuario_model->confirma_email($id);

			if($confirmado){
				$this->session->set_flashdata('mensagem', lang('msg_usuario_confirmado'));
				redirect('/acesso/');
			}else{
				$this->session->set_flashdata('mensagem', lang('msg_usuario_nao_confirmado'));
				redirect('/acesso/');
			}
		}else{
			$this->session->set_flashdata('mensagem', lang('msg_usuario_nao_confirmado'));
			redirect('/acesso/');
		}
	}
	
	public function bloquear(){
		$this->session->set_userdata('bloqueado', TRUE);
		$this->load->view('acesso/bloqueio');
	}

	public function desbloquear(){
		$senha = cripto($this->input->post('senha'));
		$usuario = $this->acesso_model->get_informacao($this->session->userdata('login'))->row();

		if($senha != $usuario->senha){
			$this->session->set_flashdata('mensagem', lang('senha_incorreta'));
			redirect('/acesso/bloquear/');
		}

		$this->session->set_userdata('bloqueado', FALSE);
		redirect('/dashboard/');
	}

	public function sair(){
		$this->session->sess_destroy();
		redirect('/acesso/');
	}
	
}