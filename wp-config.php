<?php
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          ';w/})1l]r2I p:}[quM9#r.b+v00#Uqyly^`}jc6N8NsHY,!y_%@s=%} jbF^M!&' );
define( 'SECURE_AUTH_KEY',   'e_MNqN7Sl%KjKLUO0v`8 ,=+4vXmg;S0xe>fMDX~Q!8aPfEjt/39=*bo}``3IbTm' );
define( 'LOGGED_IN_KEY',     'J>Qe;E/`3^ZRgqB{MSq~&AVi2v/CV*5nyeY2DqjQyt+OS/Qme&9b;,5Y3OHo3d(-' );
define( 'NONCE_KEY',         '7XzCir,i)`93{>[0eX|sCkptC(OpS:j^,:PY<t>NcdA~)M6hw,n}3hJh3}}l sr.' );
define( 'AUTH_SALT',         'P+II&v qoZ9!wWpklRgVbWr7JM6@=P$AMh`+.[rBdD(ewzuXoAz8{~xcWhmNq0G6' );
define( 'SECURE_AUTH_SALT',  'foBR+C25%-u:yvUcIL@$IF*39u[P82u)4f>o&sR#H1JtwMsGkx9gqyz11wE=$Jb_' );
define( 'LOGGED_IN_SALT',    '7!:A3OMT8PjWY:u`l aa(z07qx:t(;/4wNIF30B)8s)y,Zy:iAF)m`Tc]:L-C5vo' );
define( 'NONCE_SALT',        '9uC.FajnAU {<m-6_~jlInCr!KA.)UJL#?ilWI4tb=p?ED) v$&!{1{UysTm%%M$' );
define( 'WP_CACHE_KEY_SALT', 'k1NI0p-lK,iM$g gvjZ``4p3~(e!~-KT{-8~dYfjnJK?WzH9dlarRE5M5.ZfCQ1|' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
