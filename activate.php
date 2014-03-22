<?php

$settings = array(
	'max_users' => 50,
	'delete_users' => 0,
	'ignore_banned_users' => 1,
	'period' => 'fifteenmin',
	'max_last_action' => '-90 days',
	'max_last_login' => '-90 days',
	'max_time_created' => '-90 days',
	'max_time_updated' => '-90 days',
	'max_objects' => '5',
	'max_metadata' => '10',
	'max_annotations' => '1',
	'offset' => 0
);

foreach ($settings as $name => $value) {
	if (!elgg_get_plugin_setting($name, 'stale_users')) {
		elgg_set_plugin_setting($name, $value, 'stale_users');
	}
}

elgg_add_admin_notice('stale_users_enable', elgg_echo('stale_users:activate'));