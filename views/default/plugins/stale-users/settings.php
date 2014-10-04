<?php
/**
 * Settings for deleting stale users
 */

$settings = elgg_extract('entity', $vars);


$options = array(
	'name' => 'params[delete_users]',
	'value' => 1,
);

if ($settings->delete_users) {
	$options['checked'] = 'checked';
}

$enabled_input = elgg_view('input/checkbox', $options);


$options = array(
	'name' => 'params[ignore_banned_users]',
	'value' => 1
);

if ($settings->ignore_banned_users) {
	$options['checked'] = 'checked';
}

$banned_users_input = elgg_view('input/checkbox', $options);

$email_domains_input = elgg_view('input/text', array(
	'name' => 'params[email_domains]',
	'value' => $settings->email_domains
));


$allowed_periods = array(
	'minute', 'fiveminute', 'fifteenmin', 'halfhour', 'hourly',
	'daily', 'weekly', 'monthly', 'yearly', 'reboot'
);
$period_input = elgg_view('input/dropdown', array(
	'options' => $allowed_periods,
	'value' => $settings->period,
	'name' => 'params[period]'
));

$text_inputs = array(
	'users',
	'last_action',
	'last_login',
	'time_created',
	'time_updated',
	'objects',
	'metadata',
	'annotations'
);

$text_inputs_str = '';

foreach ($text_inputs as $type) {
	$setting_name = "max_{$type}";
	$label = elgg_echo("stale_users:$type");
	
	$input = elgg_view('input/text', array(
		'name' => "params[$setting_name]",
		'value' => $settings->$setting_name,
	));

	$text_inputs_str .= <<<___HTML

<div>
	<label>
		$label
		$input
	</label>
</div>

___HTML;
}

?>
<div>
	<label>
		<?php
			echo $enabled_input;
			echo elgg_echo('stale_users:delete_users');
		?>
	</label>
</div>

<div>
	<label>
		<?php
			echo $banned_users_input;
			echo elgg_echo('stale_users:ignore_banned_user');
		?>
	</label>
</div>

<div>
	<label>
		<?php
			echo elgg_echo('stale_users:email_domains');
			echo $email_domains_input;
		?>
	</label>
</div>


<div>
	<label>
		<?php
			echo elgg_echo('stale_users:period');
			echo $period_input;
		?>
	</label>
</div>

<?php
echo $text_inputs_str;