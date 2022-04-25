<?php
/**
 * Created by PhpStorm.
 * User: Sarab
 * Date: 13-Dec-17
 * Time: 5:02 PM
 */

class KMCL_AddToCartButton
{


    public static  function kmcl_add_custom_add_to_cart_button_into_single()
    {

        global $product;
       $show_hide_option = get_post_meta($product->get_id(), 'kmcl_addon_lenses', true);
       $show_hide_option2 = get_post_meta($product->get_id(), 'kmcl_add_prescription_lenses', true);
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        $idarray = array();		
        foreach ($terms as  $keyvalue) { 		
            $idarray[] = $keyvalue->term_id;		
        }		
        if ($show_hide_option == 'yes' || $show_hide_option2 == 'yes') {						
            if (in_array("67", $idarray)){}			
                elseif (in_array("218", $idarray)){			
                }
                elseif (in_array("219", $idarray)){	}			
                elseif (in_array("105", $idarray)){	}
                else{				
                        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                    }
            //			
                            add_action('woocommerce_single_product_summary', array('KMCL_AddToCartButton','kmcl_template_single_add_to_cart'), 31);

                            add_action( 'kmcl_single_variation', 'kmcl_single_variation', 10 );

                            add_action( 'kmcl_after_add_to_cart_button', 'kmcl_single_variation_add_to_cart_button', 5 );




                            add_action( 'kmcl_simple_add_to_cart', 'kmcl_simple_add_to_cart', 30 );
                            add_action( 'kmcl_grouped_add_to_cart', 'kmcl_grouped_add_to_cart', 30 );
                            add_action( 'kmcl_variable_add_to_cart', 'kmcl_variable_add_to_cart', 30 );
                            add_action( 'kmcl_external_add_to_cart', 'kmcl_external_add_to_cart', 30 );
                        }
                    }





                    public static function kmcl_template_single_add_to_cart(){
                        global $product;
                        add_filter('woocommerce_product_single_add_to_cart_text', array('KMCL_AddToCartButton','kmsp_product_single_add_to_cart_text'));
                        do_action( 'kmcl_' . $product->get_type() . '_add_to_cart' );
                    }

                    public static function  kmsp_product_single_add_to_cart_text($return){
                        //return __('Add lenses','kmtheme');
                        return __('Next','kmtheme');
                    }
                }