<?php

/*

Copyright 2014 Dario Curvino (email : d.curvino@tiscali.it)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if (!defined('ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly


class YasrOnSavePost {

    private $post_id;

    public function __construct($post_id) {
        $this->post_id = (int)$post_id;

        $this->yasrSavePost();
    }

    private function yasrSavePost() {
        //if user can not publish posts
        if (!current_user_can(YASR_USER_CAPABILITY_EDIT_POST)) {
            return;
        }
        $this->saveOverallRating();
        $this->postIsReview();
        $this->saveItemType();
        $this->saveItemTypesFields();
        $this->saveMultisetEditor();

        if (YASR_AUTO_INSERT_ENABLED === 1) {
            $this->excludeAutoInsert();
        }
    }

    /********** Save Post actions **********/

    private function saveOverallRating() {
        //this mean there we're not in the classic editor
        if (!isset($_POST['yasr_nonce_overall_rating'])) {
            return;
        }

        $update_result = null;

        if (isset($_POST['yasr_overall_rating'])) {
            $rating = $_POST['yasr_overall_rating'];
            $nonce  = $_POST['yasr_nonce_overall_rating'];
        } else {
            return;
        }

        if($rating === '0' || $rating === 0) {
            return;
        }

        if (!wp_verify_nonce($nonce, 'yasr_nonce_overall_rating_action')) {
            return;
        }

        $rating = (float) $rating;

        if ($rating > 5) {
            $rating = 5;
        }

        //Put an action to hook into
        do_action('yasr_action_on_overall_rating', $this->post_id, $rating);

        $update_result = update_post_meta($this->post_id, 'yasr_overall_rating', $rating);

        //if update_post_meta returns an integer means this is a new post
        //so we're going to insert the default YASR_ITEMTYPE
        if (is_int($update_result)) {
            add_post_meta($this->post_id, 'yasr_review_type', YASR_ITEMTYPE);
        }

    }

    private function postIsReview() {

        //this mean there we're not in the classic editor
        if(!isset($_POST['yasr_nonce_is_post_review'])) {
            return;
        }

        $nonce = $_POST['yasr_nonce_is_post_review'];

        if (!wp_verify_nonce($nonce, 'yasr_nonce_is_post_review_action')) {
            return;
        }

        if (isset($_POST['yasr_is_post_review'])) {
            update_post_meta($this->post_id, 'yasr_post_is_review', 'yes');
        }
        else {
            delete_post_meta($this->post_id, 'yasr_post_is_review');
        }
    }

    private function saveItemType() {

        //this mean there we're not in the classic editor
        if(!isset($_POST['yasr_nonce_review_type'])) {
            return;
        }

        $nonce = $_POST['yasr_nonce_review_type'];

        //check nonce
        if (!wp_verify_nonce($nonce, 'yasr_nonce_review_type_action')) {
            return;
        }

        //check if $_POST isset
        if (isset($_POST['yasr-review-type'])) {
            $snippet_type = $_POST['yasr-review-type'];

            //check if $snippet_type is a supported itemType
            if (yasr_is_supported_schema($snippet_type)===true) {
                //if the selected item type, is the same of the default one, delete the saved postmeta
                if ($snippet_type === YASR_ITEMTYPE) {
                    delete_post_meta($this->post_id, 'yasr_review_type');
                } else {
                    update_post_meta($this->post_id, 'yasr_review_type', $snippet_type);
                }
            } else {
                return;
            }
        } else {
            return;
        }

    }

    private function saveItemTypesFields() {
        $array_item_type_info = json_decode(YASR_SUPPORTED_SCHEMA_TYPES_ADDITIONAL_FIELDS, true);
        $array_to_save = array();

        foreach ($array_item_type_info as $item_type_name) {

            $nonce_action = $item_type_name . '_nonce_action';
            $nonce_name   = $item_type_name . '_nonce_name';

            //verifing nonces
            if(isset($_POST[$nonce_name])) {
                $nonce = $_POST[$nonce_name];
                if(!wp_verify_nonce($nonce, $nonce_action)) {
                    return;
                }
            }

            //get value, sanitize it and save
            if(isset($_POST[$item_type_name])) {

                //if come from textarea, use sanitize_textarea_field, that preservers newlines
                if($item_type_name === 'yasr_recipe_recipeingredient'
                   || $item_type_name === 'yasr_recipe_recipeinstructions'
                   || 'yasr_movie_actor' || 'yasr_movie_director') {
                      $item_to_save =  sanitize_textarea_field($_POST[$item_type_name]);
                } else {
                    //use sanitize_text_field
                    $item_to_save  = sanitize_text_field($_POST[$item_type_name]);
                }

                $array_to_save[$item_type_name] = $item_to_save;
            }

        }

        update_post_meta($this->post_id, 'yasr_schema_additional_fields', $array_to_save);

    }

    private function saveMultisetEditor() {

        if (isset($_POST['yasr_multiset_author_votes']) && isset($_POST['yasr_multiset_id'])) {
            $field_and_vote_array = json_decode(sanitize_text_field(stripslashes($_POST['yasr_multiset_author_votes'])));
            $set_id   = (int) $_POST['yasr_multiset_id'];
            $nonce    = $_POST['yasr_nonce_save_multi_values'];

            if (!is_int($set_id) || $field_and_vote_array == '') {
                return;
            }

        } else {
            return;
        }

        if (!wp_verify_nonce($nonce, 'yasr_nonce_save_multi_values_action')) {
            die('Security check');
        }

        $i = 0;

        $data_to_save[$i] = array(
            'set_id' => $set_id,
            'fields_and_ratings' => $field_and_vote_array
        );

        $i++;

        $set_post_meta_values = get_post_meta($this->post_id, 'yasr_multiset_author_votes',true);

        //If data for this post already exists
        if ($set_post_meta_values) {
            //first, loop saved fields and ratings
            foreach ($set_post_meta_values as $saved_set_id) {
                //if the saved set is different from the one that we're trying to save,
                //append data to save to the post meta
                if ($saved_set_id['set_id'] !== $set_id) {
                    $data_to_save[$i]['set_id'] = $saved_set_id['set_id'];
                    $data_to_save[$i]['fields_and_ratings'] = $saved_set_id['fields_and_ratings'];

                    $i++;
                    //Append data to save to the post meta

                } //if the set is not stored
            }
        }

        // Write new data
        update_post_meta($this->post_id, 'yasr_multiset_author_votes', $data_to_save);

    } //End callback function

    private function excludeAutoInsert() {

        //this mean there we're not in the classic editor
        if (!isset($_POST['yasr_nonce_auto_insert'])) {
            return;
        } else {
            $nonce = $_POST['yasr_nonce_auto_insert'];
        }

        if (!wp_verify_nonce($nonce, 'yasr_nonce_auto_insert_action')) {
            return;
        }

        if (isset($_POST['yasr_auto_insert_disabled'])) {
            update_post_meta($this->post_id, 'yasr_auto_insert_disabled', 'yes');
        } else {
            delete_post_meta($this->post_id, 'yasr_auto_insert_disabled');
        }

    }

}

add_action('save_post', 'yasr_on_save_post_callback');

function yasr_on_save_post_callback ($post_id) {
    new YasrOnSavePost($post_id);
}
