<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
// Create custom plugin's settings menu
function ranker_menu() {
    add_menu_page(__( 'Ranker', 'wp-ranking' ), __( 'Ranker Admin', 'wp-ranking' ), 'edit_pages', 'ranker', 'ranker', plugins_url('/images/icon_16.png', __FILE__) ); // This menu is for admins only
    add_submenu_page('ranker',__( 'Ranker Settings', 'wp-ranking' ), __( 'Ranker Settings', 'wp-ranking' ), 'activate_plugins', 'ranker_settings', 'ranker_settings' ); // Plugin settings
    add_menu_page(__( 'Rankings', 'wp-ranking' ), __( 'Rankings', 'wp-ranking' ), 'publish_posts', 'rankings', 'rankings', plugins_url('/images/icon_16.png', __FILE__), 7 ); //This is for authors
}
add_action('admin_menu', 'ranker_menu');
add_action( 'admin_init', 'ranker_admin_init' );
function ranker_admin_init() {
    add_settings_section( 'main-section', '', 'main_section_callback', 'ranker-plugin' );
    add_settings_field( 'show-avatars', 'Show Avatars', 'show_avatars_callback', 'ranker-plugin', 'main-section' );
    add_settings_field( 'show-comments', 'Show Comments', 'show_comments_callback', 'ranker-plugin', 'main-section' );
    add_settings_field( 'allow-authors', 'Allow all users to rank (not only admins)', 'allow_authors_callback', 'ranker-plugin', 'main-section' );
    register_setting( 'ranker-plugin', 'show-avatars' );
    register_setting( 'ranker-plugin', 'show-comments' );
    register_setting( 'ranker-plugin', 'allow-authors' );
}
function main_section_callback() { 
}
function show_avatars_callback() {
    $setting = esc_attr( get_option( 'show-avatars' ) );
    echo '<input type="checkbox" name="show-avatars" value="1" ' . checked(1, $setting, false) . ' />';
}
function show_comments_callback() {
    $setting = esc_attr( get_option( 'show-comments' ) );
    echo '<input type="checkbox" name="show-comments" value="1" ' . checked(1, $setting, false) . ' />';
}
function allow_authors_callback() {
    $setting = esc_attr( get_option( 'allow-authors' ) );
    echo '<input type="checkbox" name="allow-authors" value="1" ' . checked(1, $setting, false) . ' />';
}
//Page for plugin's settings
function ranker_settings() {
	?>
	    <div class="wrap">
	        <h2>Ranker Plugin Options</h2>
	        <form action="options.php" method="POST">
	            <?php settings_fields( 'ranker-plugin' ); ?>
	            <?php do_settings_sections( 'ranker-plugin' ); ?>
<h3>READ THIS: view the screenshots <a href="http://wordpress.org/plugins/sports-rankings-lists/screenshots/">here</a> so you know how to use this plugin!</h3>
	            <p><strong>Note</strong>: enabling any of these features will enable links to help promote this plugin. For a version without credits email kurt@fantasyknuckleheads.com</p>
	            <?php submit_button(); ?>
	        </form>
	    </div>
	    <?php
} 
//Page for ranking players
function rankings() { 
    if ( !current_user_can( 'publish_posts' ))  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        if (!get_option( 'allow-authors' ) && !current_user_can( 'activate_plugins' )) {
        	wp_die( __( 'This feature is only available for admins. You need to enable it from Ranker\'s settings before authors and editors can use it.' ) );
        }
        global $current_user;
        get_currentuserinfo();

        ?>
        <div class="wrap">
        <?php if (!isset($_GET['ranker'])) { // if no ranker was chosen, show the list of all rankers ?>

        <div id="icon-ranker" class="icon32 icon32-posts-player_list"><br></div><h2><?php _e( 'Click the Rankings below to rank players', 'wp-ranking' ); ?></h2>
        <?php
        $args = array( 'post_type' => 'ranker', 'posts_per_page' => -1 ); 

        $rankers = get_posts( $args );
        ?>
        <table class="wp-list-table widefat fixed posts rankings" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e( 'Title', 'wp-ranking' ); ?></th>
                    <th><?php _e( 'List', 'wp-ranking' ); ?></th>
                    <th><?php _e( 'Status', 'wp-ranking' ); ?>Status</th>
                    <th><?php _e( 'Date Created', 'wp-ranking' ); ?></th>
                </tr>
            </thead>
        <?php
        if ($rankers) {
          foreach ( $rankers as $ranker ) {
                  echo '<tr>';
                  echo '<td>';
                  echo '<a href="?page=rankings&ranker=' . $ranker->ID . '">';
                  echo $ranker->post_title;
                  echo '</a>';
                  echo '</td>';
                  echo '<td>';
                  $list = get_post(get_post_meta( $ranker->ID, '_list', true)); 
                  echo $list->post_title;
                  echo '</td>';
                  echo '<td>';
                  $rankings = get_post_meta( $ranker->ID, '_rankings', true); 
                  if(isset($rankings[$current_user->ID]['data'])) _e( 'Ranked', 'wp-ranking' );
                  else _e( 'New', 'wp-ranking' ); //Check if this user had already ranked this ranker
                  echo '</td>';
                  echo '<td>';
                  echo get_the_time( get_option('date_format'), $ranker->ID );
                  echo '</td>';
                  echo '</tr>';
              }
        }
        ?>
        </table>
        <?php
        }
        else { //If a ranker was chosen, show players list
            $ranker = get_post($_GET['ranker']);
        ?>
        <h2><?php echo $ranker->post_title; ?></h2>
        <h3><?php _e( 'Drag and drop players in the desired order', 'wp-ranking' ); ?></h3>
        <?php 
        $list = get_post(get_post_meta( $ranker->ID, '_list', true));
        $players = get_post_meta( $list->ID, '_players', false);
        $ranking = get_post_meta( $ranker->ID, '_rankings', false);
        $ranking = $ranking[0];
        if (isset($_POST['reset']) || isset($_GET['reset'])) {
          unset($ranking[$current_user->ID]);
        	update_post_meta($ranker->ID, '_rankings', $ranking); // If reset button was clicked
        }
        if (isset($_POST['ranking']) && !isset($_POST['reset'])) { //If "Save changes" button was clicked
            $ranking[$current_user->ID] = $_POST['ranking']; // Add data to current rankings
            add_post_meta($ranker->ID, '_rankings', $ranking, true) or
                update_post_meta($ranker->ID, '_rankings', $ranking); // Save the data
            $ranking = get_post_meta( $ranker->ID, '_rankings', false); // Get cleaned and validated data back
            $ranking = $ranking[0];
        }
        if (!empty($ranking)) {
            if(isset($ranking[$current_user->ID]['data'])) { //If this user ranked player before, get his order of players and overwrite the default $players array
                foreach ($ranking[$current_user->ID]['data'] as $player) {
                    foreach ($players[0] as $player_data) {
                        if ($player_data['id'] == $player) {
                            $temp_players[] = $player_data;
                        }
                    }
                }
                $players[0] = $temp_players;
            }
        }
        if (!empty($players[0])) {
            ?>
            <form action="" method="POST">
            <table class="wp-list-table widefat fixed posts" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php _e( 'Rank', 'wp-ranking' ); ?></th>
                        <th><?php _e( 'Name', 'wp-ranking' ); ?></th>
                        <th><?php _e( 'Team', 'wp-ranking' ); ?></th>
                        <th><?php _e( 'Position', 'wp-ranking' ); ?></th>
                        <th><?php _e( 'Delete', 'wp-ranking' ); ?></th>
                    </tr>
                </thead>
                <tbody id="sortable" >
                <?php
                $i = 1;
            foreach ($players[0] as $player) {
                echo '<tr>';
                echo '<td class="rank">';
                echo $i++;
                echo '</td>';
                echo '<td>';
                echo $player['name'];
                echo '<input type="hidden" name="ranking[data][]" value="' . $player['id'] . '" />';
                echo '</td>';
                echo '<td>';
                echo $player['team'];
                echo '</td>';
                echo '<td>';
                echo $player['position'];
                echo '</td>';
                echo '<td>';
                echo '<span class="button remove">' . __( 'Delete', 'wp-ranking' ) . '</span>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
                </tbody>
            </table>
            <h3 style="margin-top:30px;"><?php _e( 'Add Comment (optional)', 'wp-ranking' ); ?></h3>
            <?php
            wp_editor( $ranking[$current_user->ID]['comment'], 'content-id', array( 'textarea_name' => 'ranking[comment]', 'media_buttons' => false, 'tinymce_adv' => array( 'width' => '300', 'theme_advanced_buttons1' => 'formatselect,forecolor,|,bold,italic,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,|,spellchecker,wp_fullscreen,wp_adv' ) ) );
            ?>
            <p><input type="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'wp-ranking' ); ?>"> <input onclick="return confirm('<?php _e( 'This can not be undoe, are you sure?', 'wp-ranking' ); ?>')" type="submit" class="button" name="reset" value="<?php _e( 'Reset to defaults', 'wp-ranking' ); ?>"></p>
            </form>
            <script>
            // fix for table sorting 
            jQuery(function() {
              var fixHelperModified = function(e, tr) {
                  var $originals = tr.children();
                  var $helper = tr.clone();
                  $helper.children().each(function(index)
                  {
                    jQuery(this).width($originals.eq(index).width())
                  });
                  return $helper;
              };
              jQuery("#sortable").sortable({
                  helper: fixHelperModified, 
                  update: function(event, ui) {
                  			var i = 1;
                  			jQuery('.rank').each(function() {
                  			    jQuery(this).html(i);
								i++;
                  			});				
                          }
              }).disableSelection();
              jQuery('.remove').live('click', function() {
                 jQuery(this).parent().parent().remove();
               });
            });
            </script>
            <?php
        }
        ?>
        </div>
        <?php
        }
}
/* Define the custom box */
add_action( 'add_meta_boxes', 'wp_ranker_add_custom_boxes' );
/* Do something with the data entered */
add_action( 'save_post', 'wp_ranker_save_postdata' );
/* Adds a box to the main column on the Post and Page edit screens */
function wp_ranker_add_custom_boxes() {
    add_meta_box(
        'wp_ranker_sectionid',
        __( 'Players', 'wp-ranking' ),
        'wp_ranker_players_custom_box',
        'player_list','normal','high'
    );
    add_meta_box(
        'wp_ranker_sectionid',
        __( 'Player List', 'wp-ranking' ),
        'wp_ranker_player_list_custom_box',
        'ranker','normal','high'
    );
}
/* Prints the box content */
function wp_ranker_players_custom_box( $post ) {
  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'wp_ranker_noncename' );
  // The actual fields for data entry
  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
  $players = get_post_meta( $post->ID, '_players');
  echo '<ul id="sortable">';
  $i = 0;
  if (!empty($players[0])) foreach ($players[0] as $player) {
    echo '<li><input type="hidden" name="wp_ranker_players[' . $i . '][id]" value="'.esc_attr($player['id']).'" /><input type="text" placeholder="' . __( 'Default Rank', 'wp-ranking' ) . '" name="wp_ranker_players[' . $i . '][default_rank]" value="'.esc_attr($player['default_rank']).'" size="25" /> <input type="text" placeholder="' . __( 'Name', 'wp-ranking' ) . '" name="wp_ranker_players[' . $i . '][name]" value="'.esc_attr($player['name']).'" size="25" /> <input type="text" placeholder="' . __( 'Team', 'wp-ranking' ) . '" name="wp_ranker_players[' . $i . '][team]" value="'.esc_attr($player['team']).'" size="25" />  <input type="text" placeholder="' . __( 'Position', 'wp-ranking' ) . '" name="wp_ranker_players[' . $i . '][position]" value="'.esc_attr($player['position']).'" size="25" /> <span class="button remove">' . __( 'Remove', 'wp-ranking' ) . '</span> <span class="drag-icon"></span></li>';
    $i++;
  }
  echo '</ul>';
  echo '<p><span class="button add">' . __( 'Add Player', 'wp-ranking' ) . '</span></p>';
  ?>
  <p><span id="upload_file_button" class="button"><?php _e( 'Import from csv file', 'wp-ranking' ); ?></span></p>
  <p><?php _e( 'You can upload CSV file in a format like <strong>Default rank,Player name,Team,Position</strong> - one player per line.', 'wp-ranking' ); ?></p>
  <p><a href="<?php echo plugins_url('/example.csv', __FILE__); ?>"><?php _e( 'Download the example file', 'wp-ranking' ); ?></a>.</p>
  <script>
  jQuery(function() {
    jQuery( "#sortable" ).sortable({ handle: ".drag-icon" });
    jQuery('.add').live('click', function() {
        var rand = Math.floor((Math.random()*999999)+1);
        jQuery( "#sortable" ).append('<li><input type="hidden" name="wp_ranker_players[' + rand + '][id]" value="' + rand + '" /><input type="text" placeholder="<?php _e( 'Default Rank', 'wp-ranking' ); ?>" name="wp_ranker_players[' + rand + '][default_rank]" value="" size="25" /> <input type="text" placeholder="<?php _e( 'Name', 'wp-ranking' ); ?>" name="wp_ranker_players[' + rand + '][name]" value="" size="25" /> <input type="text" placeholder="<?php _e( 'Team', 'wp-ranking' ); ?>" name="wp_ranker_players[' + rand + '][team]" value="" size="25" />  <input type="text" placeholder="<?php _e( 'Position', 'wp-ranking' ); ?>" name="wp_ranker_players[' + rand + '][position]" value="" size="25" /> <span class="button remove"><?php _e( 'Remove', 'wp-ranking' ); ?></span> <span class="drag-icon"></span></li>');
     });
    jQuery('.remove').live('click', function() {
       jQuery(this).parent().remove();
     });
  });
  jQuery(document).ready(function() {
   
  jQuery('#upload_file_button').click(function() {
    if (confirm('<?php _e( 'Upload file?', 'wp-ranking' ); ?>')){
      formfield = jQuery('#upload_file').attr('name');
     tb_show('', 'media-upload.php?type=file&amp;TB_iframe=true');
     return false;
    }
  });
  window.send_to_editor = function(html) {
   url = jQuery(html).attr('href');
   tb_remove();
   import_players(url);
  }
  });
  function import_players(url) {
    jQuery.ajax({
    type: "POST",
    url: "<?php echo plugins_url( 'import_players.php' , __FILE__ ) ?>",
    data: {url: url}, 
    cache: false,
    success: function(result){
        jQuery( "#sortable" ).html(result);   
    },
    error: function(error){
        jQuery( "#sortable" ).html(error.responseText);
    }
    });
  }
  </script>
  <?php
}
/* Prints the box content */
function wp_ranker_player_list_custom_box( $post ) {
  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'wp_ranker_noncename' );
  // The actual fields for data entry
  // Use get_post_meta to retrieve an existing value from the database and use the value for the form
  $list_value = get_post_meta( $post->ID, '_list', true);
  $args = array( 'post_type' => 'player_list', 'posts_per_page' => -1 ); 
    echo '<select name="wp_ranker_list">';
  $lists = get_posts( $args );
  if ($lists) {
    foreach ( $lists as $list ) {
            echo '<option value="' . $list->ID . '" ' . selected($list_value, $list->ID, false) . ' >';
            echo $list->post_title;
            echo '</option>';
        }
  }
  echo '</select>';
}
/* When the post is saved, saves our custom data */
function wp_ranker_save_postdata( $post_id ) {
  // First we need to check if the current user is authorised to do this action. 
  if ( ! current_user_can( 'edit_pages', $post_id ) )
      return;
  // Secondly we need to check if the user intended to change this value.
  if ( ! isset( $_POST['wp_ranker_noncename'] ) || ! wp_verify_nonce( $_POST['wp_ranker_noncename'], plugin_basename( __FILE__ ) ) )
      return;
  // Thirdly we can save the value to the database
  //if saving in a custom table, get post_ID
  $post_ID = $_POST['post_ID'];
  if (isset($_POST['wp_ranker_players'])) {
    $players_list = array_values($_POST['wp_ranker_players']);
    // Do something with $players_list 
    // either using 
    add_post_meta($post_ID, '_players', $players_list, true) or
      update_post_meta($post_ID, '_players', $players_list);
    // or a custom table (see Further Reading section below)
  }
  if (isset($_POST['wp_ranker_list'])) {
    $list = $_POST['wp_ranker_list'];
    add_post_meta($post_ID, '_list', $list, true) or
      update_post_meta($post_ID, '_list', $list);
  }
}
// Change the columns for the edit ranker screen
function change_columns( $cols ) {
    $cols = array(
        'cb'       => '<input type="checkbox" />',
        'title'      => __( 'Title', 'wp-ranking' ),
        'list' => __( 'Player List', 'wp-ranking' ),
        'shortcode' => __( 'Shortcode', 'wp-ranking' ),
        'date'     => __( 'Date', 'wp-ranking' ),
      );
    $cols['shortcode'] = __( 'Shortcode', 'wp-ranking' );
    print_r($cols);
  return $cols;
}
add_filter( "manage_ranker_posts_columns", "change_columns" );
function custom_columns( $column, $post_id ) {
  switch ( $column ) {
    case "shortcode":
      echo '[ranker id="' . $post_id . '"]';
      break;
    case "list":
      $list = get_post(get_post_meta( $post_id, '_list', true)); 
      echo $list->post_title;
      break;

  }
}
add_action( "manage_posts_custom_column", "custom_columns", 10, 2 );
function wp_ranking_admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_script('jquery-ui-sortable');
}
function wp_ranking_admin_styles() {
    wp_register_style( 'wp-ranking-admin-style', plugins_url('/css/wp-ranking-admin.css', __FILE__) );
    wp_enqueue_style( 'wp-ranking-admin-style' );
    wp_enqueue_style('thickbox');
}
add_action('admin_print_scripts', 'wp_ranking_admin_scripts');
add_action('admin_print_styles', 'wp_ranking_admin_styles');