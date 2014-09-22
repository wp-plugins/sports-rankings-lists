<?php 

add_action( 'wp_ajax_import_players_from_csv', 'import_players_from_csv_callback' );

function import_players_from_csv_callback() {

    $extention = pathinfo($_POST['url'], PATHINFO_EXTENSION);
    if ($extention != 'csv') {
        echo '<span style="width: 100%;background-color:#fcf3ef;font-weight: 400;padding: 6px 12px;">Please use a comma separated file for upload - instructions <a href="http://fantasyknuckleheads.com/how-to-create-csv-for-sports-list-rankings-plugin/" target="_blank">here</a></span>';
    } else if (($handle = fopen($_POST['url'], "r")) !== FALSE) {
        $i = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            echo '<li><input type="hidden" name="wp_ranker_players[' . $i . '][id]" value="' . rand(1,999999) . '" /><input type="text" placeholder="Default Rank" name="wp_ranker_players[' . $i . '][default_rank]" value="' . $data[0] . '" size="25" /> <input type="text" placeholder="Name" name="wp_ranker_players[' . $i . '][name]" value="' . $data[1] . '" size="25" /> <input type="text" placeholder="Team" name="wp_ranker_players[' . $i . '][team]" value="' . $data[2] . '" size="25" />  <input type="text" placeholder="Position" name="wp_ranker_players[' . $i . '][position]" value="' . $data[3] . '" size="25" /> <span class="button remove">Remove</span> <span class="drag-icon"></span></li>';      
            $i++;
        }
        fclose($handle);
    }

    die(); // this is required to terminate immediately and return a proper response
}