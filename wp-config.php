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
define( 'DB_NAME', 'demo' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'Y G$E=EGO)Od153Mv4ea5CG-8}KOi`j*BEWs~<R7@]h5Yfy;M(Hj6Ir|*{+H.}L]' );
define( 'SECURE_AUTH_KEY',  '(k}|WDR-lsa~Oe2O.b fi*rF jHUe-V{ r/Nmr_=1NYWiF*C3wjH)w>F&@`}+gRU' );
define( 'LOGGED_IN_KEY',    'L(<+G=:F821K?(k:!xtwu`jSjz.hcZ7uhIAF$m0dl6}|{emfv6QQL`/&5g#O0pxT' );
define( 'NONCE_KEY',        '&2mN$>`W9[n/*e&~|Ud-fzoI>i9/pZ>gX25Rwc%k~}Sc/.<c`vSoUt`Mb.Dh{jJL' );
define( 'AUTH_SALT',        '`r.t*(ga%cv1VPW<k*`8P](~F#[Q_0j.4~Exy*]:5En=B;S6w_}w|Iacvpx{jTZc' );
define( 'SECURE_AUTH_SALT', '4E@Eh@}(W)@5upA.qWh#||XZW*K=QYOQ^ox_Z8I,lhnpcNa5)lJANU !<%VDW4_i' );
define( 'LOGGED_IN_SALT',   'mzU:`gT/^8mBWtJcq%vv<{H2Yf|;2Q+MSUmk|vczcXyWHp]e/k ul2<> 3T?}/p2' );
define( 'NONCE_SALT',       'fbo,!hz4aSL-$(SVOZ$euZ0K2a{B cJ]VodiEn^0^D$ Yw<&!Z3Hel5..62(sp|^' );

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

/* Add any custom values between this line and the "stop editing" line. */

//define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false ); // Don't show errors on screen
@ini_set( 'display_errors', 0 ); // Hide errors from being displayed

// define('WP_HOME', 'http://192.168.137.1/site');
// define('WP_SITEURL', 'http://192.168.137.1/site');


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
