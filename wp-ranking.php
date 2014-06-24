<?php
/*
Plugin Name: Wordpress List Ranking Plugin
Plugin URI: http://wordpress.org
Description: For a version without credits email kurt@fantasyknuckleheads.com - Credits only show if you enable advanced features - Useful for rankings and list of anything you desire but optimized for ranking athletes and sports teams. 
Version: 2.2
Author: kutu62
Author URI: http://fantasyknuckleheads.com
*/
if ( ! function_exists('wp_ranking_post_types') ) {
// Register Custom Post Types
function wp_ranking_post_types() {
	// Register Lists
	$labels = array(
		'name'                => _x( 'Player lists', 'Post Type General Name', 'wp-ranking' ),
		'singular_name'       => _x( 'Player list', 'Post Type Singular Name', 'wp-ranking' ),
		'menu_name'           => __( 'Player lists', 'wp-ranking' ),
		'parent_item_colon'   => __( 'Parent list:', 'wp-ranking' ),
		'all_items'           => __( 'Lists', 'wp-ranking' ),
		'view_item'           => __( 'View list', 'wp-ranking' ),
		'add_new_item'        => __( 'Add New List', 'wp-ranking' ),
		'add_new'             => __( 'New List', 'wp-ranking' ),
		'edit_item'           => __( 'Edit List', 'wp-ranking' ),
		'update_item'         => __( 'Update List', 'wp-ranking' ),
		'search_items'        => __( 'Search lists', 'wp-ranking' ),
		'not_found'           => __( 'No lists found', 'wp-ranking' ),
		'not_found_in_trash'  => __( 'No lists found in Trash', 'wp-ranking' ),
	);
	$args = array(
		'label'               => __( 'player_list', 'wp-ranking' ),
		'description'         => __( 'Player list', 'wp-ranking' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'taxonomies'          => array( 'list_category' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => 'ranker',
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'menu_position'       => 100,
		'menu_icon'           => plugins_url('/images/icon_16.png', __FILE__),
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'player_list', $args );
	// Register Rankers
	$labels = array(
			'name'                => _x( 'Rankers', 'Post Type General Name', 'wp-ranking' ),
			'singular_name'       => _x( 'Ranker', 'Post Type Singular Name', 'wp-ranking' ),
			'menu_name'           => __( 'Ranker', 'wp-ranking' ),
			'parent_item_colon'   => __( 'Parent Ranker:', 'wp-ranking' ),
			'all_items'           => __( 'Rankers', 'wp-ranking' ),
			'view_item'           => __( 'View Ranker', 'wp-ranking' ),
			'add_new_item'        => __( 'Add New Ranker', 'wp-ranking' ),
			'add_new'             => __( 'New Ranker', 'wp-ranking' ),
			'edit_item'           => __( 'Edit Ranker', 'wp-ranking' ),
			'update_item'         => __( 'Update Ranker', 'wp-ranking' ),
			'search_items'        => __( 'Search rankers', 'wp-ranking' ),
			'not_found'           => __( 'No rankers found', 'wp-ranking' ),
			'not_found_in_trash'  => __( 'No rankers found in Trash', 'wp-ranking' ),
		);
		$args = array(
			'label'               => __( 'ranker', 'wp-ranking' ),
			'description'         => __( 'Rankers', 'wp-ranking' ),
			'labels'              => $labels,
			'supports'            => array( 'title', ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'ranker',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'menu_icon'           => plugins_url('/images/icon_16.png', __FILE__),
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
		);
		register_post_type( 'ranker', $args );
}
// Hook into the 'init' action
add_action( 'init', 'wp_ranking_post_types', 0 );
}
if ( ! function_exists('list_category') ) {
// Register List Categoties
function list_category()  {
	$labels = array(
		'name'                       => _x( 'List categories', 'Taxonomy General Name', 'wp-ranking' ),
		'singular_name'              => _x( 'List category', 'Taxonomy Singular Name', 'wp-ranking' ),
		'menu_name'                  => __( 'List Categories', 'wp-ranking' ),
		'all_items'                  => __( 'All List Categories', 'wp-ranking' ),
		'parent_item'                => __( 'Parent List Category', 'wp-ranking' ),
		'parent_item_colon'          => __( 'Parent List Category:', 'wp-ranking' ),
		'new_item_name'              => __( 'New List Category Name', 'wp-ranking' ),
		'add_new_item'               => __( 'Add New List Category', 'wp-ranking' ),
		'edit_item'                  => __( 'Edit List Category', 'wp-ranking' ),
		'update_item'                => __( 'Update List Category', 'wp-ranking' ),
		'separate_items_with_commas' => __( 'Separate list categories with commas', 'wp-ranking' ),
		'search_items'               => __( 'Search list categories', 'wp-ranking' ),
		'add_or_remove_items'        => __( 'Add or remove list categories', 'wp-ranking' ),
		'choose_from_most_used'      => __( 'Choose from the most used list categories', 'wp-ranking' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'list_category', 'player_list', $args );
}
register_taxonomy_for_object_type( 'list_category', 'player_list' );
// Hook into the 'init' action
add_action( 'init', 'list_category', 0 );
}
// Adding a shortocde [ranker id="id"]
function ranker_shortcode( $atts ) {
	$id = $atts['id']; // Get ranker id
	$rankings = get_post_meta($id, '_rankings'); // Get rankings
	$rankings = $rankings[0]; 
	$list_id = get_post_meta($id, '_list', true); // Get list id
	$players = get_post_meta($list_id, '_players'); // Get players from list
	$players = $players[0];
	ob_start();
	?>
	<style type="text/css">
		.ranked-user {
			padding: 5px;
		}
	</style>
	<?php
	global $rankers_counter;
	if (!isset($rankers_counter)) {
		$rankers_counter = 0;
	} 
	$rankers_counter++;
	echo '<table id="ranker' . $rankers_counter . '">';
	echo '<thead>';
	echo '<tr>';
	echo '<th></th>';
	$authors_count = 0;
	foreach ($rankings as $author => $data) {
		$authors_count++;
		echo '<th class="author" onclick="sortTable(' . $authors_count . ', \'ranker' . $rankers_counter . '\')" title="' . __( 'Click to sort by this author', 'wp-ranking' ) . '">';
		if (get_option( 'show-avatars' )) echo get_avatar( $author, 50 );
		$user_info = get_userdata($author);
		echo '<br>';
		echo '<span class="ranked-user">' . $user_info->display_name . '</span>';
		echo '</th>';		
	}
if ($authors_count > 1) echo '<th class="author" onclick="sortTable(' . ($authors_count + 1) . ', \'ranker' . $rankers_counter . '\')" title="' . __( 'Click to sort by composite ranking', 'wp-ranking' ) . '">' . __( 'Composite', 'wp-ranking' ) . '</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	$player_counter = 1;
	foreach ($players as $player) {
		if ($player_counter % 2 == 0) $class = 'even';
		else $class = 'odd';
		$player_counter++;
		echo '<tr class="' . $class . '" id="player-' . $player['id'] . '">';
		echo '<td class="player">';
		echo $player['name'];
		if ($player['team'] != '') echo ', ' . $player['team'];
		if ($player['position'] != '') echo ', ' . $player['position'];
		echo '</td>';
		$composite = 0;
		$i = 0;
		foreach ($rankings as $ranking) {
			echo '<td align="center">';
			foreach ($ranking['data'] as $rank => $player_id) {
				if ($player_id == $player['id']) {
					echo $rank + 1;
					$composite = $composite + $rank + 1;
					$i++;					
				}
			}
			echo '</td>';
		}
		if ($authors_count > 1) {
			echo '<td class="sort" align="center">';
			echo round($composite / $i);
			echo '</td>';
		}
		echo '</tr>';
		if (round($composite / $i) == 0) echo '<script>element = document.getElementById("player-' . $player['id'] . '"); element.parentNode.removeChild(element);</script>';
	}
	echo '</tbody>';
	echo '</table>';
	if (get_option( 'show-comments' )) {
		foreach ($rankings as $author => $data) {
			if ($data['comment'] != ''){
				echo '<div class="author-comment">';
				echo get_avatar( $author, 32 );
				echo $data['comment'];
				echo '</div>';
			}
		}
	}
	if (get_option( 'show-comments' ) || get_option( 'show-avatars' ) || get_option( 'allow-authors' )) {
		echo '<br><p style="font-size:85%;">Want rankings like this? <a href="http://wordpress.org/plugins/sports-rankings-lists/">Download Plugin</a> or read more @ <a href="http://fantasyknuckleheads.com/" title="Fantasy Football for all you Knuckleheads">Fantasy Knuckleheads</a></p>';
	}
	// Table sorting function
	?>
	<script>
	    var authorsCount = <?php echo $authors_count; ?>;
	    	if (authorsCount == 1) {
	    		sortTable(1, 'ranker<?php echo $rankers_counter; ?>');
	    	}
	    	else {
	    		sortTable(authorsCount + 1, 'ranker<?php echo $rankers_counter; ?>');
	    	}
	</script>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output ;
}
add_shortcode( 'ranker', 'ranker_shortcode' );
function wp_ranking_styles() {
if ( is_single() ) {
	wp_enqueue_script('ranker-script', plugins_url( '/js/scripts.js' , __FILE__ ));
    wp_register_style( 'wp-ranking-style', plugins_url('/css/wp-ranking.css', __FILE__) );
    wp_enqueue_style( 'wp-ranking-style' );
}}
add_action('wp_enqueue_scripts', 'wp_ranking_styles');
include( plugin_dir_path( __FILE__ ) . 'admin.php');
if (function_exists('load_plugin_textdomain'))
	{
		load_plugin_textdomain('wp-ranking', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}
?>
