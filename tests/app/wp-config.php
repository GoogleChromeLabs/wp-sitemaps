<?php
/**
 * WordPress Behat tests config file.
 */

define( 'DB_NAME',     getenv( 'WP_TESTS_DB_NAME' ) ?: 'wordpress_app' );
define( 'DB_USER',     getenv( 'WP_TESTS_DB_USER' ) ?: 'root' );
define( 'DB_PASSWORD', getenv( 'WP_TESTS_DB_PASS' ) ?: '' );
define( 'DB_HOST',     getenv( 'WP_TESTS_DB_HOST' ) ?: '127.0.0.1' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

define( 'AUTH_KEY',         '!%^)gjJdM7b8f7Zw+=?LkW^#.s:Z!vPLovsK]+*o.~x!+v9DiA@hK3.Q%5bu11Kh' );
define( 'SECURE_AUTH_KEY',  ']a3Y+gZvW!mKGqo-#UBqoY+v^p_5-!VZEed4h!y6JpDr_vjfMLeL!T8Wz@yGFWDg' );
define( 'LOGGED_IN_KEY',    'J~dV}qR}%qmBQ.0o4ofxN!iPp~g7GP:?!z>]?EjyT~]FZ-J^_F-RLprGyMwN6ed}' );
define( 'NONCE_KEY',        'oLg-GU:*aUwM~x)s#pdV^zqWoQ*88psCgG?R0@XxP>+P4m3}z102-Txpy)YX)7iq' );
define( 'AUTH_SALT',        '?N}%tWf%Yp2.e3G}5d5q8@jJJ0pBAP4=b2?Y~~kMaK4pi=V)M,5]gx3ow,4^Pi5^' );
define( 'SECURE_AUTH_SALT', 'zCZ0#gGnGzmGXf]pY+b>p0ihny?!?snQ~wPFmE!gVZ]PpQr}Y^yUnM>s89]^kQ.s' );
define( 'LOGGED_IN_SALT',   'fdVd=NPxvsR@nDv,Z@ND*gM3ZiX%bp5qu]17Vz8*0XRFKMkW#eMYH_50.}K-mc+s' );
define( 'NONCE_SALT',       '2rufUiNJ.iBZ*4]Kg}BCof!xi]!ULh%D.)np^FXkzo^y*tbL,>.x7uBVV!v)s5g2' );

$table_prefix = 'wp_';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
