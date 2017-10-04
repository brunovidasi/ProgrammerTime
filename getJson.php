<?php

   /* 
	* PROGRAMMER TIME JSON REQUEST WITH PHP
	* 	@author BrunoVidasi - bruno@programmertime.com
	* 	@date 2015-03-06
	* 	@version 1.0
	* 
	* LOGIN ERROR MESSAGE:
	* 	0 - no error
	* 	1 - user not found
	* 	2 - wrong password
	* 	3 - user inactive
	* 	4 - indefined error
	* 
	*/

	@define(MAIN_URL, 'http://desenvolvimento.programmertime.com');

	if($_POST){

		$url = $_POST['url'];

		$data = array(
			'login' => $_POST['username'],
			'senha' => $_POST['password'],
			'cripto' => $_POST['cripto'],
			'idprojeto' => $_POST['project_id'],
			'idetapa' => $_POST['stage_id'],
			'idcliente' => $_POST['client_id'],
			'idusuario' => $_POST['user_id'],
		);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($curl);
		$error = curl_error($curl);
		// print_r($result);
		$object = json_decode($result);

		curl_close($curl);

		echo "<pre>";
		print_r($object);
		echo "</pre>";
		die();
	}

?>

<html>
	<head>
		<title>ProgrammerTime - Json PHP Test</title>
	</head>

	<body>

		<h1>ProgrammerTime - Json PHP Test</h1>

		<form method="post" action="">

			<input type="text" name="username" placeholder="Username" value="brunovidasi" />
			<input type="password" name="password" placeholder="Password" value=""/>
			<input type="hidden" name="url" id="url"/>
			<input type="hidden" name="cripto" id="cripto"/>

			<br /><br />

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/logar'; document.getElementById('cripto').value = 0;" type="submit" >
				Get User Data
			</button>

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/getProjetos'; document.getElementById('cripto').value = 1;" type="submit" >
				Get Projects
			</button>

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/getClientes'; document.getElementById('cripto').value = 1;" type="submit" >
				Get Clients
			</button>

			<br /><br />

			<input type="text" name="project_id" placeholder="Project ID" value="27" /> <br /><br />

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/getProjeto'; document.getElementById('cripto').value = 1;" type="submit" >
				Get Project by ID
			</button>

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/getEtapas'; document.getElementById('cripto').value = 1;" type="submit" >
				Get Stages by Project ID
			</button>

			<br /><br />

			<input type="text" name="stage_id" placeholder="Stage ID" value="62" /> <br /><br />

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/getEtapa'; document.getElementById('cripto').value = 1;" type="submit" >
				Get Stages by Stage ID
			</button>

			<br /><br />

			<input type="text" name="client_id" placeholder="Client ID" value="7" /> <br /><br />

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/getCliente'; document.getElementById('cripto').value = 1;" type="submit" >
				Get Client by Client ID
			</button>

			<br /><br />

			<input type="text" name="user_id" placeholder="User ID" value="1" /> <br /><br />

			<button onclick="document.getElementById('url').value = '<?php echo MAIN_URL; ?>/json/getUsuario'; document.getElementById('cripto').value = 1;" type="submit" >
				Get UserData by User ID
			</button>

			<br /><br />

			<p>
				This system makes a request to <a href="http://www.programmertime.com/">ProgrammerTime</a>, receives the information in JSON format and converts it to Array, only for testing purposes.
			</p>

			<p>
				Enter the required fields and click the information button you want to get.
			</p>

			<p>
				You must login and password for all requests.
			</p>

			<p>
				Developed by &copy; <a href="http://www.brunovidasi.com/">BrunoVidasi</a>
			</p>

		</form>

	</body>
</html>