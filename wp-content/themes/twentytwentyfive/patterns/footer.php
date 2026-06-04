<?php
/**
 * Title: Footer
 * Slug: twentytwentyfive/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Description: Footer with logo, tagline and links.
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:group {"style":{"spacing":{"blockGap":"8px"}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group">
			<!-- wp:site-title {"level":2,"style":{"typography":{"fontWeight":"700","fontSize":"1.25rem"}}} /-->
			<!-- wp:site-tagline /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"right","flexWrap":"wrap"}} -->
			<!-- wp:navigation-link {"label":"Shop","type":"page","id":6,"url":"?page_id=6","kind":"post-type","isTopLevelLink":true} /-->
			<!-- wp:navigation-link {"label":"Cart","type":"page","id":7,"url":"?page_id=7","kind":"post-type","isTopLevelLink":true} /-->
			<!-- wp:navigation-link {"label":"Account","type":"page","id":9,"url":"?page_id=9","kind":"post-type","isTopLevelLink":true} /-->
		<!-- /wp:navigation -->
	</div>
	<!-- /wp:group -->

	<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
	<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"0.8rem"},"color":{"text":"#888"}}} -->
	<p class="has-text-align-center has-text-color" style="color:#888;font-size:0.8rem">Powered by Tung Tung Tung &mdash; &copy; Triple T Shop</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
