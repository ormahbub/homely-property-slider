<?php
/**
 * Plugin Name: Homely Property Slider
 * Description: A simple slider widget for Elementor, created based on client's requirements.
 * Version: 1.3.0
 * Author: Mahbub
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'elementor/widgets/register', function( $widgets_manager ) {

    class Homely_Property_Slider extends \Elementor\Widget_Base {

        public function get_name() { return 'homely_property_slider'; }
        public function get_title() { return 'Property Gallery Slider'; }
        public function get_icon() { return 'eicon-gallery-grid'; }
        public function get_categories() { return [ 'general' ]; }
        public function get_script_depends() { return [ 'swiper' ]; }
        public function get_style_depends() { return [ 'swiper' ]; }

        protected function register_controls() {
            $this->start_controls_section('section_content', ['label' => 'Content']);
            $this->add_control('gallery', [
                'label' => 'Select Images',
                'type' => \Elementor\Controls_Manager::GALLERY,
                'dynamic' => ['active' => true],
            ]);
            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();
            $gallery = $settings['gallery'];
            if ( empty( $gallery ) ) return;

            $id = 'homely-slider-' . $this->get_id();
            ?>
            
            <div id="<?php echo $id; ?>" class="homely-slider-container">
                <div class="swiper homely-main-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ( $gallery as $image ) : 
                            $url = wp_get_attachment_image_url($image['id'], 'full') ?: $image['url'];
                        ?>
                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( $url ); ?>" alt="Property">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>

                <div class="swiper homely-thumb-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ( $gallery as $image ) : 
                            $url = wp_get_attachment_image_url($image['id'], 'medium') ?: $image['url'];
                        ?>
                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( $url ); ?>" alt="Thumb">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <style>
                /* Force visibility */
                .homely-slider-container { display: flex; gap: 10px; height: 500px; min-height: 300px; width: 100%; opacity: 1 !important; }
                .homely-main-slider { flex: 4; height: 100%; position: relative; overflow: hidden; background: #f0f0f0; }
                .homely-thumb-slider { flex: 1; height: 100%; overflow: hidden; }
                
                /* Ensure slides have dimensions */
                .homely-slider-container .swiper-slide { width: 100% !important; height: 100% !important; }
                .homely-slider-container img { width: 100%; height: 100%; object-fit: cover; display: block; }
                
                .homely-thumb-slider .swiper-slide { height: 20% !important; opacity: 0.5; border: 2px solid transparent; }
                .homely-thumb-slider .swiper-slide-thumb-active { opacity: 1; border-color: #811e3b; }
                
                .homely-main-slider .swiper-button-next, .homely-main-slider .swiper-button-prev { background: #811e3b; color: #fff; width: 35px; height: 35px; border-radius: 50%; }
                .homely-main-slider .swiper-button-next::after, .homely-main-slider .swiper-button-prev::after { font-size: 14px; }

                @media (max-width: 767px) {
                    .homely-slider-container { flex-direction: column; height: 400px; }
                    .homely-thumb-slider .swiper-slide { height: 100% !important; width: 80px !important; }
                }
            </style>

            <script>
            jQuery(window).on('elementor/frontend/init', function() {
                elementorFrontend.hooks.addAction('frontend/element_ready/homely_property_slider.default', function($scope) {
                    const container = $scope.find('.homely-slider-container')[0];
                    
                    const thumbs = new Swiper($scope.find('.homely-thumb-slider')[0], {
                        direction: window.innerWidth > 767 ? 'vertical' : 'horizontal',
                        slidesPerView: 4,
                        spaceBetween: 10,
                        watchSlidesProgress: true,
                        observer: true,
                        observeParents: true,
                    });

                    new Swiper($scope.find('.homely-main-slider')[0], {
                        spaceBetween: 10,
                        grabCursor: true,
                        observer: true,
                        observeParents: true,
                        navigation: {
                            nextEl: $scope.find('.swiper-button-next')[0],
                            prevEl: $scope.find('.swiper-button-prev')[0],
                        },
                        thumbs: { swiper: thumbs },
                    });
                });
            });
            </script>
            <?php
        }
    }
    $widgets_manager->register( new \Homely_Property_Slider() );
});