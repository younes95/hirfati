<!-- CONTAINER -->
<?php
$azera_website_name = get_bloginfo( 'name', 'display' );
$azera_website_description = get_bloginfo( 'description' );
$azera_shop_big_title_logo = get_theme_mod('azera_shop_big_title_logo', azera_shop_get_file('/images/logo-2.png'));
$azera_shop_header_title = get_theme_mod('azera_shop_header_title', $azera_website_name);
$azera_shop_header_subtitle = get_theme_mod('azera_shop_header_subtitle', $azera_website_description);
$azera_shop_header_button_text = get_theme_mod('azera_shop_header_button_text',esc_html__('GET STARTED','algibro-shop'));
$azera_shop_header_button_link = get_theme_mod('azera_shop_header_button_link','#');
$azera_shop_enable_move = get_theme_mod('azera_shop_enable_move');
$azera_shop_first_layer = get_theme_mod('azera_shop_first_layer', azera_shop_get_file('/images/background1.png'));
$azera_shop_second_layer = get_theme_mod('azera_shop_second_layer',azera_shop_get_file('/images/background2.png'));

if(!empty($azera_shop_big_title_logo) || !empty($azera_shop_header_title) || !empty($azera_shop_header_subtitle) || !empty($azera_shop_header_button_text)){ ?>
	<div class="header-section-inner-wrap">
		<?php
		if( isset($azera_shop_enable_move) && $azera_shop_enable_move !== true ) {

			echo '<ul id="parallax_move">';
				if ( empty($azera_shop_first_layer) && empty($azera_shop_second_layer) ) {
					$azera_shop_header_image2 = get_header_image();
					echo '<li class="layer layer1" data-depth="0.10" style="background-image: url('.$azera_shop_header_image2.');"></li>';
				} else {
					if( !empty($azera_shop_first_layer) )  {
						echo '<li class="layer layer1" data-depth="0.10" style="background-image: url('.esc_url($azera_shop_first_layer).');"></li>';
					}
					if( !empty($azera_shop_second_layer) ) {
						echo '<li class="layer layer2" data-depth="0.20" style="background-image: url('.esc_url($azera_shop_second_layer).');"></li>';
					}
				}
			echo '</ul>';
		} ?>

		<div class="overlay-layer-wrap">
			<div class="container overlay-layer" id="parallax_header">
				<div class="row">
					<div class="col-md-12 intro-section-text-wrap">
						<!-- ONLY LOGO ON HEADER -->
						<?php
						if( !empty($azera_shop_big_title_logo) || is_customize_preview() ) { ?>
							<div class="only-logo">
								<div id="only-logo-inner" class="navbar">
									<div id="parallax_only_logo" class="navbar-header">
										<img src="<?php if(!empty($azera_shop_big_title_logo) ) { echo esc_url($azera_shop_big_title_logo); } ?>" alt="">
									</div>
								</div>
							</div>
							<?php
						}

						if(!empty($paralax_one_header_logo) || !empty($azera_shop_header_title) || !empty($azera_shop_header_subtitle) || !empty($azera_shop_header_button_text)){?>
							<div id="intro-section" class="intro-section">

								<!-- WELCOM MESSAGE -->
								<?php
								if( !empty($azera_shop_header_subtitle) ){
									echo '<h5 id="intro_section_text_2" class="white-text">'.wp_kses_post($azera_shop_header_subtitle).'</h5>';
								} else {
									if ( is_customize_preview() ) {
										echo '<h5 id="intro_section_text_2" class="white-text azera_shop_only_customizer"></h5>';
									}
								}

								if( !empty($azera_shop_header_title) ){
									echo '<h1 id="intro_section_text_1" class="intro white-text">'.wp_kses_post($azera_shop_header_title).'</h1>';
								} else {
									if ( is_customize_preview() ) {
										echo '<h1 id="intro_section_text_1" class="intro white-text azera_shop_only_customizer"></h1>';
									}
								}

								if( !empty($azera_shop_header_button_text) ){
									if( empty($azera_shop_header_button_link) ){
										echo '<button id="inpage_scroll_btn" class="btn btn-primary standard-button inpage-scroll"><span class="screen-reader-text">'.esc_html__('Header button label:','algibro-shop').esc_html($azera_shop_header_button_text).'</span>'.wp_kses_post($azera_shop_header_button_text).'</button>';
									} else {
										if(strpos($azera_shop_header_button_link, '#') === 0) {
											echo '<button id="inpage_scroll_btn" class="btn btn-primary standard-button inpage-scroll" data-anchor="'.esc_attr($azera_shop_header_button_link).'"><span class="screen-reader-text">'.esc_html__('Header button label:','algibro-shop').esc_html($azera_shop_header_button_text).'</span>'.wp_kses_post($azera_shop_header_button_text).'</button>';
										} else {
											echo '<button id="inpage_scroll_btn" class="btn btn-primary standard-button inpage-scroll" onClick="parent.location=\''.esc_url($azera_shop_header_button_link).'\'"><span class="screen-reader-text">'.esc_html__('Header button label:','algibro-shop').esc_html($azera_shop_header_button_text).'</span>'.wp_kses_post($azera_shop_header_button_text).'</button>';
										}
									}
								} else {
									if ( is_customize_preview() ) {
										echo '<div id="intro_section_text_3" class="button"><div id="inpage_scroll_btn"><a href="" class="btn btn-primary standard-button inpage-scroll azera_shop_only_customizer"></a></div></div>';
									}
								} ?>
							</div>
							<?php
						} ?>
					</div>
				</div>
			</div>
		</div>
	</div><!-- .header-section-inner-wrap -->
	<?php
} ?>
