<?php global $WCMp; ?>
<?php wc_print_notices(); ?>

<?php
$state = WC()->countries->get_states('CA');
$extra_fields = array(
    'vendor_first_name' => array(
        'label' => 'First Name',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'vendor_last_name' => array(
        'label' => 'Last Name',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'email' => array(
        'label' => 'Email Address',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'

    ),
    'vendor_page_title' => array(
        'label' => 'Company',
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'vendor_account_number' => array(
        'label' => 'Bank Account Number',
        'placeholder' => 'Bank Account Number',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'vendor_business_number' => array(
        'label' => 'Business Tax Number',
        'placeholder' => 'Business Tax Number',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'vendor_website' => array(
        'label' => 'Website Url',

        'placeholder' => 'eg: www.example.com',
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'vendor_phone' => array(
        'label' => 'Telephone',
        'placeholder' => '(123) 456-7890',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'vendor_address_1' => array(
        'label' => 'Street Address 1',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-12'
    ),
    'vendor_address_2' => array(
        'label' => 'Street Address 2',
        'type' => 'text',
        'class' => 'wcmp-regi-12'
    ),
    'vendor_state' => array(
        'label' => 'State/Province',
        'required' => true,
        'type' => 'select',
        'class' => 'wcmp-regi-6',
        'value' => $state
    ),
    'vendor_city' => array(
        'label' => 'City',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    ),
    'vendor_postcode' => array(
        'label' => 'Postal Code',
        'required' => true,
        'type' => 'text',
        'class' => 'wcmp-regi-6'
    )

);


?>
<div class="wcmp_regi_main">
    <form class="register 123" role="form" method="post" id="km_register_form" enctype="multipart/form-data">
        <?php if (!is_user_logged_in()) : ?>
            <h2 class="reg_header1"><?php echo apply_filters('wcmp_vendor_registration_header_text', __('Vendor Registration Form', 'woocommerce')); ?></h2>

            <div class="wcmp_regi_form_box">
                <?php
                $wcmp_vendor_general_settings_name = get_option('wcmp_vendor_general_settings_name'); ?>
                <h3 class="reg_header2"><?php echo apply_filters('woocommerce_section_label', __('Account Details', 'dc-woocommerce-multi-vendor')); ?></h3>


                <?php foreach ($extra_fields as $key => $extra_field): ?>
                    <?php $is_require = false; ?>
                    <div class="<?php echo $extra_field['class'] ?>">
                        <label for="<?php echo $key ?>"><?php _e($extra_field['label'], 'woocommerce'); ?>
                            <?php if (isset($extra_field['required']) && $extra_field['required']): ?>
                                <?php $is_require = true; ?>
                                <span class="required">*</span>
                            <?php endif ?>
                        </label>
                        <?php if (isset($extra_field['type']) && $extra_field['type'] == 'select'): ?>
                            <select name="userdata[<?php echo $key ?>]" <?php echo ($is_require) ? 'required="required"' : '' ?> >
                                <?php foreach ($extra_field['value'] as $k => $v): ?>
                                    <option
                                        <?php echo (isset($_REQUEST['userdata'][$key]) && $_REQUEST['userdata'][$key] == $k) ? 'selected' : '' ?>
                                        value="<?php echo $k ?>"><?php echo $v ?></option>
                                <?php endforeach; ?>

                            </select>
                        <?php else: ?>
                            <?php if ($key == 'email'): ?>
                                <input placeholder="<?php echo(isset($extra_field['placeholder']) ? $extra_field['placeholder'] : '') ?>"
                                       type="<?php echo $extra_field['type'] ?>" <?php echo ($is_require) ? 'required="required"' : '' ?>
                                       name="<?php echo $key ?>" id="<?php echo $key ?>"
                                       value="<?php echo (isset($_REQUEST[$key])) ? $_REQUEST[$key] : '' ?>"/>
                                <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="" />
                                <input type="hidden" value="<?php if (!empty($_POST['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($_POST['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]"  />
                                <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo (isset($_REQUEST['wcmp_vendor_fields'][$key])) ? $_REQUEST['wcmp_vendor_fields'][$key] : '' ?>" />



                            <?php else: ?>
                                <input placeholder="<?php echo(isset($extra_field['placeholder']) ? $extra_field['placeholder'] : '') ?>"
                                       id="<?php echo $key ?>"
                                       type="<?php echo $extra_field['type'] ?>" <?php echo ($is_require) ? 'required="required"' : '' ?>
                                       name="userdata[<?php echo $key ?>]" id="<?php echo $key ?>"
                                       value="<?php echo (isset($_REQUEST['userdata'][$key])) ? $_REQUEST['userdata'][$key] : '' ?>"/>
                                <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="" />
                                <input type="hidden" value="<?php if (!empty($_POST['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($_POST['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]"  />
                                <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo (isset($_REQUEST['wcmp_vendor_fields'][$key])) ? $_REQUEST['wcmp_vendor_fields'][$key] : '' ?>" />
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>

                <?php endforeach; ?>
                <div class="wcmp-regi-12">
                    <label for="password">Password <span class="required">*</span>
                    </label>
                    <input placeholder="" type="password" required="required" name="password"
                           value="<?php echo (isset($_REQUEST['password'])) ? $_REQUEST['password'] : '' ?>"
                           id="password" value="">
                </div>
                <div class="wcmp-regi-12">
                    <label for="confirm_password">Confirm Password <span class="required">*</span>
                    </label>
                    <input placeholder="" type="password" required="required" name="confirm_password"
                           value="<?php echo (isset($_REQUEST['confirm_password'])) ? $_REQUEST['confirm_password'] : '' ?>"
                           id="confirm_password" value="">
                </div>

                <div style="<?php echo((is_rtl()) ? 'right' : 'left'); ?>: -999em; position: absolute;"><label
                        for="trap"><?php _e('Anti-spam', 'woocommerce'); ?></label><input type="text" name="email_2"
                                                                                          id="trap" tabindex="-1"/>
                </div>
                <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>

                <?php do_action('wcmp_vendor_register_form'); ?>
                <div class="clearboth"></div>
            </div>
        <?php else : ?>
            <div class="wcmp_regi_form_box">

                <?php do_action('wcmp_vendor_register_form'); ?>
                <div class="clearboth"></div>
            </div>
        <?php endif; ?>
        <?php //do_action('register_form'); ?>
        <?php if (is_user_logged_in()) {
            echo '<input type="hidden" name="vendor_apply" />';
        } ?>
        <input type="hidden" value="true" name="pending_vendor"/>


        <?php do_action('woocommerce_register_form'); ?>
        <p class="woocomerce-FormRow form-row">
            <?php
            $button_text = apply_filters('wcmp_vendor_registration_submit', 'Register');
            ?>
            <input type="submit" class="woocommerce-Button button custom-btn" name="register"
                   value="<?php esc_attr_e($button_text, 'woocommerce'); ?>"/>
        </p>
        <?php do_action('woocommerce_register_form_end'); ?>
    </form>
</div>

<script>

    jQuery(document).ready(function ($) {
        // validate the comment form when it is submitted

        $("#vendor_phone").mask("(999) 999-9999");

        $.validator.addMethod("cus_url", function (value, element) {


            if (value.substr(0, 7) != 'http://') {
                value = 'http://' + value;
            }
            if (value.substr(value.length - 1, 1) != '/') {
                value = value + '/';
            }
            return this.optional(element) || /(?:(?:http|https):\/\/)?([-a-zA-Z0-9.]{2,256}\.[a-z]{2,4})\b(?:\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi.test(value);
        }, "Please enter valid url.");

        // validate signup form on keyup and submit
        $("#km_register_form").validate({
            rules: {
                'userdata[vendor_first_name]': "required",
                'userdata[vendor_last_name]': "required",
                'email': {
                    required: true,
                    email: true
                },
                'password': {
                    required: true,
                    minlength: 6
                },
                'confirm_password': {
                    required: true,
                    minlength: 6,
                    equalTo: '#password'
                },
                'userdata[vendor_website]': {
                    cus_url: true
                },
                'userdata[vendor_phone]': "required",
                'userdata[vendor_address_1]': "required",
                'userdata[vendor_state]': "required",
                'userdata[vendor_city]': "required",
                'userdata[vendor_postcode]': "required",

            },
            messages: {
                'userdata[vendor_first_name]': "Please enter your firt name",
                'userdata[vendor_last_name]': "Please enter your last name",
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                },
                confirm_password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long",
                    equalTo: "Please enter the same password as above"
                },
                email: "Please enter a valid email address",
                'userdata[vendor_phone]': "Please enter your telephone",
                'userdata[vendor_address_1]': "Please enter a address",
                'userdata[vendor_state]': "Please enter your province",
                'userdata[vendor_city]': "Please enter you city",
                'userdata[vendor_postcode]': "Please enter your are postal code",

            }
        });

        $(".custom-btn").click(function(){

            var  first_name =$('input[name="userdata[vendor_first_name]').val();
            var  last_name =$('input[name="userdata[vendor_last_name]').val();
            var  url =$('input[name="userdata[vendor_website]').val();
            var  phone =$('input[name="userdata[vendor_phone]').val();
            var  email =$('input[name="email').val();

            if(first_name) {
                $('input[name="wcmp_vendor_fields[vendor_first_name][type]').val('text');
                $('input[name="wcmp_vendor_fields[vendor_first_name][value]').val(first_name);
                $('input[name="wcmp_vendor_fields[vendor_first_name][label]').val('First Name');
            }
            if(last_name) {
                $('input[name="wcmp_vendor_fields[vendor_last_name][type]"]').val('text');
                $('input[name="wcmp_vendor_fields[vendor_last_name][value]').val(last_name);
                $('input[name="wcmp_vendor_fields[vendor_last_name][label]').val('Last Name');
            }
            if(phone) {
                $('input[name="wcmp_vendor_fields[vendor_phone][type]"]').val('text');
                $('input[name="wcmp_vendor_fields[vendor_phone][value]').val(phone);
                $('input[name="wcmp_vendor_fields[vendor_phone][label]').val('Phone');
            }
            if(email) {
                $('input[name="wcmp_vendor_fields[email][type]"]').val('email');
                $('input[name="wcmp_vendor_fields[email][value]').val(email);
                $('input[name="wcmp_vendor_fields[email][label]').val('Email Address');
            }
            if(url) {
                $('input[name="wcmp_vendor_fields[vendor_website][type]"]').val('url');
                $('input[name="wcmp_vendor_fields[vendor_website][value]').val(url);
                $('input[name="wcmp_vendor_fields[vendor_website][label]').val('Website');
            }
        });



    });
</script>