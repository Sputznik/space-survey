<?php

$screens = array(
	'survey-slug' => array(
		'label' => 'Survey Slug',
		'tab' => plugin_dir_path(__FILE__) . 'settings/survey-slug.php',
	),
	// 'import-questions' => array(
	// 	'label' => 'Import Questions',
	// 	// 'tab' => plugin_dir_path(__FILE__) . 'settings-import-terms.php',
	// 	'action' => 'import-questions',
	// ),
	// 'import-posts' => array(
	// 	'label' => 'Import Posts',
	// 	'tab' => plugin_dir_path(__FILE__) . 'settings-import-posts.php',
	// 	'action' => 'import-posts',
	// ),
	// 'bulk-delete' => array(
	// 	'label' => 'Bulk Delete',
	// 	// 'tab' => plugin_dir_path(__FILE__) . 'settings-bulk-delete.php',
	// 	'action' => 'bulk-delete',
	// ),
);

// $screens = apply_filters('orbit_admin_settings_screens', $screens);

$active_tab = '';
?>
<div class="wrap">
	<h1>Space Settings</h1>
	<h2 class="nav-tab-wrapper">
	<?php
		foreach ($screens as $slug => $screen) {
			$url = admin_url('admin.php?page=space-settings');
			if (isset($screen['action'])) {
				$url = esc_url(add_query_arg(array('action' => $screen['action']), admin_url('admin.php?page=space-settings')));
			}

			$nav_class = "nav-tab";

			if (isset($screen['action']) && isset($_GET['action']) && $screen['action'] == $_GET['action']) {
				$nav_class .= " nav-tab-active";
				$active_tab = $slug;
			}

			if (!isset($screen['action']) && !isset($_GET['action'])) {
				$nav_class .= " nav-tab-active";
				$active_tab = $slug;
			}

			echo '<a href="' . $url . '" class="' . $nav_class . '">' . $screen['label'] . '</a>';
		}
	?>

	</h2>

	<?php

		if (file_exists($screens[$active_tab]['tab'])) {
			include $screens[$active_tab]['tab'];
		}
	?>

</div>
