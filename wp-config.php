<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'hirfati');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'root');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', '');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '6N$-Nx5Ef{n-FBp(F[s$d/sQtgw?n0(YMd<mPgf1ABz544~g#d_^0InT4oy?d|yF');
define('SECURE_AUTH_KEY',  '5V:M#]*|.4o/C*EpL-$n>]GsnbN7u*A!}`*n=ajvW?d>*@J`m$Sd:/}az<s7g;>|');
define('LOGGED_IN_KEY',    'DI@<UQ<`#.yu]h/q@GvY+^h;T#EOX(?6sc_^?P6qABAX4dMY]9N0q&@b46,pigP=');
define('NONCE_KEY',        '*#ofky>ZLHxAJN2f],;.kAfTPG+P ;UFTp-^_]{iQoD[i0.D(d1%w-Wm5hxC3Y<i');
define('AUTH_SALT',        'Nv|yAMq/p{g]PZRO2-GjisExWn|3iDjFoQ_O[K*#=Z879 ~*6P-{FSD-Yav9dffS');
define('SECURE_AUTH_SALT', 'omll`eZb_qk[H,QO.guN$(>%5,b?@V7VzZ}T!@~1/72q.!%GmfEa!vAO]+WKsGU3');
define('LOGGED_IN_SALT',   'l!/S1Lsj{.fkDrt+LU9h|taKEr[`Gvz@]:ET7ErfuP0tOp*)<:R[/};,K1xAU8Te');
define('NONCE_SALT',       '/r##gN{5XKfv (QU-.[v?uq)|fPnIl#RXWA%)s;a_)<CT4dO,PArW ~oN~9ik<S7');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');