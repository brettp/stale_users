<?php
/**
 * 	en.php - English language variables
 */
return array(
	'admin:users:stale_users' => "Stale users",

	'stale_users:activate' => 'Stale Users is active, but will not operate until you configure its settings',

	'stale_users:delete_users' => 'Enabled. (This setting will DELETE USERS!)',
	'stale_users:ignore_banned_user' => 'Do not delete banned users',
	'stale_users:email_domains' => 'Only check users registered with these email domains (comma separated, empty means all):',
	'stale_users:period' => 'Cron period to run on: ',
	'stale_users:users' => 'Number of users to process per period',
	'stale_users:last_action' => 'Ignore users who have performed and action more recently than:',
	'stale_users:last_login' => 'Ignore users who have logged in more recently than:',
	'stale_users:time_created' => 'Ignore users created more recently than:',
	'stale_users:time_updated' => 'Ignore users updated more recent than than:',
	'stale_users:objects' => 'Ignore users who own more than this many objects:',
	'stale_users:annotations' => 'Ignore users who own more than this many annotations:',
	'stale_users:metadata' => 'Ignore users who own more than this many metadata entries:',

);
