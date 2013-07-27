<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------
  | DATABASE CONNECTIVITY SETTINGS
  | -------------------------------------------------------------------
  | This file will contain the settings needed to access your database.
  |
  | For complete instructions please consult the 'Database Connection'
  | page of the User Guide.
  |
  | -------------------------------------------------------------------
  | EXPLANATION OF VARIABLES
  | -------------------------------------------------------------------
  |
  |	['hostname'] The hostname of your database server.
  |	['username'] The username used to connect to the database
  |	['password'] The password used to connect to the database
  |	['database'] The name of the database you want to connect to
  |	['dbdriver'] The database type. ie: mysql.  Currently supported:
  mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
  |	['dbprefix'] You can add an optional prefix, which will be added
  |				 to the table name when using the  Active Record class
  |	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
  |	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
  |	['cache_on'] TRUE/FALSE - Enables/disables query caching
  |	['cachedir'] The path to the folder where cache files should be stored
  |	['char_set'] The character set used in communicating with the database
  |	['dbcollat'] The character collation used in communicating with the database
  |				 NOTE: For MySQL and MySQLi databases, this setting is only used
  | 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
  |				 (and in table creation queries made with DB Forge).
  | 				 There is an incompatibility in PHP with mysql_real_escape_string() which
  | 				 can make your site vulnerable to SQL injection if you are using a
  | 				 multi-byte character set and are running versions lower than these.
  | 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
  |	['swap_pre'] A default table prefix that should be swapped with the dbprefix
  |	['autoinit'] Whether or not to automatically initialize the database.
  |	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
  |							- good for ensuring strict SQL while developing
  |
  | The $active_group variable lets you choose which connection group to
  | make active.  By default there is only one group (the 'default' group).
  |
  | The $active_record variables lets you determine whether or not to load
  | the active record class
 */

$active_group = 'default';
$active_record = TRUE;

if (getenv("VCAP_SERVICES")) {

	// Appfog.com settings
	$VCAP_SERVICES = json_decode(getenv("VCAP_SERVICES"), TRUE);

	$_default_database = isset($VCAP_SERVICES['mysql-5.1'][0]['credentials']['name']) ? $VCAP_SERVICES['mysql-5.1'][0]['credentials']['name'] : "";
	$_default_hostname = isset($VCAP_SERVICES['mysql-5.1'][0]['credentials']['host']) ? $VCAP_SERVICES['mysql-5.1'][0]['credentials']['host'] : "";
	$_default_username = isset($VCAP_SERVICES['mysql-5.1'][0]['credentials']['username']) ? $VCAP_SERVICES['mysql-5.1'][0]['credentials']['username'] : "";
	$_default_password = isset($VCAP_SERVICES['mysql-5.1'][0]['credentials']['password']) ? $VCAP_SERVICES['mysql-5.1'][0]['credentials']['password'] : "";

	$db['default']['hostname'] = $_default_hostname;
	$db['default']['username'] = $_default_username;
	$db['default']['password'] = $_default_password;
	$db['default']['database'] = $_default_database;
	$db['default']['dbdriver'] = 'mysql';
	$db['default']['dbprefix'] = '';
	$db['default']['pconnect'] = TRUE;
	$db['default']['db_debug'] = TRUE;
	$db['default']['cache_on'] = FALSE;
	$db['default']['cachedir'] = '';
	$db['default']['char_set'] = 'utf8';
	$db['default']['dbcollat'] = 'utf8_general_ci';
	$db['default']['swap_pre'] = '';
	$db['default']['autoinit'] = TRUE;
	$db['default']['stricton'] = FALSE;
} elseif ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == "::1") {

	$database_localhost = __DIR__ . '/database-localhost.php';

	if (!file_exists($database_localhost)) {
		show_error("No database connection settings were found in the database config file (database-localhost.php).", 500);
	} else {
		require_once $database_localhost;
	}
}

/*
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = '';
$db['default']['password'] = '';
$db['default']['database'] = '';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;
 */


/* End of file database.php */
/* Location: ./application/config/database.php */