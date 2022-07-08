<?php
/**
 * The template used for 404 pages.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>
<section id="content" class="full-width">
	<div id="post-404page" class="post-404page">
		<div class="post-content">
			<div class="opps">
				<div>
					<h1 class="secTitle">Oops</h1>
					<h3 class="subTitle">This Page Could Not Be Found!</h3>
					<div class="btnArea">
						<a href="<?php echo site_url(); ?>" class="btn btn-primary">Back To Home</a>
					</div>
				</div>
			</div>							
		</div>
	</div>
</section>
<?php get_footer(); ?>
