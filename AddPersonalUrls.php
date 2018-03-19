<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die( 1 );
}

/**
 * @brief %AddPersonalUrls extension for MediaWiki.
 *
 * @defgroup Extensions-AddPersonalUrls AddPersonalUrls extension
 *
 * @ingroup Extensions
 *
 * This extension adds some items to the personal URLs (the links
 * which in the Vector skin are located near the top of the screen).
 *
 * To activate this extension, put the source files into
 * `$IP/extensions/AddPersonalUrls` and add the following into your
 * `LocalSettings.php` file:
 *
 * ~~~
 * require_once("$IP/extensions/AddPersonalUrls/AddPersonalUrls.php");
 * ~~~
 *
 * You can customize @ref $wgAddPersonalUrlsTable and the @ref
 * AddPersonalUrls.i18n.php "messages", and you can define your own
 * CSS definitions to format the URLs.
 *
 * @version 1.1.0
 *
 * @copyright [GPL-3.0+](https://gnu.org/licenses/gpl-3.0-standalone.html)
 *
 * @author [RV1971](http://www.mediawiki.org/wiki/User:RV1971)
 *
 * @sa [User documentation]
 * (http://www.mediawiki.org/wiki/Extension:AddPersonalUrls)
 *
 * @sa @ref AddPersonalUrls-images
 *
 * @sa [MediaWiki Manual](http://www.mediawiki.org/wiki/Manual:Contents):
 * - [Developing extensions]
 * (http://www.mediawiki.org/wiki/Manual:Developing_extensions)
 * - [Skins](http://www.mediawiki.org/wiki/Manual:Skins)
 * - [Hooks](http://www.mediawiki.org/wiki/Manual:Hooks)
 * - [Messages API](http://www.mediawiki.org/wiki/Manual:Messages_API)
 *
 * @sa [Semantic Versioning](http://semver.org)
 */

/**
 * @brief Setup for the @ref Extensions-AddPersonalUrls.
 *
 * @file
 *
 * @ingroup Extensions
 * @ingroup Extensions-AddPersonalUrls
 *
 * @author [RV1971](http://www.mediawiki.org/wiki/User:RV1971)
 *
 */

/**
 * @brief Table of URLs to add.
 *
 * The table is an array where each entry assigns an *ID* to a *URL*
 * as follows:
 *
 * - URLs are inserted after the link to the user's home page, in the
 * order in which they appear in the array.
 *
 * - The *ID* is the name of a system message used to display the
 * link. AddPersonalUrls is shipped with default messages (in the
 * languages de, en, fr and it) for the links in the default
 * $wgAddPersonalUrlsTable.
 *
 * - In the resulting HTML code, each link is wrapped into an \<li>
 * item having an id attribute with value `pt-`*ID*. This is a
 * feature provided by MediaWiki which allows to format personal URLs
 * individually via CSS.
 *
 * - The *URL* is either a page name (without brackets), optionally
 * followed by a '?' and a query string, or an external link. A *URL* is
 * recognized an external link iff it contains the string '://'. I'm
 * sure you'll find a better way, so please let me know your ideas.
 *
 * - The string `$username` will be replaced with the current
 * username. Note that in `LocalSettings.php`, you need to write the
 * *URL* within single quotes to avoid that PHP already interprets
 * `$username` while processing `LocalSettings.php`, in which case it
 * would evaluate to an empty string.
 *
 * - When the *URL* is the name of a a page that does not exist, it
 * opens the page for editing, and the content of the system messages
 * *ID*`-preload` and `addpersonalurls-preload` is
 * preloaded. AddPersonalUrls is shipped with default preload messages
 * explaining the purpose of each page. This will also work for any
 * *URL*s you add, provided that *ID* is equal to
 * `addpersonalurls-` *subpage_title_in_lowercase*.
 */
$wgAddPersonalUrlsTable = array(
	'addpersonalurls-userpages'
	=> 'Special:PrefixIndex?prefix=$username&namespace=2',
	'addpersonalurls-home' => 'User:$username/Home',
	'addpersonalurls-favorites' => 'User:$username/Favorites',
	'addpersonalurls-sandbox' => 'User:$username/Sandbox',
	'addpersonalurls-notes' => 'User:$username/Notes'
);

/**
 * @brief [About]
 * (http://www.mediawiki.org/wiki/$wgExtensionCredits) this extension.
 */
$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'AddPersonalUrls',
	'descriptionmsg' => 'addpersonalurls-desc',
	'version' => '1.1.0',
	'author' => '[http://www.mediawiki.org/wiki/User:RV1971 RV1971]',
	'url' => 'http://www.mediawiki.org/wiki/Extension:AddPersonalUrls'
);

/**
 * @brief [Autoloading]
 * (http://www.mediawiki.org/wiki/Manual:$wgAutoloadClasses)
 */
$wgAutoloadClasses['AddPersonalUrls'] =
	__DIR__ . '/AddPersonalUrls.body.php';

/**
 * @brief [Defer initialization]
 * (https://www.mediawiki.org/wiki/Manual:$wgExtensionFunctions)
 */
$wgExtensionFunctions[] = 'AddPersonalUrls::init';

/// [Localisation](https://www.mediawiki.org/wiki/Localisation_file_format).
$wgMessagesDirs['AddPersonalUrls'] = __DIR__ . '/i18n';

/**
 * @brief Old-style [Localisation]
 * (http://www.mediawiki.org/wiki/Localisation) file for MW 1.19 compatibility.
 */
$wgExtensionMessagesFiles['AddPersonalUrls'] =
	__DIR__ . '/AddPersonalUrls.i18n.php';

/// [Resource Modules](http://www.mediawiki.org/wiki/$wgResourceModules).
$wgResourceModules['ext.addPersonalUrls'] = array(
	'styles' => 'css/ext.addPersonalUrls.css',
	'localBasePath' => __DIR__,
	'remoteExtPath' => 'AddPersonalUrls'
);
