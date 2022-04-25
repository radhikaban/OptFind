<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/19/2017
 * Time: 3:59 PM
 */

if (!empty($vendor_id)) {
    $args = array(
        'meta_key' => '_km_favourites_vendros',
        'meta_value' => sprintf(':%s;', $vendor_id),
        'meta_compare' => 'LIKE'
    );


    $users = get_users($args);

    ?>

    <div class='simple_container'>

        <div id="fav_button_warp">
            <a href="javascript:void(0)">Favourites Shop (<?php echo count($users) ?>)</a>
        </div>
    </div>

    <?php

}

