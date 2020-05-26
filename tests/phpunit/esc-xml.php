<?php

/**
 * @group formatting
 */
class Tests_Formatting_EscXml extends WP_UnitTestCase {
	public function test_esc_xml_basics() {
		// Simple string.
		$html = 'The quick brown fox.';
		$this->assertEquals( $html, esc_xml( $html ) );

		// URL with &.
		$html = 'http://localhost/trunk/wp-login.php?action=logout&_wpnonce=cd57d75985';
		$this->assertEquals( $html, esc_xml( $html ) );

		// SQL query.
		$html     = "SELECT meta_key, meta_value FROM wp_trunk_sitemeta WHERE meta_key IN ('site_name', 'siteurl', 'active_sitewide_plugins', '_site_transient_timeout_theme_roots', '_site_transient_theme_roots', 'site_admins', 'can_compress_scripts', 'global_terms_enabled') AND site_id = 1";
		$expected = 'SELECT meta_key, meta_value FROM wp_trunk_sitemeta WHERE meta_key IN (&#039;site_name&#039;, &#039;siteurl&#039;, &#039;active_sitewide_plugins&#039;, &#039;_site_transient_timeout_theme_roots&#039;, &#039;_site_transient_theme_roots&#039;, &#039;site_admins&#039;, &#039;can_compress_scripts&#039;, &#039;global_terms_enabled&#039;) AND site_id = 1';
		$this->assertEquals( $expected, esc_xml( $html ) );
	}

	public function test_escapes_ampersands() {
		$source   = 'penn & teller & at&t';
		$expected = 'penn & teller & at&t';
		$this->assertEquals( $expected, esc_xml( $source ) );
	}

	public function test_escapes_greater_and_less_than() {
		$source   = 'this > that < that <randomhtml />';
		$expected = 'this > that < that <randomhtml />';
		$this->assertEquals( $expected, esc_xml( $source ) );
	}

	public function test_ignores_existing_entities() {
		$source   = '&#038; &#x00A3; &#x22; &amp;';
		$expected = '& £ &#x22; &';
		$this->assertEquals( $expected, esc_xml( $source ) );
	}
}
