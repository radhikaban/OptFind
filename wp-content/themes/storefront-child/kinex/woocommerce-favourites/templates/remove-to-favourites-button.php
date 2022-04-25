<?php
global $product;
?>
<div class='simple_container'>
    <span class='simple_message'></span>
    <div id="fav_button_warp">
        <button class="simple-remove-from-favourites" data-productid='<?php echo $product->get_id() ?>'>Remove Favorites</button>
    </div>
</div>
