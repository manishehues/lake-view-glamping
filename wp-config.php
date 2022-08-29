<?php
define( 'WP_CACHE', true );




/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'lakevir0_lvglive' );
/** Database username */
define( 'DB_USER', 'lakevir0_lvglive' );
/** Database password */
define( 'DB_PASSWORD', 'Shinda@11' );
/** Database hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'jQ5#TlqrDCik+KDD$.S`#||s>*|5N9@HytUYQi,$zoO&(8#M+AfSj28rn=Q?][r?' );
define( 'SECURE_AUTH_KEY',  'mQ~rbB[k8+u/9)K?LcFtvBIf^Wfm,BLR4;^8hAkdh5+)$gcdDOs7Az{;H+lH 4&H' );
define( 'LOGGED_IN_KEY',    'uE?>.$O~G Zh4cjk$Pk?f&)Q^,Mt<4j?](jb0j<hkP&o2vHu$!P-In5HY+Tt;dVe' );
define( 'NONCE_KEY',        '}Akrpo-,)_Cle2<Noy|8nvUz--^i5)-:Y8{#8$c2IS{uRW{^QW+kc}:WTlcHVcy^' );
define( 'AUTH_SALT',        '%e%a+h-5Q=_ r0|ogX&$F7]-N?SV6a+D4![xoQE5]zWv.D1M|h=o <1nZv^WQO??' );
define( 'SECURE_AUTH_SALT', 'Mr$Id8nF(ajk1A1S g.j#*)jr>iyT?k0;4ge89CX;pi6.JwHy`Kp:AL*|Y@|TZ1=' );
define( 'LOGGED_IN_SALT',   '+wR!z}vcBeNK0:{uv,%mjSXQ9){Zq+N7o`wj-pM|?#|:hhJF7w7{.4slj{AB0[Pk' );
define( 'NONCE_SALT',       '70{X)O]QN#nxje++s#S~;]0cwcd{dTx)<I~*Ed!wgvnI]?8QSp}og;AsfXTe/i6N' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'lake641_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );
define('ALLOW_UNFILTERED_UPLOADS', true);
/* Add any custom values between this line and the "stop editing" line. */
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';