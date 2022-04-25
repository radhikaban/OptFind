<?php
/**
 * The template for displaying vendor orders item band called from vendor_orders.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-transaction/vendor-transaction-items.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
if(!empty($transactions)) { 
	foreach($transactions as $transaction_id) {
		$order_ids = $commssion_ids = '';
		$commission_details = get_post_meta($transaction_id, 'commission_detail', true);
		$transfer_charge = get_post_meta($transaction_id, 'transfer_charge', true);
		$transaction_amt = get_post_meta($transaction_id, 'amount', true) - get_post_meta($transaction_id, 'transfer_charge', true) - get_post_meta($transaction_id, 'gateway_charge', true);	
		?>
		<tr>
			<td align="center"  width="20" >
				<span class="input-group-addon beautiful">
					<input name="transaction_ids[]" value="<?php echo $transaction_id; ?>"  class="select_transaction" type="checkbox" >
				</span>
			</td>
			<td align="center" ><?php echo get_the_date('d/m', $transaction_id); ?></td>
                        <td align="center" ><?php echo isset($transfer_charge) ? wc_price($transfer_charge) : wc_price(0); ?></td>
                        <td align="center" valign="middle" ><?php echo wc_price($transaction_amt); ?></td>
    </tr>
		<?php 
	} 
}	
?>