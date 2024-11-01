<?php
## Allowing Direct File Access to plugin files
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$pageURL = $_SERVER["REQUEST_URI"];
$parts = Explode('/', $pageURL);
$page= $parts[count($parts) - 1] ;
$redvalue= substr($page,0,12);
if($redvalue=='redirect.php')
{
$phrase  = $pageURL;
$healthy = array("redirect.php");
$yummy   = array("");

$newphrase = str_replace($healthy, $yummy, $phrase);
header("location:".$newphrase);
}

$GLOBALS['wpdemobar_settings']->wpdemobar_is_firefox();
$GLOBALS['wpdemobar_settings']->wpdemobar_is_windows();

## get current theme name
$option = get_option('fbar_settings');
$poption = get_option('page_fbar_settings');
$current_theme = $_GET['theme'];
$theme_found = false;

## build theme data array
global $wpdb;
$theme_array = $GLOBALS['wpdemobar_settings']->wpdemobar_get_sites();
$page_slug=get_option('wpt_page_slug');

if (!get_option('xredirect') || 1):
## get current theme data

    foreach ($theme_array as $i => $theme) :

        if ($theme['slug'] == $current_theme) :

            $current_theme_name = ucfirst($theme['slug']);
            $current_theme_url = $theme['site_url'];
            $current_theme_purchase_url = $theme['download_url'];
            $ptype=$theme['ptype'];
            $price=$theme['price'];

            $theme_found = true;

        endif;

    endforeach;

    if ($theme_found == false) :
        $current_theme_name = $theme_array[0]['slug'];
        $current_theme_url = $theme_array[0]['site_url'];
        $current_theme_purchase_url = $theme_array[0]['download_url'];
        $ptype=$theme_array[0]['ptype'];
        $price=$theme_array[0]['price'];
    endif;
 $currentFile = $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

    ?>
    <!DOCTYPE html>
    <!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
    <!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
    <!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
    <!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
            <?php $GLOBALS['wpdemobar_settings']->wpdemobar_custom_title();?>
            <?php

            echo '<!-- Mobile Specific -->';
            echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
            echo '<meta name="description" content="'.get_option("wpt_meta_description").'" >';
            echo '<meta name="keywords" content="'.get_option("wpt_meta_keywords").'" >';            
            // Favicon
            echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.esc_url(wp_get_attachment_thumb_url(get_option("wpt_page_icon"))).'" />';

            ?>
            <!-- CSS Style -->
            <link rel="stylesheet" href="<?php echo WPDEMOBAR_PLUGIN_URL ?>/assets/css/frame.css"> 
            <!-- JavaScript -->
            <?php  global $wp_scripts;
            wp_print_scripts( array( 'jquery','jquery-ui-core' ) );
            ?>

            <script src="<?php echo WPDEMOBAR_PLUGIN_URL; ?>/assets/js/custom.js"></script> 

            <link rel="profile" href="http://gmpg.org/xfn/11" />
            <link rel="pingback" href="<?php bloginfo( "pingback_url" ) ; ?>" />
        </head>

        <body>
        <?php

        ?>
            <div id="switch-bar">
                <div class="switch-container">
                    <div class="logo">
                        <a href="<?php echo get_permalink($page_slug) ?>" target="_blank" title="<?php echo get_option('wpt_page_title') ?>">

                            <img height="40" src="<?php echo (get_option('wpt_an_image')!="")?wp_get_attachment_thumb_url(get_option('wpt_an_image')) : '' ?>" alt="<?php echo get_option('wpt_page_title') ?>" />
                        </a>
                    </div>

                    <ul>
                        <li id="theme_list"><a id="theme_select" href="#"><?php
                                if ($theme_found == false) : echo "Select a theme...";
                                else: echo $current_theme_name;
                                endif;
                                ?><i aria-hidden="true" class="fa fa-angle-up"></i></a>

                            <ul id="test1a">
                                <?php
                                foreach ($theme_array as $i => $theme) :
                                    echo '<li><a href="'.get_permalink(absint(get_option( "wpt_page_slug" ))). '?theme='.strtolower($theme['slug']).'" >' .
                                    ucfirst($theme['title']) .
                                    ' <span style="background-color: ' . $theme['color'] . '">' . $theme['type'] . '</span></a>';
                                        echo '<img alt="" class="preview" src="';
                                        
                                            echo esc_url($theme['screen'][0]);
                                        echo '" />';
                                    echo '</li>';
                                endforeach;
                                ?>
                            </ul>
                        </li>	
                    </ul>
                    <?php if (in_array(1,get_option('wpt_responsive'))) { ?>
                        <div class="responsive">
                            <a href="#" class="desktop active" title="<?php echo esc_html__( 'View Desktop Version', 'wpdemobar' ); ?>"><i aria-hidden="true" class="fa fa-desktop"></i></a> 
                            <a href="#" class="tabletlandscape" title="<?php echo esc_html__( 'View Tablet Landscape (1024x768)', 'wpdemobar' ); ?>"><i aria-hidden="true" class="fa fa-tablet"></i></a> 
                            <a href="#" class="tabletportrait" title="<?php echo esc_html__( 'View Tablet Portrait (768x1024)', 'wpdemobar' ); ?>"><i aria-hidden="true" class="fa fa-tablet"></i></a> 
                            <a href="#" class="mobilelandscape" title="<?php echo esc_html__( 'View Mobile Landscape (480x320)', 'wpdemobar' ); ?>"><i aria-hidden="true" class="fa fa-mobile"></i></a>
                            <a href="#" class="mobileportrait" title="<?php echo esc_html__( 'View Mobile Portrait (320x480)', 'wpdemobar' ); ?>"><i aria-hidden="true" class="fa fa-mobile"></i></a>
                        </div>
                    <?php } ?>
                    <?php if (in_array(1,get_option('wpt_share'))) { ?>
                                                
                    <div class="share">
                        <ul class="share-buttons">
                          <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urldecode($current_theme_purchase_url); ?>&t=WordPress%20Themes%20and%20Plugins" title="<?php echo esc_html__( 'Share on Facebook', 'wpdemobar' ); ?>Share on Facebook" target="_blank"><i aria-hidden="true" class="fa fa-facebook"></i></a></li>
                          <li><a href="http://www.tumblr.com/share?v=3&u=<?php echo urldecode($current_theme_purchase_url); ?>&t=WordPress%20Themes%20and%20Plugins&s=" target="_blank" title="<?php echo esc_html__( 'Post on Tumblr', 'wpdemobar' ); ?>"><i aria-hidden="true" class="fa fa-tumblr"></i></a></li>
                          <li><a href="http://wordpress.com/press-this.php?u=<?php echo urldecode($current_theme_purchase_url); ?>&t=WordPress%20Themes%20and%20Plugins&s=We%20love%20to%20work%20in%20WordPress%20%2Cpractically%20anything.%20We%20develop%20themes%2C%20plugins.%20We%20customize%20themes%2C%20plugins.%20We%20make%20free%20and%20premium%20themes%2C%20help%20others%20learn%20and%20use%20WordPress!" target="_blank" title="<?php echo esc_html__( 'Publish on WordPress', 'wpdemobar' ); ?>"><i aria-hidden="true" class="fa fa-wordpress"></i></a></li>
                        </ul>

                    </div>
                    <?php } ?>
                            
                    
                    
                    <ul class="links">
                        
                            <li class="purchase" rel="<?php echo $current_theme_purchase_url; ?>">
                                <span class="demo-price"><?php if ($ptype=='premium' && $price!="") { ?>$<?php echo $price;?><?php } else { echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp";} ?></span>
                            </li>
                        
                        <?php if (in_array(1,get_option('wpt_purchase'))) { ?>
                            <li class="purchase" rel="<?php echo $current_theme_purchase_url; ?>">
                                <a href="<?php echo $current_theme_purchase_url; ?>"><i aria-hidden="true" class="fa <?php echo ($ptype=='premium')?'fa-shopping-cart':'fa-download';?>"></i><?php echo ($ptype=='premium')?__('Buy Now','wpdemobar'):__('Download','wpdemobar');?></a>
                            
                            </li>
                        <?php } ?>
                        <?php if (in_array(1,get_option('wpt_close'))) { ?>
                            <li class="close" rel="<?php echo $current_theme_url; ?>">
                                <a href="<?php echo $current_theme_url; ?>"><i aria-hidden="true" class="fa fa-close"></i> <?php echo esc_html__('Close','wpdemobar');?></a></li>		
                        <?php } ?>

                    </ul>
                </div>
                </div>
          </div>

            <iframe id="iframe" src="<?php echo $current_theme_url; ?>" frameborder="0" width="100%" height="100%"></iframe>
              
        </body>
    </html>
    <?php
endif;
?>