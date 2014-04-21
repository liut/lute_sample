<?PHP


return [
	'default' => 'demo.passport',
	'demo' => [
		'passport' => [
			'dsn' => 'mysql:host=localhost;dbname=eb_passport',
			'charset' => 'utf8',
			'username' => 'dbreader',
			'password' => '3rYATPyTyMeWGMxm'
		]
		,
		'cms' => [
			'r' => [
				'dsn' => 'mysql:host=localhost;dbname=ebencms',
				'username' => 'db_pp_reader',
				'password' => 'secretpassword'
			]
			,
			'w' => [
				'dsn' => 'mysql:host=localhost;dbname=ebencms',
				'username' => 'db_pp_owner',
				'password' => 'secretpassword'
			]
		]
	]

];
