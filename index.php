<?php

   /*
	*
	* Programmer Time _ 1.0
	* @autor Bruno Vieira da Silva - @brunovidasi - bruno@brunovidasi.com
	* Sistema de Gerenciamento de Projetos para Desenvolvimento de Softwares
	* Desenvolvimento PHP (Codeigniter 2.2)
	*
	*/
	
	if(phpversion() < '5.3.28')
		exit('� imposs�vel executar o Programmer Time em um servidor com a vers�o do PHP menor que 5.3.28. O ideal � ter instalada uma vers�o igual ou superior a 5.4.31 do PHP.');
	
	setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
	date_default_timezone_set('America/Sao_Paulo');

	define('ENVIRONMENT', 'development');

	if(defined('ENVIRONMENT')){
		switch (ENVIRONMENT){
			case 'development': error_reporting(E_ALL); break;
			case 'testing':
			case 'production': error_reporting(0); break;
			default: exit('O ambiente de aplica��o n�o est� definido corretamente.');
		}
	}

	$system_path = 'system';
	$application_folder = 'application';
	
	if(defined('STDIN')) chdir(dirname(__FILE__));
	
	if(realpath($system_path) !== FALSE) $system_path = realpath($system_path).'/';
	
	$system_path = rtrim($system_path, '/').'/';

	if(!is_dir($system_path)) exit("O caminho da pasta do sistema n�o parece estar definida corretamente. Por favor, abra o seguinte arquivo e corrigir o problema: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	define('EXT', '.php');
	define('BASEPATH', str_replace("\\", "/", $system_path));
	define('FCPATH', str_replace(SELF, '', __FILE__));
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));
	
	if(is_dir($application_folder)){
		define('APPPATH', $application_folder.'/');
	}else{
		if(!is_dir(BASEPATH.$application_folder.'/'))
			exit("O caminho da pasta do aplicativo n�o parece estar definida corretamente. Por favor, abra o seguinte arquivo e corrigir o problema: ".SELF);
		define('APPPATH', BASEPATH.$application_folder.'/');
	}

	require_once BASEPATH.'core/CodeIgniter.php';