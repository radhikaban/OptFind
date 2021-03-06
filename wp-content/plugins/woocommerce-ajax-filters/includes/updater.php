<?php
if ( ! class_exists( 'BeRocket_updater' ) ) {
    define( "BeRocket_update_path", 'http://berocket.com/' );
    define( "BeRocket_updater_log", TRUE );
    class BeRocket_updater {
        public static $plugin_info = array();
        public static $slugs = array();
        public static $key = '';
        public static $error_log = array();
        public static $debug_mode = false;
        public static function run () {
            $options = self::get_options();
            self::$debug_mode = ! empty($options['debug_mode']);
            self::$key = ( empty($options['account_key']) ? '' : $options['account_key'] );
            add_action( 'admin_head', array( __CLASS__, 'scripts') );
            add_action( 'admin_menu', array( __CLASS__, 'account_page'), 1 );
            add_action( 'network_admin_menu', array( __CLASS__, 'network_account_page'));
            add_action( 'admin_init', array( __CLASS__, 'account_option_register') );
            add_filter( 'pre_set_site_transient_update_plugins', array( __CLASS__, 'update_check_set') );
            add_action( 'install_plugins_pre_plugin-information', array( __CLASS__, 'plugin_info'), 1);
            add_action( "wp_ajax_br_test_key", array ( __CLASS__, 'test_key' ) );
            add_filter( 'http_request_host_is_external', array ( __CLASS__, 'allow_berocket_host' ), 10, 3 );
            if ( BeRocket_updater_log ) {
                add_action('admin_footer', array( __CLASS__, 'error_log'));
                add_action('wp_footer', array( __CLASS__, 'error_log'));
            }
            $plugin = array();
            $plugin = apply_filters( 'BeRocket_updater_add_plugin', $plugin );
            if( ! isset( $options['plugin_key'] ) || ! is_array( $options['plugin_key'] ) ) {
                $options['plugin_key'] =  array();
            }
            $update = false;
            foreach ( $plugin as $plug_id => $plug ) {
                self::$slugs[$plug['id']] = $plug['slug'];
                if( isset( $options['plugin_key'][$plug['id']] ) && $options['plugin_key'][$plug['id']] != '' ) {
                    $plugin[$plug_id]['key'] = $options['plugin_key'][$plug['id']];
                } elseif( isset( $plugin[$plug_id]['key'] ) && $plugin[$plug_id]['key'] != '' ) {
                    $options['plugin_key'][$plug['id']] = $plugin[$plug_id]['key'];
                    $update = true;
                }
            }
            self::$plugin_info = $plugin;
            if( $update ) {
                self::set_options($options);
            }
            add_filter('berocket_display_additional_notices', array(__CLASS__, 'berocket_display_additional_notices'));
        }
        public static function error_log () {
            if( self::$debug_mode ) {
                self::$error_log = apply_filters( 'BeRocket_updater_error_log', self::$error_log );
                ?>
                <script>
                    console.log(<?php echo json_encode( self::$error_log ); ?>);
                </script>
                <?php
            }
        }
        public static function add_to_debug($data, $plugin, $keys = false) {
            if( self::$debug_mode ) {
                if( $keys === false ) {
                    self::$error_log[$plugin][] = $data;
                } elseif( is_array($keys) ) {
                    if( count($keys) > 0 ) {
                        $data_set = self::$error_log[$plugin];
                        $last_key = array_pop($keys);
                        foreach($keys as $key) {
                            if( ! is_array($data_set[$key]) ) {
                                $data_set[$key] = array();
                            }
                            $new_set = &$data_set[$key];
                            unset($data_set);
                            $data_set = &$new_set;
                            unset($new_set);
                        }
                        if( empty($last_key) ) {
                            $data_set[] = $data;
                        } else {
                            $data_set[$last_key] = $data;
                        }
                    } else {
                        self::$error_log[$plugin][] = $data;
                    }
                } else {
                    self::$error_log[$plugin][$keys] = $data;
                }
            }
        }
        public static function berocket_display_additional_notices($notices) {
            if( ! empty($_GET['page']) && $_GET['page'] == 'berocket_account' ) {
                return $notices;
            }
            if( is_network_admin() ) {
                $not_activated_notices = get_site_transient('berocket_not_activated_notices_site');
            } else {
                $not_activated_notices = get_transient('berocket_not_activated_notices');
            }
            if( $not_activated_notices == false ) {
                $active_plugin = get_option('berocket_key_activated_plugins');
                $active_site_plugin = get_site_option('berocket_key_activated_plugins');
                if( ! is_array($active_plugin) ) {
                    $active_plugin = array();
                }
                if( ! is_array($active_site_plugin) ) {
                    $active_site_plugin = array();
                }
                $not_activated_notices = array();
                foreach ( self::$plugin_info as $plugin ) {
                    if( empty($active_plugin[$plugin['id']]) && empty($active_site_plugin[$plugin['id']]) ) {
                        if( version_compare($plugin['version'], '2.0', '>=') ) {
                            $not_activated_notices[] = array(
                                'start' => 0,
                                'end'   => 0,
                                'name'  => $plugin['name'],
                                'html'  => '<strong>Please 
                                    <a class="berocket_button" href="' . (is_network_admin() ? admin_url('network/admin.php?page=berocket_account') : admin_url('admin.php?page=berocket_account')) . '">activate plugin</a> ' . $plugin['name'] . ' with help of Plugin/Account Key from 
                                    <a class="berocket_button" href="' . BeRocket_update_path . 'user" target="_blank">BeRocket account</a></strong>.
                                    You can activate plugin in 
                                    <a class="berocket_button" href="' . (is_network_admin() ? admin_url('network/admin.php?page=berocket_account') : admin_url('admin.php?page=berocket_account')) . '">BeRocket Account settings</a>
                                    ',
                                'righthtml'  => '',
                                'rightwidth'  => 0,
                                'nothankswidth'  => 0,
                                'contentwidth'  => 1600,
                                'subscribe'  => false,
                                'priority'  => 10,
                                'height'  => 50,
                                'repeat'  => false,
                                'repeatcount'  => 1,
                                'image' => array(
                                    'local' => '',
                                    'width' => 0,
                                    'height' => 0,
                                    'scale' => 1,
                                )
                            );
                        }
                    }
                }
                if( is_network_admin() ) {
                    set_site_transient('berocket_not_activated_notices_site', $not_activated_notices, 7200);
                } else {
                    set_transient('berocket_not_activated_notices', $not_activated_notices, 7200);
                }
            }
            $notices = array_merge($notices, $not_activated_notices);
            return $notices;
        }
        public static function get_plugin_count() {
            $count = count(self::$plugin_info);
            return $count;
        }
        public static function allow_berocket_host( $allow, $host, $url ) {
            if ( $host == 'berocket.com' )
                $allow = true;
            return $allow;
        }
        public static function test_key () {
            if( $curl = curl_init() ) {
                curl_setopt($curl, CURLOPT_URL, BeRocket_update_path.'main/account_updater');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
                curl_setopt($curl, CURLOPT_POST, true);
                $site_url = get_site_url();
                $postdata = 'key='.$_POST['key'].'&id='.$_POST['id'].'&url='.$site_url;
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
                $out = curl_exec($curl);
                $current_plugin = false;
                $out = json_decode($out, true);
                $out['upgrade'] = array();
                $options = self::get_options();
                if( $_POST['id'] != 0 ) {
                    foreach(self::$plugin_info as $plugin) {
                        if( $plugin['id'] == $_POST['id'] ) {
                            $current_plugin = $plugin;
                            break;
                        }
                    }
                    if( $current_plugin !== false ) {
                        if( empty($out['error']) ) {
                            $options['plugin_key'][$_POST['id']] = $_POST['key'];
                            if( isset($out['versions'][$_POST['id']]) && version_compare( $current_plugin['version'], $out['versions'][$_POST['id']], '<' ) ) {
                                $upgrade_button = '<a href="' . wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $current_plugin['plugin'], 'upgrade-plugin_' . $current_plugin['plugin'] ) . '" class="button-primary button">Upgrade plugin</a>';
                                $out['plugin_table'] = '<p>' . $upgrade_button . '</p>' . $out['plugin_table'];
                                $out['upgrade'][] = array('id' => $_POST['id'], 'upgrade' => $upgrade_button);
                            }
                        }
                    }
                } else {
                    foreach(self::$plugin_info as $plugin) {
                        if( isset($out['versions'][$plugin['id']]) && version_compare( $plugin['version'], $out['versions'][$plugin['id']], '<' ) ) {
                            $upgrade_button = '<a href="' . wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $plugin['plugin'], 'upgrade-plugin_' . $plugin['plugin'] ) . '" class="button-primary button">Upgrade plugin</a>';
                            $out['upgrade'][] = array('id' => $plugin['id'], 'upgrade' => $upgrade_button);
                        }
                    }
                    $options['account_key'] = $_POST['key'];
                }
                self::set_options($options);
                delete_site_transient( 'update_plugins' );
                $out = json_encode($out);
                echo $out;
                curl_close($curl);
            } else {
                $data = array('key_exist' => 0, 'status' => 'Failed', 'error' => 'cURL init failed. Please enable cURL for update.');
            }
            wp_die();
        }
        public static function scripts () {
            ?>
            <script>
                function BeRocket_key_check ( key, show_correct, product_id ) {
                    if ( typeof( product_id ) == 'undefined' || product_id == null ) {
                        product_id = 0;
                    }
                    data = {action: 'br_test_key',key: key, id: product_id};
                    is_submit = false;
                    jQuery.ajax({
                        url: ajaxurl,
                        data: data,
                        type: 'POST',
                        success: function (data) {
                                jQuery('.berocket_test_result').html(data);
                                if ( data.key_exist == 1 ) {
                                    if ( show_correct ) {
                                        html = '<h3>'+data.status+'</h3>';
                                        html +='<p><b>UserName: </b>'+data.username+'</p>';
                                        html +='<p><b>E-Mail: </b>'+data.email+'</p>';
                                        html +=data.plugin_table;
                                        jQuery('.berocket_test_result').html(html);
                                        data.upgrade.forEach(function( el, i, arr ) {
                                            jQuery('.berocket_product_key_'+el.id+'_status').html(el.upgrade);
                                        });
                                    }
                                    is_submit = true;
                                } else {
                                    html = '<h3>'+data.status+'</h3>';
                                    html +='<p><b>Error message:</b>'+data.error+'</p>';
                                    jQuery('.berocket_test_result').html(html);
                                }
                                jQuery('.berocket_product_key_'+product_id+'_status').text(data.status);
                            },
                        dataType: 'json',
                        async: false
                    });
                    return is_submit;
                }
                jQuery(document).on( 'click', '.berocket_test_account_product', function ( event ) {
                    event.preventDefault();
                    if ( jQuery(this).data('product') ) {
                        key = jQuery(jQuery(this).data('product')).val();
                    } else {
                        key = jQuery('#berocket_product_key').val();
                    }
                    BeRocket_key_check ( key, true, jQuery(this).data('id') );
                });
            </script>
            <style>
                .toplevel_page_berocket_account .dashicons-before img {
                    max-width: 16px;
                    max-height: 16px;
                }
            </style>
            <?php
        }
        public static function network_account_page () {
            add_menu_page( 
                'BeRocket Account Settings',
                'BeRocket Account',
                'manage_options', 
                'berocket_account', 
                array( __CLASS__, 'account_form_network'),
                plugin_dir_url( __FILE__ ).'ico.png',
                '55.55'
            );
        }
        public static function account_page () {
            if( self::get_plugin_count() > 3 ) {
                add_menu_page( 
                    'BeRocket Account Settings',
                    'BeRocket',
                    'manage_options', 
                    'berocket_account', 
                    array( __CLASS__, 'account_form'),
                    plugin_dir_url( __FILE__ ).'ico.png',
                    '55.55'
                );
            } else {
                add_submenu_page( 
                    'options-general.php',
                    'BeRocket Account Settings',
                    'BeRocket Account',
                    'manage_options', 
                    'berocket_account', 
                    array( __CLASS__, 'account_form')
                );
            }
        }
        public static function account_option_register () {
            register_setting( 'BeRocket_account_option_settings', 'BeRocket_account_option' );
        }
        public static function account_form () {
            ?>
            <div class="wrap">
                <form method="post" action="options.php" class="account_key_send">
                    <?php
                    $options = get_option( 'BeRocket_account_option' );
                    self::inside_form($options);
                    ?>
                </form>
            </div>
            <?php
        }
        public static function account_form_network () {
            ?>
            <div class="wrap">
                <form method="post" action="edit.php?page=berocket_account" class="account_key_send">
                    <?php
                    if(isset($_POST['BeRocket_account_option']))
                    {
                        update_site_option('BeRocket_account_option', $_POST['BeRocket_account_option']);
                    }
                    $options = get_site_option( 'BeRocket_account_option' );
                    self::inside_form($options);
                    ?>
                </form>
            </div>
            <?php
        }
        public static function inside_form ($options) {
            settings_fields('BeRocket_account_option_settings');
            if( isset( $options['plugin_key'] ) && is_array( $options['plugin_key'] ) ) {
                $plugins_key = $options['plugin_key'];
            } else {
                $plugins_key = array();
            }
            ?>
            <h2>BeRocket Account Settings</h2>
            <div>
                <table>
                    <tr>
                        <td><h3>DEBUG MODE</h3></td>
                        <td colspan=3><label><input type="checkbox" name="BeRocket_account_option[debug_mode]" value="1"<?php if( ! empty($options['debug_mode']) ) echo ' checked' ?>>Enable debug mode</label></td>
                    </tr>
                    <tr>
                        <td><h3>Account key</h3></td>
                        <td><input type="text" id="berocket_account_key" name="BeRocket_account_option[account_key]" size="50" value="<?php echo ( empty($options['account_key']) ? '' : $options['account_key'] ) ?>"></td>
                        <td><input class="berocket_test_account button-secondary" type="button" value="Test"></td>
                        <td class="berocket_product_key_0_status"></td>
                    </tr>
                <?php 
                foreach ( self::$plugin_info as $plugin ) {
                    echo '<tr class="berocket_updater_plugin_key" data-id="', $plugin['id'], '">';
                    echo '<td><h4>';
                    if( isset( $plugin['name'] ) ) {
                        echo $plugin['name'];
                    } else {
                        echo $plugin['slug'];
                    }
                    echo '</h4></td>';
                    echo '<td><input id="berocket_product_key_', $plugin['id'], '" size="50" name="BeRocket_account_option[plugin_key][', $plugin['id'], ']" type="text" value="', ( empty($options['plugin_key'][$plugin['id']]) ? '' : $options['plugin_key'][$plugin['id']] ), '"></td>';
                    echo '<td><input class="berocket_test_account_product button-secondary" data-id="', $plugin['id'], '" data-product="#berocket_product_key_', $plugin['id'], '" type="button" value="Test"></td>';
                    echo '<td class="berocket_product_key_status berocket_product_key_', $plugin['id'], '_status"></td>';
                    echo '</tr>';
                    unset( $plugins_key[$plugin['id']] );
                } 
                foreach($plugins_key as $key_id => $key_val) {
                    echo '<input name="BeRocket_account_option[plugin_key][', $key_id, ']" type="hidden" value="', $key_val, '">';
                }
                ?>
                </table>
            </div>
            <div class="berocket_test_result"></div>
            <input type="submit" class="button-primary" value="Save Changes" />
            <script>
                jQuery('.berocket_test_account').click( function ( event ) {
                    event.preventDefault();
                    key = jQuery('#berocket_account_key').val();
                    BeRocket_key_check ( key, true );
                });
                jQuery(document).on( 'submit', '.account_key_send', function ( event ) {
                    key = jQuery('#berocket_account_key').val();
                    if ( key != '' ) {
                        result = BeRocket_key_check ( key, false );
                        if ( ! result ) {
                            event.preventDefault();
                        }
                    }
                });
            </script>
            <?php
        }
        public static function update_check_set ( $value ) {
            if( is_network_admin() ) {
                $active_plugin = get_site_option('berocket_key_activated_plugins');
            } else {
                $active_plugin = get_option('berocket_key_activated_plugins');
            }
            foreach ( self::$plugin_info as $plugin ) {
                if( ! empty(self::$key) && strlen( self::$key ) == 40 )
                    $key = self::$key;
                if( ! empty($plugin['key']) && strlen( $plugin['key'] ) == 40 )
                    $key = $plugin['key'];
                $version = FALSE;
                if( ! empty($key) && $curl = curl_init() ) {
                    $url = BeRocket_update_path.'main/get_plugin_version/'.$plugin['id'].'/'.$key;
                    curl_setopt( $curl, CURLOPT_URL, $url );
                    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                    $site_url = get_site_url();
                    $postdata = 'url='.$site_url;
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
                    $version = curl_exec( $curl );
                    curl_close( $curl );
                    $version = json_decode( @ $version );
                    if ( ! empty($version->status) && $version->status == 'success' ) {
                        $version = $version->version;
                    } else {
                        $version = FALSE;
                    }
                }
                if( ! is_array($active_plugin) ) {
                    $active_plugin = array();
                }
                if ( $version !== FALSE ) {
                    $active_plugin[$plugin['id']] = true;
                    if ( version_compare( $plugin['version'], $version, '<' ) ) {
                        $value->checked[$plugin['plugin']] = $version;
                        $val = (object)array(
                            'id'          => 'br_'.$plugin['id'], 
                            'new_version' => $version, 
                            'package'     => BeRocket_update_path.'main/update_product/'.$plugin['id'].'/'.$key,
                            'url'         => BeRocket_update_path.'product/'.$plugin['id'],
                            'plugin'      => $plugin['plugin'],
                            'slug'        => $plugin['slug']
                        );
                        $value->response[$plugin['plugin']] = $val;
                    }
                } else {
                    $active_plugin[$plugin['id']] = false;
                }
            }
            if( is_network_admin() ) {
                update_site_option('berocket_key_activated_plugins', $active_plugin);
            } else {
                update_option('berocket_key_activated_plugins', $active_plugin);
            }
            delete_site_transient('berocket_not_activated_notices_site');
            delete_transient('berocket_not_activated_notices');
            if ( isset( $value->no_update ) && is_array( $value->no_update ) ) {
                foreach ( $value->no_update as $key => $val ) {
                    if ( isset($val->slug) && in_array( $val->slug, self::$slugs ) ) {
                        unset($value->no_update[$key]);
                    }
                }
            }
            return $value;
        }
        public static function plugin_info() {
            $plugin = wp_unslash( $_REQUEST['plugin'] );
            if ( in_array( $plugin, self::$slugs ) ) {
                remove_action('install_plugins_pre_plugin-information', 'install_plugin_information');
                $plugin_id = array_search( $plugin, self::$slugs );
                if( $curl = curl_init() ) {
                    $url = BeRocket_update_path.'main/update_info/'.$plugin_id;
                    curl_setopt( $curl, CURLOPT_URL, $url );
                    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
                    $plugin_info = curl_exec( $curl );
                    curl_close( $curl );
                    echo $plugin_info;
                    die;
                }
            }
        }
        public static function get_options() {
            if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
                $options = get_site_option( 'BeRocket_account_option' );
            else
                $options = get_option( 'BeRocket_account_option' );
            return $options;
        }
        public static function set_options($options) {
            if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) )
                update_site_option( 'BeRocket_account_option', $options );
            else
                update_option( 'BeRocket_account_option', $options );
        }
    }
    add_action( 'plugins_loaded', array( 'BeRocket_updater', 'run' ), 999 );
}
?>
