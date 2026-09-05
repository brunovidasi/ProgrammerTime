<?php
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 

# 							CONFIGURAÇÃO PROGRAMMER TIME 1.0							#

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 


# BANCO DE DADOS DO GERENCIADOR DO PTIME 												#
# SECURITY: real hosting DB credentials were committed here and exposed on
# a public GitHub repo. Rotate the actual database password on the server
# if this account still exists, then fill in a real (non-committed) value
# via a local, gitignored config or environment variable.

define('PT_DB_HOST', 'localhost');			# Link ou IP do HOST
define('PT_DB_USER', 'YOUR_DB_USER');		# Nome de Usuário
define('PT_DB_PASS', 'YOUR_DB_PASSWORD');	# Senha
define('PT_DB_DATABASE', 'YOUR_DB_NAME');	# Nome do Banco

# BANCO DE DADOS DO PROGRAMMER TIME														#

define('DB_HOST_P', 'localhost');			# Link ou IP do HOST
define('DB_USER_P', 'YOUR_DB_USER');		# Nome de Usuário
define('DB_PASS_P', 'YOUR_DB_PASSWORD');		# Senha
define('DB_DATABASE_P', 'YOUR_DB_NAME');	# Nome do Banco

# LICENSA DE USO DO PTIME 																#
# SECURITY: the values below were real license/access secrets committed
# here and exposed on a public GitHub repo. They are unused by the current
# app (the license check in acesso.php is hardcoded bypassed), but if this
# licensing system is still live anywhere, rotate them there too.

// define('PT_VERSION', '1.0');				# versão
// define('PT_TYPE', ''); 					# trial, free, vitalicy, testing, production
// define('PT_USER', '');					# usuário do programmertime
// define('PT_LINCENSE_KEY', '');			# licensa de uso no caso de vitalício
// define('PT_ACCESS_PASS', '');			# senha de acesso ao programmertime
// define('PT_VERIFICATION', '1');			# senha de verificação

define('PT_VERSION', '1.0');
define('PT_TYPE', 'testing');
define('PT_USER', 'YOUR_PT_USER');
define('PT_LINCENSE_KEY', 'YOUR_LICENSE_KEY');
define('PT_ACCESS_PASS', 'YOUR_ACCESS_PASS');
define('PT_VERIFICATION', 'YOUR_VERIFICATION_KEY');

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 