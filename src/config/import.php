<?php

$imports = [
	'db_table_prefix' => '',
	'get_import_route' => '/import',
	'post_import_route' => '/import',
	'roles' => [
		'admin' => [
			'enabled' => true
		],
		'user' => [
			'enabled' => false
		],
	],
];

return $imports;
