<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'programozocms' );

/** Database username */
define( 'DB_USER', 'programozocms' );

/** Database password */
define( 'DB_PASSWORD', '2764cms,314' );

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
define( 'AUTH_KEY',         ';aKQ;W->:OG9;A+%m&(m,.di`~1vD<<(#])?R, `0{&1aH]z80m,Hd:K?:1^tPVs' );
define( 'SECURE_AUTH_KEY',  'Md15eO0YwLNY-+qA :Ccg>|mGFB>B-=@$.`q)m9<.*aj6?a!l)ZV$A&5P6?<Jw<j' );
define( 'LOGGED_IN_KEY',    'ijo-)e@0`PmvH+Gql{W;FS3~B5mr0~Q8`W [02Q&iP3F<N:aIc~ZmtNchdd^FJLz' );
define( 'NONCE_KEY',        'TBa}HMI7G[-]Gw+<6QxPR!we2;>Ruk=gix_]>_<v#92k;1v,@h`HJ;X@NV*buLl<' );
define( 'AUTH_SALT',        'J~UH:)#T)-bQ,;!hojc*8=[zNc[~h=:HVw&!;tF_(c=n<W?Pl(.=VY{^7@1c6$dn' );
define( 'SECURE_AUTH_SALT', 'kPXzx3`25zkO|$3CBOmaHh|y$)]ZWNDceh?fj@lS!WD v{:_LDcnL9m?ZzM$3T7z' );
define( 'LOGGED_IN_SALT',   '*2L7l8f-U(xBsBV#!F|7D7 eHIxBQ#P`+_)GX)tP:;-m-eK,6rN,LW509!Qjh(U2' );
define( 'NONCE_SALT',       'S,E/}sq2!P&RkO$%3<Ko^7Bdavugr&^iz/>v3w&rk0$s0k:;%3[&}.NlbL)7B%!k' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
