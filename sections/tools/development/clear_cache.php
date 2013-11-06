<?
if (!check_perms('users_mod') || !check_perms('admin_clear_cache')) {
	error(403);
}

View::show_header('Clear a cache key');

//Make sure the form was sent
if (!empty($_GET['key'])) {
	if ($_GET['submit'] == 'Multi') {
		$Keys = array_map('trim', preg_split('/\s+/', $_GET['key']));
	} else {
		$Keys = [trim($_GET['key'])];
	}
}
if (isset($Keys) && $_GET['type'] == 'clear') {
	foreach ($Keys as $Key) {
		if (preg_match('/(.*?)(\d+)\.\.(\d+)$/', $Key, $Matches) && is_number($Matches[2]) && is_number($Matches[3])) {
			for ($i = $Matches[2]; $i <= $Matches[3]; $i++) {
				$Cache->delete_value($Matches[1].$i);
			}
		} else {
			$Cache->delete_value($Key);
		}
	}
	echo '<div class="save_message">Key(s) ' . implode(', ', array_map('display_str', $Keys)) . ' cleared!</div>';
}
?>
	<div class="header">
		<h2>Clear a cache key</h2>
	</div>
	<form class="manage_form" name="cache" method="get" action="">
		<input type="hidden" name="action" value="clear_cache" />
		<table class="layout" cellpadding="2" cellspacing="1" border="0" align="center">
			<tr>
				<td>Key</td>
				<td>
					<select name="type">
						<option value="view">View</option>
						<option value="clear">Clear</option>
					</select>
					<input type="text" name="key" id="key" class="inputtext" value="<?=(isset($_GET['key']) && $_GET['submit'] != 'Multi' ? display_str($_GET['key']) : '')?>" />
					<input type="submit" name="submit" value="Single" class="submit" />
				</td>
			</tr>
			<tr>
				<td>Multi-key</td>
				<td>
					<select name="type">
						<option value="view">View</option>
						<option value="clear">Clear</option>
					</select>
					<textarea type="text" name="key" id="key" class="inputtext"><?=(isset($_GET['key']) && $_GET['submit'] == 'Multi' ? display_str($_GET['key']) : '')?></textarea>
					<input type="submit" name="submit" value="Multi" class="submit" />
				</td>
			</tr>
<?
if (isset($Keys) && $_GET['type'] == 'view') {
	foreach ($Keys as $Key) {
?>
			<tr>
				<td><?=display_str($Key)?></td>
				<td>
					<pre><? var_dump($Cache->get_value($Key)); ?></pre>
				</td>
			</tr>
<?
	}
}
?>
		</table>
	</form>
<?
View::show_footer();
