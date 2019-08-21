<?php
/*************************************************************************
 * This file takes FRONTEND and BACKEND web URLs.
 *
 * You can either provide Amazon AWS S3 details or FTP details for file
 * storage purpose. Disable FTP for local file storage.
 * 
 * Fow AWS S3 you need to provide bucket name, region, key and secret of 
 * user having access to bucket.
 * 
 * This file also takes the FTP details of USER that has full access 
 * (READ/WRITE/UPDATE/DELETE/DIRECTORY LISTING) to the folder on which 
 * he was given FTP rights.
 * Also this file stores the folder name created within that FTP path
 * of USER and a web-accessible URL of that folder where uploaded files
 * need to be stored by system.
 * e.g. 
 * 1. Create a folder named: FTP_UPLOADS
 * 2. Create a FTP USER with FULL ACCESS on FTP_UPLOADS folder
 * 3. Create a sub-folder in FTP_UPLOADS and name it ATTACHMENTS
 * 4. Create a new DOMAIN/SUB-DOMAIN and point it to ATTACHMENTS folder
 *
 * Details of USER to connect to FTP_UPLOADS will go in ftp_server_details
 * section.
 *
 * Name of sub-folder (ATTACHMENTS) and WEB-URL of DOMAIN/SUB-DOMAIN will
 * go to web_folder_details section.
 *************************************************************************/

return [

	'frontend_url' => 'http://localhost/ecarrefour/multefront/web',	// For localhost: 'http://localhost/MulteCart/multefront/web',
													// dont put trailing slash

	'backend_url' => 'http://localhost/ecarrefour/multeback/web',	// For localhost: 'http://localhost/MulteCart/multeback/web',
														// dont put trailing slash
	
	'aws' => [
		'enabled' => false,	// set it to false if you wish to use FTP method instead
		's3_bucket' => 'multecart',
		'region' => 'us-east-2',
		'user_key' => 'USER_KEY',
		'user_secret' => 'USER_SECRET',
		'web_folder' => 'attachments',	// can be any name without spaces/special characters
		],

	/* Below details are to be used only when AWS is not enabled */

	'ftp' => [
		'enabled' => false,	// Set it to true if you want to upload files to a remote storage using FTP
		],

	/* Below details are to be used only when FTP is enabled */

	'ftp_server_details' => [
		'ftp_protocol' => 'ftp', // can be sftp but untested - use at your own risk!!!
        'ftp_url' =>  'localhost', // don't prepend ftp:// or sftp:// 
		'ftp_user' => 'USER',		// User that can connect to "storage" folder
		'ftp_password' => 'PASSWORD',
		'ftp_port' => '21',
		],

	'web_folder_details' => [
		'web_folder' => 'attachments',		// This is the folder that is web accessible
        'web_url' =>  'http://files.yourwebsite.com', // Web accessible path for "attachments" folder.
																			  // For localhost: 'http://localhost/MulteCart/multeback/web/attachments',
		],
];
