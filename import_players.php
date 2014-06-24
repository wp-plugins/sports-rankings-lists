<?php 
if (($handle = fopen($_POST['url'], "r")) !== FALSE) {
	$i = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	  	echo '<li><input type="hidden" name="wp_ranker_players[' . $i . '][id]" value="' . rand(1,999999) . '" /><input type="text" placeholder="Default Rank" name="wp_ranker_players[' . $i . '][default_rank]" value="' . $data[0] . '" size="25" /> <input type="text" placeholder="Name" name="wp_ranker_players[' . $i . '][name]" value="' . $data[1] . '" size="25" /> <input type="text" placeholder="Team" name="wp_ranker_players[' . $i . '][team]" value="' . $data[2] . '" size="25" />  <input type="text" placeholder="Position" name="wp_ranker_players[' . $i . '][position]" value="' . $data[3] . '" size="25" /> <span class="button remove">Remove</span> <span class="drag-icon"></span></li>';		
        $i++;
    }
    fclose($handle);
} ?>