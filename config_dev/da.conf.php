<?PHP


return array(
	'default' => 'eb.passport',
	'eb' => array(
		'passport' => array(
			'dsn' => 'mysql:host=localhost;dbname=eb_passport',
			'charset' => 'utf8',
			'username' => 'dbreader',
			'password' => '3rYATPyTyMeWGMxm'
		)
		,
		'cms' => array(
			'r' => array(
				'dsn' => 'mysql:host=localhost;dbname=ebencms',
				'username' => 'db_pp_owner',
				'password' => 'secretpassword'
			)
			,
			'w' => array(
				'dsn' => 'mysql:host=localhost;dbname=ebencms',
				'username' => 'db_pp_owner',
				'password' => 'secretpassword'
			)
		)
	)
	
);