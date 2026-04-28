<?php
/**
 * Plugin Name: Homely Property Slider
 * Description: A professional slider with Dynamic Gallery support and full Elementor styling.
 * Version: 1.5.2
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
            
            // --- CONTENT TAB ---
            $this->start_controls_section('section_content', ['label' => 'Content']);

            $this->add_control('gallery', [
                'label' => 'Select Images',
                'type' => \Elementor\Controls_Manager::GALLERY,
                'dynamic' => [ 'active' => true ], // RESTORED: This enables JetEngine/ACF support
                'default' => [],
            ]);

            $this->add_control('thumb_position', [
                'label' => 'Thumbnail Position',
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'right',
                'options' => [
                    'right' => 'Main Left / Thumbs Right',
                    'left'  => 'Thumbs Left / Main Right',
                ],
            ]);

            $this->add_control('visible_thumbs', [
                'label' => 'Visible Thumbnails',
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 4,
            ]);

            $this->add_control('show_arrows', [
                'label' => 'Show Arrows',
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'separator' => 'before',
            ]);

            $this->add_control('next_icon', [
                'label' => 'Next Icon',
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [ 'value' => 'fas fa-chevron-right', 'library' => 'solid' ],
                'condition' => ['show_arrows' => 'yes'],
            ]);

            $this->add_control('prev_icon', [
                'label' => 'Previous Icon',
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [ 'value' => 'fas fa-chevron-left', 'library' => 'solid' ],
                'condition' => ['show_arrows' => 'yes'],
            ]);

            $this->end_controls_section();

            // --- STYLE TAB: THUMBNAILS ---
            $this->start_controls_section('style_thumbs', [
                'label' => 'Thumbnails',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]);

            $this->add_control('thumb_height', [
                'label' => 'Thumb Height (px)',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [ 'px' => [ 'min' => 50, 'max' => 250 ] ],
                'selectors' => [
                    '{{WRAPPER}} .homely-thumb-slider .swiper-slide' => 'height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]);

            $this->add_control('active_border_color', [
                'label' => 'Active Border Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#811e3b',
                'selectors' => [
                    '{{WRAPPER}} .homely-thumb-slider .swiper-slide-thumb-active' => 'border-color: {{VALUE}};',
                ],
            ]);

            $this->end_controls_section();

            // --- STYLE TAB: ARROWS ---
            $this->start_controls_section('style_arrows', [
                'label' => 'Navigation Icons',
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => ['show_arrows' => 'yes'],
            ]);

            $this->start_controls_tabs('arrow_tabs');

            $this->start_controls_tab('arrow_normal', ['label' => 'Normal']);
            $this->add_control('arrow_color', [
                'label' => 'Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .homely-arrow' => 'color: {{VALUE}};'],
            ]);
            $this->add_control('arrow_bg', [
                'label' => 'Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .homely-arrow' => 'background-color: {{VALUE}};'],
            ]);
            $this->end_controls_tab();

            $this->start_controls_tab('arrow_hover', ['label' => 'Hover']);
            $this->add_control('arrow_hover_color', [
                'label' => 'Hover Color',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .homely-arrow:hover' => 'color: {{VALUE}};'],
            ]);
            $this->add_control('arrow_hover_bg', [
                'label' => 'Hover Background',
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => ['{{WRAPPER}} .homely-arrow:hover' => 'background-color: {{VALUE}};'],
            ]);
            $this->end_controls_tab();

            $this->end_controls_tabs();

            $this->add_responsive_control('arrow_size_box', [
                'label' => 'Icon Box Size',
                'type' => \Elementor\Controls_Manager::SLIDER,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .homely-arrow' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
                    '{{WRAPPER}} .homely-arrow i, {{WRAPPER}} .homely-arrow svg' => 'font-size: calc({{SIZE}}px / 2.5);',
                ],
            ]);

            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();
            $gallery = $settings['gallery'];
            if ( empty( $gallery ) ) return;

            $id = 'homely-' . $this->get_id();
            $flex_dir = ($settings['thumb_position'] === 'left') ? 'row-reverse' : 'row';
            ?>
            
            <div id="<?php echo $id; ?>" class="homely-slider-container" style="flex-direction: <?php echo $flex_dir; ?>;">
                <div class="swiper homely-main-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ( $gallery as $image ) : 
                            $img_url = wp_get_attachment_image_url($image['id'], 'full') ?: $image['url'];
                        ?>
                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( $img_url ); ?>" alt="Property">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if ( 'yes' === $settings['show_arrows'] ) : ?>
                        <div class="swiper-button-prev homely-arrow"><?php \Elementor\Icons_Manager::render_icon( $settings['prev_icon'], [ 'aria-hidden' => 'true' ] ); ?></div>
                        <div class="swiper-button-next homely-arrow"><?php \Elementor\Icons_Manager::render_icon( $settings['next_icon'], [ 'aria-hidden' => 'true' ] ); ?></div>
                    <?php endif; ?>
                </div>

                <div class="swiper homely-thumb-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ( $gallery as $image ) : 
                            $thumb_url = wp_get_attachment_image_url($image['id'], 'medium') ?: $image['url'];
                        ?>
                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="Thumb">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <style>
                .homely-slider-container { display: flex; gap: 15px; height: 500px; width: 100%; }
                .homely-main-slider { flex: 4; position: relative; overflow: hidden; border-radius: 8px; }
                .homely-thumb-slider { flex: 1; overflow: hidden; border-radius: 8px; }
                
                .homely-slider-container img { width: 100%; height: 100%; object-fit: cover; display: block; }
                
                /* Thumb Styles */
                .homely-thumb-slider .swiper-slide { height: 110px !important; cursor: pointer; transition: 0.3s; border: 3px solid transparent; border-radius: 6px; overflow: hidden; opacity: 0.6; }
                .homely-thumb-slider .swiper-slide-thumb-active { opacity: 1; border-color: #811e3b; }

                /* Arrow Styles */
                .homely-arrow { background: #811e3b; color: #fff; border-radius: 50%; display: flex !important; align-items: center; justify-content: center; transition: 0.3s; z-index: 10; }
                .homely-arrow::after { content: none !important; } 

                @media (max-width: 767px) {
                    .homely-slider-container { flex-direction: column !important; height: auto; }
                    .homely-main-slider { height: 350px; }
                    .homely-thumb-slider { height: 90px; width: 100%; }
                    .homely-thumb-slider .swiper-slide { height: 100% !important; width: 90px !important; }
                }
            </style>

            <script>
            jQuery(document).ready(function($) {
                const $scope = $('#<?php echo $id; ?>');
                const visibleCount = <?php echo intval($settings['visible_thumbs']); ?>;
                
                const thumbs = new Swiper($scope.find('.homely-thumb-slider')[0], {
                    direction: window.innerWidth > 767 ? 'vertical' : 'horizontal',
                    slidesPerView: window.innerWidth > 767 ? visibleCount : 'auto',
                    spaceBetween: 10,
                    watchSlidesProgress: true,
                    slideToClickedSlide: true,
                });

                new Swiper($scope.find('.homely-main-slider')[0], {
                    spaceBetween: 10,
                    loop: true,
                    navigation: {
                        nextEl: $scope.find('.swiper-button-next')[0],
                        prevEl: $scope.find('.swiper-button-prev')[0],
                    },
                    thumbs: { swiper: thumbs },
                });
            });
            </script>
            <?php
        }
    }
    $widgets_manager->register( new \Homely_Property_Slider() );
});