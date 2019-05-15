<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'wp_admin' );

/** MySQL database password */
define( 'DB_PASSWORD', 'applepie' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '?$cTFfK:gyH:i/OX79>?DPY|X rx_o2?86w3&YV3s7kblAuIz-|`/.IgXPg`X?C,' );
define( 'SECURE_AUTH_KEY',  '|^*>n<Lu3?*oypNH0,VUng_Y0Ef3TvA)m;3@8os1,r|XC/Llhxbe9ej8+USF!2RN' );
define( 'LOGGED_IN_KEY',    'ZI>?WpLOh4g(VCd!%l}>v ]Q{,NZs7O@=fP8z>l&yMl^Z8QE`h!x%rI?n!0rj?!d' );
define( 'NONCE_KEY',        '2-aF|<0G*>#0fLNB,7QN1XOA^q56)l;4,3[1[@CpdCv!y9^w1&9i]?*J0W<1?We@' );
define( 'AUTH_SALT',        '85sx1]#mT^`O]{TUuS,2&X+yOUo<QdO;qwK`-,Eg!hj#[,[G, hR(<G<h+b=b0)x' );
define( 'SECURE_AUTH_SALT', '|i<Js!+`AFU7a5$hD99o)$3-|BF_X`Jj%2%1:Mh_kC3t]2!]9HeoDUu-RHKJ$M$B' );
define( 'LOGGED_IN_SALT',   'v{ -N4;,SYpLRviP|G.E$Nli323pI5u8mr7hLZ@LM7Q>8bT?Kcu|.;O!m^RbUIdU' );
define( 'NONCE_SALT',       'K}``3h?/iF;-=7_^g2?hh{;HWoGk^O HlKaivi}+Q5Z(hu;S+/Cyszbht@dF]fso' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
