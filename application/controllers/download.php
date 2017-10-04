<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Downloads extends CI_Controller {

	public function index(){
		
	}
	
	public function especificacoes_tecnicas(){
		$this->download->especificacoes_tecnicas();
	}	
	
	public function informacao_suporte(){
		$this->download->informacao_suporte();
	}
	
}