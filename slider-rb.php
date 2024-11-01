<?php
/**
 * Plugin Name:       Slider RB - image slider, photo slider, content slider
 * Description:       Slider RB one of the most simple and easiest ways to create an image slider or content slider in your WordPress blog. Simple photo slider block.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.0.3
 * Author:            rbPlugins
 * Author URI:        https://profiles.wordpress.org/rbplugins/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       slider-rb
 *
 * @package           slider-rb
 */

define("RB_SLIDER_VERSION", "1.0.3");

function slider_rb_block_init()
{
    register_block_type(
        __DIR__,
        array(
            'render_callback' => "rbSliderBlockRender",
        )
    );
}

add_action('init', 'slider_rb_block_init');

add_action('wp_enqueue_scripts', 'rbslider_enqueue_custom_assets');
function rbslider_enqueue_custom_assets()
{
    wp_enqueue_script('rbslider-slider', plugin_dir_url(__FILE__) . '/assets/swiper-bundle.min.js', array(), RB_SLIDER_VERSION, false);
    wp_enqueue_style('rbslider-slider', plugin_dir_url(__FILE__) . '/assets/swiper-bundle.min.css', array(), RB_SLIDER_VERSION);
}

if (!function_exists("rbSliderBlockRender")) {

    function rbSliderBlockRender($attributes, $content)
    {
        if (!isset($attributes['mediaIDs']) || !count($attributes['mediaIDs'])) {
            return '<p style="text-align:center;font-size: 30px;">Please select images</p>';
        }

        //print_r( $attributes );
        //print_r( $content  );

        $slides = '';

        for ($i = 0; $i < count($attributes['mediaIDs']); $i++) {
            $el = $attributes['mediaIDs'][$i];

            $slides .= '<div class="swiper-slide" style="background-image: url(' . $el['url'] . ');" >
                            <div class="rb-slider-desc">' . $el['caption'] . '</div>
                        </div>';
        }
        $u_id = wp_unique_id('rb_slider_');
        
        $width = "100%";
        if (isset($attributes["width"]) && isset($attributes["widthUnit"])) {
            $width = $attributes["width"] . $attributes["widthUnit"];
        }

        $height = "40vh";
        if (isset($attributes["height"]) && isset($attributes["heightUnit"])) {
            $height = $attributes["height"] . $attributes["heightUnit"];
        }

        $autoplay = 'autoplay: false,';

        if (isset($attributes["autoplay"]) && $attributes["autoplay"]) {
            $autoplay = '
                autoplay: {
                    delay: ' . (isset($attributes["delay"]) ? (int) $attributes["delay"] : 3000) . ',
                    pauseOnMouseEnter: true
                },
            ';
        }

        return '
        <div class="swiper rb-swiper" id="' . $u_id . '" style="width: '.$width.'; height: '.$height.';">
            <div class="swiper-wrapper">' . $slides . '</div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
      </div>
            <script>
                if( typeof window.swiperArr === \'undefined\' ){
                    window.swiperArr = [];
                }
                window.swiperArr.push(
                    new Swiper("#' . $u_id . '", {
                        direction: ' . (isset($attributes["delay"]) && $attributes["delay"] == "vertical" ? '"vertical"' : '"horizontal"') . ',
                        speed: 400,
                        spaceBetween: 30,
                        navigation: {
                            nextEl: ".swiper-button-next",
                            prevEl: ".swiper-button-prev",
                        },

                        ' . $autoplay . '
                    })
                );
            </script>
        ';
    }
}
