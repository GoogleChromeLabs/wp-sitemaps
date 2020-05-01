<?php

class Test_Core_Sitemaps_Stylesheet extends WP_UnitTestCase {
	/**
	 * Test that stylesheet content can be filtered.
	 */
	public function test_filter_sitemaps_stylesheet_content() {
		$stylesheet = new Core_Sitemaps_Stylesheet();

		add_filter( 'core_sitemaps_stylesheet_content', '__return_empty_string' );

		$content = $stylesheet->get_sitemap_stylesheet();

		remove_filter( 'core_sitemaps_stylesheet_content', '__return_empty_string' );

		$this->assertSame( '', $content, 'Could not filter stylesheet content' );
	}

	/**
	 * Test that sitemap index stylesheet content can be filtered.
	 */
	public function test_filter_sitemaps_index_stylesheet_content() {
		$stylesheet = new Core_Sitemaps_Stylesheet();

		add_filter( 'core_sitemaps_index_stylesheet_content', '__return_empty_string' );

		$content = $stylesheet->get_sitemap_index_stylesheet();

		remove_filter( 'core_sitemaps_index_stylesheet_content', '__return_empty_string' );

		$this->assertSame( '', $content, 'Could not filter sitemap index stylesheet content' );
	}

	/**
	 * Test that sitemap stylesheet CSS can be filtered.
	 */
	public function test_filter_sitemaps_stylesheet_css() {
		$stylesheet = new Core_Sitemaps_Stylesheet();

		add_filter( 'core_sitemaps_stylesheet_css', '__return_empty_string' );

		$css = $stylesheet->get_stylesheet_css();

		remove_filter( 'core_sitemaps_stylesheet_css', '__return_empty_string' );

		$this->assertSame( '', $css, 'Could not filter sitemap stylesheet CSS' );
	}
}
