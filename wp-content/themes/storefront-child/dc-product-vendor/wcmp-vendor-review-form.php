<?php
/**
 * The template for displaying Seller Review form
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/wcmp-vendor-review-form.php
 *
 * @author        WC Marketplace
 * @package    WCMp/Templates
 * @version   2.3.5
 */
global $WCMp, $wpdb;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (isset($queried_object->term_id) && !empty($queried_object)) {
    $vendor = get_wcmp_vendor_by_term($queried_object->term_id);
    $shop_name = $vendor->user_data->data->display_name;
    $vendor_id = $vendor->id;
    $count = $vendor->get_review_count();
    $is_enable = wcmp_seller_review_enable($queried_object->term_id);
    $current_user = wp_get_current_user();
    $reviews_lists = $vendor->get_reviews_and_rating(0);
}


?>
<div class="wocommerce">
    <div id="reviews">

        <div class="reviews-wrap-header">
            <div class="col-full">
                <h2>Reviews</h2>
                <?php
                $count = 0;
                if (get_wcmp_vendor_settings('is_sellerreview', 'general') == 'Enable') {
                    $queried_object = get_queried_object();
                    if (isset($queried_object->term_id) && !empty($queried_object)) {
                        $rating_val_array = wcmp_get_vendor_review_info($queried_object->term_id);
                        $rating = round($rating_val_array['avg_rating']);
                        $count = intval($rating_val_array['total_rating']);
                    }
                }

                ?>

                <span><?php //km_get_review_title($rating) ?>


                    <?php if ($count > 0) { ?>
                        <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf(__('Rated %s out of 5', 'dc-woocommerce-multi-vendor'), $rating) ?>">
                    <span style="width:<?php echo ( round($rating_val_array['avg_rating']) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e('out of 5', 'dc-woocommerce-multi-vendor'); ?></span>
                        </span>
                        (<?php echo $count; ?>)<?php } ?>
                </span>
            </div>
        </div>

        <div class="col-full">
            <div id="wcmp_vendor_reviews">
                <?php if (isset($is_enable) && $is_enable) { ?>
                    <div id="review_form_wrapper">
                        <div id="review_form">
                            <div id="respond" class="comment-respond">
                                <?php if ($vendor->id != get_current_user_id()) : ?>
                                    <h3 id="reply-title" class="comment-reply-title"><?php
                                        if ($count == 0) {
                                            echo __(sprintf('Be the first to review “%s”', $shop_name), 'dc-woocommerce-multi-vendor');
                                        } else {
                                            echo __(sprintf('Add a review to “%s”', $shop_name), 'dc-woocommerce-multi-vendor');
                                        }
                                        ?> </h3>
                                    <form action="" method="post" id="commentform" class="comment-form" novalidate="">
                                        <p id="wcmp_seller_review_rating"></p>

                                        <p class="comment-form-rating"><label
                                                for="rating"><?php echo __('Your Rating', 'dc-woocommerce-multi-vendor'); ?></label>
                                            <select name="rating" id="rating">
                                                <option
                                                    value=""><?php echo __('Rate...', 'dc-woocommerce-multi-vendor'); ?></option>
                                                <option
                                                    value="5"><?php echo __('Perfect', 'dc-woocommerce-multi-vendor'); ?></option>
                                                <option
                                                    value="4"><?php echo __('Good', 'dc-woocommerce-multi-vendor'); ?></option>
                                                <option
                                                    value="3"><?php echo __('Average', 'dc-woocommerce-multi-vendor'); ?></option>
                                                <option
                                                    value="2"><?php echo __('Not that bad', 'dc-woocommerce-multi-vendor'); ?></option>
                                                <option
                                                    value="1"><?php echo __('Very Poor', 'dc-woocommerce-multi-vendor'); ?></option>
                                            </select></p>
                                        <p class="comment-form-comment">
                                            <label
                                                for="comment"><?php echo __('Your Review', 'dc-woocommerce-multi-vendor'); ?> </label>
                                            <textarea id="comment" name="comment" cols="45" rows="8"
                                                      aria-required="true"></textarea>
                                        </p>

                                        <p class="form-submit">
                                            <input id="wcmp_vendor_for_rating" name="wcmp_vendor_for_rating"
                                                   type="hidden" value="<?php echo $vendor_id; ?>">
                                            <input id="author" name="author" type="hidden"
                                                   value="<?php echo $current_user->display_name; ?>" size="30"
                                                   aria-required="true">
                                            <input id="email" name="email" type="hidden"
                                                   value="<?php echo $current_user->user_email; ?>" size="30"
                                                   aria-required="true">
                                            <input name="submit" type="button" id="submit" class="submit"
                                                   value="Submit">

                                        </p>
                                    </form>
                                <?php endif; ?>
                            </div><!-- #respond -->
                        </div>
                    </div>
                <?php } else{?>
                    <div id="review_form_wrapper">
                        <div id="review_form">
                            <h3><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ) ?>">Login or Register</a> to submit your reviews.</h3>
                        </div>
                    </div>
                <?php } ?>
                <div id="comments">
                    <?php
                    if ($count > 0) {
                        $start = 0;
                        $posts_per_page = get_option('posts_per_page');
                        $total_pages = ceil($count / $posts_per_page);
                        ?>

                        <form id="vendor_review_rating_pagi_form">
                            <input type="hidden" name="pageno" id="wcmp_review_rating_pageno" value="1">
                            <input type="hidden" name="postperpage" id="wcmp_review_rating_postperpage"
                                   value="<?php echo $posts_per_page; ?>">
                            <input type="hidden" name="totalpage" id="wcmp_review_rating_totalpage"
                                   value="<?php echo $total_pages; ?>">
                            <input type="hidden" name="totalreview" id="wcmp_review_rating_totalreview"
                                   value="<?php echo $count; ?>">
                            <input type="hidden" name="term_id" id="wcmp_review_rating_term_id"
                                   value="<?php echo $queried_object->term_id; ?>">
                        </form>
                        <?php
                        if (isset($reviews_lists) && count($reviews_lists) > 0) {
                            echo '<ol class="commentlist vendor_comment_list">';
                            $WCMp->template->get_template('review/wcmp-vendor-review.php', array('reviews_lists' => $reviews_lists, 'vendor_term_id' => $queried_object->term_id));
                            echo '</ol>';
                            if ($total_pages > 1) {
                                echo '<div class="wcmp_review_loader"><img src="' . $WCMp->plugin_url . 'assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>';
                                echo '<div class="load_more_wrap"><input name="loadmore" type="button" id="wcmp_review_load_more" class="submit wcmp_load_more btn btn-default" style="" value="Load More"></div>';
                            }
                        }
                    } elseif ($count == 0) {
                        ?>
                        <p class="woocommerce-noreviews"><?php echo __('There are no reviews yet.', 'dc-woocommerce-multi-vendor'); ?> </p>
                    <?php } ?>
                </div>
                <div class="clear"></div>

            </div>
        </div>
    </div>
</div>
