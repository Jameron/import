<?php

$imports = [
	'db_table_prefix' => '',
	'post_upload_route' => '/import',
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
