<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/18/2017
 * Time: 2:46 PM
 */

class KMFavourites_Product
{

    public static function get_favourites($user_id = false)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $favourites = get_user_meta($user_id, '_km_favourites_product', true);
        if (empty($favourites)) {
            $favourites = array();
        }
       // $favourites = self::check_favourites_products($favourites);
        return $favourites;
    }

    private static function check_favourites_products($favourites)
    {
        $flag = false;
        foreach ($favourites as $key => $id) {
            if (false === get_post_status($id)) {
                unset($favourites[$key]);
                $flag = true;
            }
        }
        if ($flag) {
            self::update_favourites($favourites);
        }
        return $favourites;
    }

    public static function update_favourites($favourites, $user_id = false)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        update_user_meta($user_id, '_km_favourites_product', $favourites);
    }

    public static function remove($user_id = false, $product_id)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        $favourites = get_user_meta($user_id, '_km_favourites_product', true);
        if (($key = array_search($product_id, $favourites)) !== false) {
            unset($favourites[$key]);
        }
        self::update_favourites($favourites, $user_id);
    }

}