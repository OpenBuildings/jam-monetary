<?php 

require_once __DIR__.'/../vendor/autoload.php';

Kohana::modules(array(
	'database'  => MODPATH.'database',
	'jam'       => __DIR__.'/../modules/jam',
	'jam-monetary' => __DIR__.'/..',
));

spl_autoload_register(function($class)
{
	$file = __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.str_replace('_', '/', $class).'.php';

	if (is_file($file))
	{
		require_once $file;
	}
});

Kohana::$config
	->load('database')
		->set('default', array(
			'type'       => 'PDO',
			'connection' => array(
                'dsn' => 'mysql:host=localhost;dbname=test-jam-monetary',
				'username'   => 'root',
				'password'   => '',
				'persistent' => TRUE,
			),
            'identifier' => '`',
			'table_prefix' => '',
			'charset'      => 'utf8',
			'caching'      => FALSE,
		));

Kohana::$environment = Kohana::TESTING;
