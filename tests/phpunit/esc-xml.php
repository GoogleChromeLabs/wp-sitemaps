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

	public function test_ignores_cdata_sections() {
		// basic CDATA Section containing chars that would otherwise be escaped if not in a CDATA Section
		// not to mention the CDATA Section markup itself :-)
		// $source contains embedded newlines to test that the regex that ignores CDATA Sections
		// correctly handles that case.
		$source   = "This is\na<![CDATA[test of the <emergency>]]>\nbroadcast system";
		$expected = "This is\na<![CDATA[test of the <emergency>]]>\nbroadcast system";
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );

		// string with chars that should be escaped as well as a CDATA Section that should be not be.
		$source   = 'This is &hellip; a <![CDATA[test of the <emergency>]]> broadcast <system />';
		$expected = 'This is … a <![CDATA[test of the <emergency>]]> broadcast &lt;system /&gt;';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );

		// Same as above, but with the CDATA Section at the start of the string.
		$source   = '<![CDATA[test of the <emergency>]]> This is &hellip; a broadcast <system />';
		$expected = '<![CDATA[test of the <emergency>]]> This is … a broadcast &lt;system /&gt;';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );

		// Same as above, but with the CDATA Section at the end of the string.
		$source   = 'This is &hellip; a broadcast <system /><![CDATA[test of the <emergency>]]> ';
		$expected = 'This is … a broadcast &lt;system /&gt;<![CDATA[test of the <emergency>]]> ';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );

		// multiple CDATA Sections.
		$source   = 'This is &hellip; a <![CDATA[test of the <emergency>]]> &broadcast; <![CDATA[<system />]]>';
		$expected = 'This is … a <![CDATA[test of the <emergency>]]> &amp;broadcast; <![CDATA[<system />]]>';
		$actual   = esc_xml( $source );
		$this->assertEquals( $expected, $actual );
	}

}
