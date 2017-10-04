<?php  

class Download extends CI_Model {

    function __construct() {
        parent::__construct();
    }
	
	function especificacoes_tecnicas(){
	
		$especifica��es = 'PROGRAMMER TIME - Gerenciamento de Projetos\n
________________________________________________________________________________________\n\n

http://www.programmertime.com/ \n\n


Especifica��es T�cnicas \n
________________________________________________________________________________________\n\n

Vers�o do Sistema Atual: 1.0.0.0 \n
Vers�o do CodeIgniter: 2.1.4 \n
Data de Lan�amento: - \n
Data de Atualiza��o: - \n


Informa��es de PHP \n
________________________________________________________________________________________\n\n

Vers�o do PHP: 5.3.28 \n
Outras vers�es do PHP podem causar incompatibilidade com o sistema. \n\n


Informa��es de Banco de Dados \n
________________________________________________________________________________________\n\n

Database: MySQL 5.5.36-cll \n
SGBD: PHPMyAdmin \n\n


Informa��es de Desenvolvimento \n
________________________________________________________________________________________\n\n

Desenvolvedor WEB: Bruno Vieira - bruno@programmertime.com - www.brunovidasi.com \n
Desenvolvedor ANDORID: Filipe Moreira - filipe@programmertime.com \n';
		
		$t�tulo = 'Programmer Time - Especifica��es T�cnicas.txt';

		force_download($t�tulo, $especifica��es);
	}
	
	
	function informacao_suporte(){
	
		$useragent = $_SERVER['HTTP_USER_AGENT'];
	 
		if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$useragent,$matched)) {
			$browser_version=$matched[1];
			$browser = 'IE';
		}elseif (preg_match( '|Opera/([0-9].[0-9]{1,2})|',$useragent,$matched)) {
			$browser_version=$matched[1];
			$browser = 'Opera';
		}elseif(preg_match('|Firefox/([0-9\.]+)|',$useragent,$matched)) {
			$browser_version=$matched[1];
			$browser = 'Firefox';
		}elseif(preg_match('|Chrome/([0-9\.]+)|',$useragent,$matched)) {
			$browser_version=$matched[1];
			$browser = 'Chrome';
		}elseif(preg_match('|Safari/([0-9\.]+)|',$useragent,$matched)) {
			$browser_version=$matched[1];
			$browser = 'Safari';
		}else {
			$browser_version = '';
			$browser= 'Outro';
		}

		$informa��es = 'Informa��es de Usu�rio\n
________________________________________________________________________________________\n\n

Nome: '. $this->session->userdata('nome_completo') .'\n
Login: '. $this->session->userdata('login') .' \n
ID: '. $this->session->userdata('id') .' \n
E-mail: '. $this->session->userdata('email') .' \n
Matr�cula: '. $this->session->userdata('matricula') .' \n
Imagem: '. $this->session->userdata('imagem') .' \n\n

N�mero de Acessos ao Sistema: '. $this->session->userdata('numero_acesso') .' \n
N�vel de Acesso: '. $this->session->userdata('nivel_acesso') .' \n
Status: '. $this->session->userdata('status') .' \n
Confirmado: '. $this->session->userdata('confirmado') .' \n
Confirma��o: '. $this->session->userdata('email_senha') .' \n
Logado: '. $this->session->userdata('logado') .' \n\n


Dados da Sess�o \n
________________________________________________________________________________________\n\n

SESSION ID: '. $this->session->userdata('session_id') .' \n
USER AGENT: '. $this->session->userdata('user_agent') .' \n
LAST ACTIVITY: '. $this->session->userdata('last_activity') .' \n
LAST VISIT: '. $this->session->userdata('last_visit') .' \n

Browser: '.  $browser . ' - ' . $browser_version .' \n
IP: '. $this->session->userdata('ip_address') .' \n

Informa��es do Sistema \n
________________________________________________________________________________________ \n\n

Vers�o do Programmer Time: 1.0.0.0 \n
Vers�o do PHP: '. phpversion() .'';
		 
		$t�tulo = 'Informa��es de Sess�o - '. $this->session->userdata('login') .'.txt';

		force_download($t�tulo, $informa��es);
	}

}