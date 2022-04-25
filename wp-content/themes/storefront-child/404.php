<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

			<div class="error-404 not-found row-spacing-top">

				<div class="page-content">

					<header class="not-found-header">
						<h1 class="not-found-title"><?php esc_html_e( 'Oops!', 'storefront' ); ?></h1>
					</header><!-- .page-header -->

					<h2><strong><?php esc_html_e( '404', 'storefront' ); ?></strong><?php esc_html_e( 'Page Not Found', 'storefront' ); ?></h2>
					<p><?php esc_html_e( 'Sorry an error has occured, Request page not found!', 'storefront' ); ?></p>
					<a href="<?php echo site_url(); ?>" class="btn btn-default">Back to home page</a>

					<?php
					echo '<section class="search-box" aria-label="' . esc_html__( 'Search', 'storefront' ) . '">';

					if ( storefront_is_woocommerce_activated() ) {
						the_widget( 'WC_Widget_Product_Search' );
					} else {
						get_search_form();
					}

					echo '</section>';

					if ( storefront_is_woocommerce_activated() ) {

						echo '<div class="fourohfour-columns-2 sec-hidden">';

							echo '<section class="col-1" aria-label="' . esc_html__( 'Promoted Products', 'storefront' ) . '">';

								storefront_promoted_products();

							echo '</section>';

							echo '<nav class="col-2" aria-label="' . esc_html__( 'Product Categories', 'storefront' ) . '">';

								echo '<h2>' . esc_html__( 'Product Categories', 'storefront' ) . '</h2>';

								the_widget( 'WC_Widget_Product_Categories', array(
									'count' => 1,
								) );

							echo '</nav>';

						echo '</div>';

						echo '<section class="sec-hidden" aria-label="' . esc_html__( 'Popular Products', 'storefront' ) . '">';

							echo '<h2>' . esc_html__( 'Popular Products', 'storefront' ) . '</h2>';

							echo storefront_do_shortcode( 'best_selling_products', array(
								'per_page' => 4,
								'columns'  => 4,
							) );

						echo '</section>';
					}
					?>

				</div><!-- .page-content -->
			</div><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer();
