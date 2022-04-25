<?php $index =0 ?>
<?php foreach ($taxonomies as $taxonomy): ?>

    <div class="radio-button-cont section-content-lens-category" >
        <div class="radio-text">
            <h5><?php echo $taxonomy->name ?></h5>
            <p><?php echo $taxonomy->category_description ?> </p>
        </div>

        <input <?php echo ($index ==0)?'checked="checked"':''; ?>class="next-step <?php echo $taxonomy->taxonomy ?>" type="radio" id="<?php echo $taxonomy->slug ?>"
               name="<?php echo $taxonomy->taxonomy ?>" value="<?php echo $taxonomy->term_id ?>">
        <label data-index="<?php echo  $taxonomy->term_id; ?>" for="<?php echo $taxonomy->slug ?>"></label>

    </div>
    <?php $index++; ?>
<?php endforeach; ?>