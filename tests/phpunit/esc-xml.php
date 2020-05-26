<?php

/**
 * @group formatting
 */
class Tests_Formatting_EscXml extends WP_UnitTestCase {
	public function test_esc_xml_basics() {
		// Simple string.
		$source   = 'The quick brown fox.';
		$expected = $source;
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );

		// URL with &.
		$source   = 'http://localhost/trunk/wp-login.php?action=logout&_wpnonce=cd57d75985';
		$expected = 'http://localhost/trunk/wp-login.php?action=logout&amp;_wpnonce=cd57d75985';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );

		// SQL query.
		$source   = "SELECT meta_key, meta_value FROM wp_trunk_sitemeta WHERE meta_key IN ('site_name', 'siteurl', 'active_sitewide_plugins', '_site_transient_timeout_theme_roots', '_site_transient_theme_roots', 'site_admins', 'can_compress_scripts', 'global_terms_enabled') AND site_id = 1";
		$expected = 'SELECT meta_key, meta_value FROM wp_trunk_sitemeta WHERE meta_key IN (&#039;site_name&#039;, &#039;siteurl&#039;, &#039;active_sitewide_plugins&#039;, &#039;_site_transient_timeout_theme_roots&#039;, &#039;_site_transient_theme_roots&#039;, &#039;site_admins&#039;, &#039;can_compress_scripts&#039;, &#039;global_terms_enabled&#039;) AND site_id = 1';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );
	}

	public function test_escapes_ampersands() {
		$source   = 'penn & teller & at&t';
		$expected = 'penn &amp; teller &amp; at&amp;t';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );
	}

	public function test_escapes_greater_and_less_than() {
		$source   = 'this > that < that <randomhtml />';
		$expected = 'this &gt; that &lt; that &lt;randomhtml /&gt;';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );
	}

	public function test_escapes_html_named_entities() {
		$source   = 'this &amp; is a &hellip; followed by &rsaquo; and more and a &nonexistent; entity';
		$expected = 'this &amp; is a … followed by › and more and a &amp;nonexistent; entity';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );
	}

	public function test_ignores_existing_entities() {
		$source   = '&#038; &#x00A3; &#x22; &amp;';
		// note that _wp_specialchars() strips leading 0's from numeric character references.
		$expected = '&#038; &#xA3; &#x22; &amp;';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );
	}
}
