<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'booking');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '`Y/}0Csbx4#t*|Dg.sS588_X(9&8#=.B*mVOa|HFue$uVffu5&|kD{Q.o7e$=W`3');
define('SECURE_AUTH_KEY',  '6;v&pYhzA%Jt$,uT0Ya$9c>j.]sW.[#M+9$Y. Bh5uu6l^>k3!#Dua%>_S%9Yn<7');
define('LOGGED_IN_KEY',    'MGP/z#WD!<w^/b`TY`)Pmrh(hSAgXnLOXry6]%#C-|3[7nkvjG)3(p_m,+yb !u]');
define('NONCE_KEY',        'k9o?P#.CloS4e|Mi9zS8Kr}wBTqUph2LNg@t{vl%4U!vS3zR]zr:obh|]oJqb1ip');
define('AUTH_SALT',        '3||R]Y|h5/3_;3}PV5j-#!T2?&wy9[|HX;PFg]?~Kx8*+n(MLW0_`x+|;]XI*}A/');
define('SECURE_AUTH_SALT', '#f,1BfeP+3)lNrizq>D%Aty$Ll|BZ&;NrsG-l#yGL_GWn_=@|n?<-K&^oPJOclb&');
define('LOGGED_IN_SALT',   '+)y-Ms[WCjM_|ECQ9z59A2m_{ ?Q[OSGJx5`|+2o5j8W 6jOd0.9uGP|9s{-da|@');
define('NONCE_SALT',       ',x#Q+?Z3X9[jBy2RUkhKwUo xar`r;RCnU]2^TYf@z%Bv:n0]xyA[a;X*6Sk;^iT');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
/* Debug Config */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
error_reporting( E_ALL );
ini_set( 'display_errors', 'yes' );

/* That's all, stop editing! Happy blogging. */
define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . '/booking');
define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] . '/booking');
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
