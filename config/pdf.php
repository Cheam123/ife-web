<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => env('APP_NAME', 'Laravel PDF'),
	'subject'               => '',
	'keywords'              => '',
	'creator'               => env('APP_NAME', 'Laravel PDF'),
	'display_mode'          => 'fullpage',
	'tempDir'               => storage_path('app/public/zip/'), //base_path('../temp/'),
	'pdf_a'                 => false,
	'pdf_a_auto'            => false,
	'icc_profile_path'      => ''
];