<?php

define('FS_METHOD', 'direct');
define('FORCE_SSL_ADMIN', true);








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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', '@todo' );

/** MySQL database username */
define( 'DB_USER', '@todo' );

/** MySQL database password */
define( 'DB_PASSWORD', '@todo' );

/** MySQL hostname */
define( 'DB_HOST', '@todo' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '@ R?c8e;o-!JM69e-n*4t95^R+;G7w?-qGEU4bLq~fImMF>j6L*jX:~#B1P|<O||');
define('SECURE_AUTH_KEY',  'Ri-tf en5,zV>|$Er9D_},K46EJ=)Qv(P8JYYqt~o+Z|2e QN:G]r@IW;x6oD:-k');
define('LOGGED_IN_KEY',    '|C8BT+kg|I#Jl}.J2uArT<_R&ec.oGSq-P=6:KIB+[L5CB6!1c2I|Q8ky:Gtg{%v');
define('NONCE_KEY',        '@aU?rQJ{-Ll+o{Y_|szxBPGxWCS~8(#GXxfP;$j&].=-/8Cv+>b6@^0WVc**EC<G');
define('AUTH_SALT',        'b2&2!h#]]3b%-_8`PcW1/-U>{@<a+|+E{A@+??b.-:v@Eha-3wD~ZJ*R[/ q^{.=');
define('SECURE_AUTH_SALT', '-*3l;.eGQ>2*K(6CHy~A(`}w]$xdKZ37g#z0gtWx6]&}.g/5s!l:+ g^iHxX1c@ ');
define('LOGGED_IN_SALT',   '#<9l <qxDbvrJ+6DrPA3DWq@kUj_L(x+S|1.`FF_l]4alk][hJ& 1Rj{#N5vq{_ ');
define('NONCE_SALT',       '>ot~>6gj_O@7v);chDbh9ko$>QhVC P-z-X9LrV&9y}8XKR9PvU|(Ux[c|8&->b6');


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'zx53bi4g5g';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
