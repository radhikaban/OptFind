<div id='simple_favourites_display'>
    <div class="shop">
        <?php

        ?>

        <?php k_shop_pagination($favourites,12); ?>
    </div>

</div>
<?php

function k_shop_pagination($display_array, $show_per_page)
{
    $page = 1;


    if (isset($_GET['km-page']) && filter_var($_GET['km-page'], FILTER_VALIDATE_INT) == true) {
        $page = $_GET['km-page'];
    }


    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $show_per_page;

    $outArray = array_slice($display_array, $start, $show_per_page);

//pagination links
    $total_elements = count($display_array);
    $pages = ((int)$total_elements / (int)$show_per_page);
    $pages = ceil($pages);
    if ($pages >= 1 && $page <= $pages) {

        echo k_shop_get_design($outArray);
        echo '<div class="pwb-all-brands"><div class="pwb-pagination-wrapper">';
        echo k_displayPaginationBelow($show_per_page, $page, $total_elements);
        echo '</div></div>';

    } else {
        echo __('No Shops Found', 'perfect-woocommerce-brands');
    }
}

function k_shop_get_design($favourites)
{
    ob_start();
    ?>
    <?php foreach ($favourites as $favourite): ?>
    <?php $vendor_id = get_user_meta($favourite, '_vendor_term_id', true); ?>
    <?php $vendor = get_wcmp_vendor_by_term($vendor_id); ?>
    <div class="fav-shop-block">
        <a href="<?php echo  $vendor->permalink ?>">
        <?php $image = $vendor->image ? $vendor->image : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png'; ?>
        <img src="<?php echo $image ?>">
        <?php
        $address = '';

        if ($vendor->city) {
            $address = $vendor->city . ', ';
        }
        if ($vendor->state) {
            $address .= $vendor->state . ', ';
        }
        if ($vendor->country) {
            $address .= $vendor->country;
        }
        $vendor_hide_address = get_user_meta($favourite, '_vendor_hide_address', true);
        $vendor_hide_phone = get_user_meta($favourite, '_vendor_hide_phone', true);
        $vendor_hide_email = get_user_meta($favourite, '_vendor_hide_email', true);
        $tagline = get_user_meta($favourite, '_vendor_tagline', true);

        $vendorshop_owner = get_user_meta($favourite, '_vendor_page_title', true);
       
        ?>

        <h2> <?php echo $vendorshop_owner ?></h2>

</a>
    </div>
<?php endforeach; ?>
<div class="clear"></div>
    <?php
    return ob_get_clean();
}

function k_displayPaginationBelow($per_page, $page, $total)
{

    $adjacents = "2";

    $page = ($page == 0 ? 1 : $page);
    $start = ($page - 1) * $per_page;
    $page_url = '';
    $prev = $page - 1;
    $next = $page + 1;
    $setLastpage = ceil($total / $per_page);
    $lpm1 = $setLastpage - 1;
    $setPaginate = "";


    if ($setLastpage > 1) {
        $setPaginate .= "<ul class='setPaginate'>";
        //$setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
        if ($setLastpage < 7 + ($adjacents * 2)) {
            for ($counter = 1; $counter <= $setLastpage; $counter++) {
                if ($counter == $page) {
                    $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                } else {
                    $page_url = add_query_arg('km-page', $counter);
                    $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                }

            }
        } elseif ($setLastpage > 5 + ($adjacents * 2)) {
            if ($page < 1 + ($adjacents * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page) {
                        $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                    } else {
                        $page_url = add_query_arg('km-page', $counter);
                        $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                    }
                }
                $setPaginate .= "<li class='dot'>...</li>";
                $page_url = add_query_arg('km-page', $lpm1);
                $setPaginate .= "<li><a href='{$page_url}'>$lpm1</a></li>";
                $page_url = add_query_arg('km-page', $setLastpage);
                $setPaginate .= "<li><a href='{$page_url}'>$setLastpage</a></li>";
            } elseif ($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $page_url = add_query_arg('km-page', 1);
                $setPaginate .= "<li><a href='{$page_url}'>1</a></li>";
                $page_url = add_query_arg('km-page', 2);
                $setPaginate .= "<li><a href='{$page_url}'>2</a></li>";
                $setPaginate .= "<li class='dot'>...</li>";
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page) {
                        $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                    } else {
                        $page_url = add_query_arg('km-page', $counter);
                        $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                    }
                }
                $setPaginate .= "<li class='dot'>..</li>";
                $page_url = add_query_arg('km-page', $lpm1);
                $setPaginate .= "<li><a href='{$page_url}'>$lpm1</a></li>";
                $page_url = add_query_arg('km-page', $setLastpage);
                $setPaginate .= "<li><a href='{$page_url}'>$setLastpage</a></li>";
            } else {
                $page_url = add_query_arg('km-page', 1);
                $setPaginate .= "<li><a href='{$page_url}'>1</a></li>";
                $page_url = add_query_arg('km-page', 2);
                $setPaginate .= "<li><a href='{$page_url}'>2</a></li>";
                $setPaginate .= "<li class='dot'>..</li>";
                for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++) {
                    if ($counter == $page) {
                        $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                    } else {
                        $page_url = add_query_arg('km-page', $counter);
                        $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                    }
                }
            }
        }


        if ($page < $counter - 1) {
            $page_url = add_query_arg('km-page', $next);
            $setPaginate .= "<li><a href='{$page_url}'>Next</a></li>";

        } else {
            $setPaginate .= "<li><a class='current_page'>Next</a></li>";

        }

        $setPaginate .= "</ul>\n";
    }


    return $setPaginate;
}