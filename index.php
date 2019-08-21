<?php

$paths = array(
	"./core",
	"./models",
	"./views",
	"./controllers"	
);

set_include_path(get_include_path() . ":" . implode(":", $paths));

require_once "Application.php";

$config = array (
	"db_dsn"		=> "mysql:dbname=survey_db;host=127.0.0.1",
	"db_user"		=> "survey",
	"db_pass"		=> "survey_pass",
	"db_pconnect"	=> true,
	"db_charset"	=> "utf8",
	"base_url"		=> "/survey",
);

$application = new Application($config);
$application->run();
