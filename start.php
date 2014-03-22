<?php
/**
 * Deletes users that match certain conditions
 */

elgg_register_event_handler('init', 'system', 'stale_users_init');

/**
 * Init
 */
function stale_users_init() {
	elgg_register_admin_menu_item('administer', 'stale_users', 'users');

	if (elgg_get_plugin_setting('delete_users', 'stale_users')) {
		$period = elgg_get_plugin_setting('period', 'stale_users');
		elgg_register_plugin_hook_handler('cron', $period, 'stale_users_cron');
	}

	// reset offset if settings updated
	elgg_register_plugin_hook_handler('action', 'plugins/settings/save', 'stale_users_reset_offset');
}

/**
 * Reset offset if params have changed
 *
 * @param type $hook
 * @param type $type
 * @param type $value
 * @param type $params
 * @return type
 */
function stale_users_reset_offset($hook, $type, $value, $params) {
	$plugin_id = get_input('plugin_id');
	if ($plugin_id != 'stale_users') {
		return $value;
	}

	elgg_set_plugin_setting('offset', 0, 'stale_users');
}

function stale_users_cron() {
//	elgg_set_plugin_setting('offset', 0, 'stale_users');
//	exit;
	$db_prefix = elgg_get_config('dbprefix');
	$limit = elgg_get_plugin_setting('max_users', 'stale_users');
	$offset = elgg_get_plugin_setting('offset', 'stale_users');
	
	$options = array(
		'type' => 'user',
		'limit' => $limit,
		'wheres' => array(
			'ue.admin = "no"',
			'ue.email LIKE "%@hotmail.com" or ue.email LIKE "%@outlook.com"'
			),
		'joins' => array("JOIN {$db_prefix}users_entity ue on ue.guid = e.guid"),
		'offset' => $offset,
		'order_by' => 'e.guid asc'
	);

	if (elgg_get_plugin_setting('ignore_banned_users', 'stale_users')) {
		$options['wheres'][] = "banned = 'no'";
	}

	// time stamps
	$text_inputs = array(
		'last_action',
		'last_login',
		'time_created',
		'time_updated'
	);

	foreach ($text_inputs as $type) {
		$setting_name = "max_{$type}";
		$time = elgg_get_plugin_setting($setting_name, 'stale_users');
		$ts = strtotime($time);

		if ($time && $ts) {
			if ($type == 'last_action') {
				$options['wheres'][] = "ue.$type < $ts";
			} else {
				$options['wheres'][] = "$type < $ts";
			}
		}
	}

	// we can't have it fetching extra chunks because we don't know the offset
	// set chunk size to the max limit
	$batch = new ElggBatch('elgg_get_entities', $options, null, $limit);

	foreach ($batch as $user) {
		$info = array(
			'guid' => $user->guid,
			'name' => "$user->name ($user->username)",
			'url' => $user->getURL(),
			'email' => $user->email,
			'time_created' => date('r', $user->time_created),
			'time_updated' => date('r', $user->time_updated),
			'last_action' => $user->last_action ? date('r', $user->last_action) : 'Never',
			'last_login' => $user->last_login ? date('r', $user->last_login) : 'Never',
		);

		$delete = true;

		$count_fields = array(
			'max_objects' => 'entities',
			'max_annotations' => 'annotations',
			'max_metadata' => 'metadata'
		);
		
		foreach ($count_fields as $name => $table_name) {
			$max = elgg_get_plugin_setting($name, 'stale_users');

			$q = "SELECT count(*) as count from {$db_prefix}{$table_name} WHERE owner_guid = {$user->getGUID()}";
			$data = get_data_row($q);

			$info[$table_name] = (int) $data->count;

			if ($max === null) {
				continue;
			}

			if ($data->count > $max) {
				elgg_set_plugin_setting('offset', ++$offset, 'stale_users');
				$delete = false;
			}
		}

		if ($delete) {
			$info['delete'] = 1;
			var_dump($info);
			// delete
		} else {
			$info['delete'] = 0;
		}
	}

	var_dump("Offset: $offset");
}


