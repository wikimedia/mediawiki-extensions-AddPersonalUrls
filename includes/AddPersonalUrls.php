<?php

// phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName

/**
 * Code for the @ref Extensions-AddPersonalUrls.
 *
 * @file
 *
 * @ingroup Extensions
 * @ingroup Extensions-AddPersonalUrls
 *
 * @author [RV1971](https://www.mediawiki.org/wiki/User:RV1971)
 *
 */

use MediaWiki\Hook\BeforePageDisplayHook;
use MediaWiki\Hook\EditFormPreloadTextHook;
use MediaWiki\Hook\SkinTemplateNavigation__UniversalHook;

/**
 * Class implementing the @ref Extensions-AddPersonalUrls.
 *
 * @ingroup Extensions-AddPersonalUrls
 */
class AddPersonalUrls implements
	BeforePageDisplayHook,
	EditFormPreloadTextHook,
	SkinTemplateNavigation__UniversalHook
{

	/**
	 * BeforePageDisplay hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 *
	 * Add the [Resource Modules]
	 * (https://www.mediawiki.org/wiki/$wgResourceModules) to the page.
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 */
	public function onBeforePageDisplay( $out, $skin ): void {
		$out->addModules( 'ext.addPersonalUrls' );
	}

	/**
	 * EditFormPreloadText hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/EditFormPreloadText
	 *
	 * Preload text when creating new pages in the User namespace.
	 * See @ref $wgAddPersonalUrlsTable for an explanation how the text is
	 * composed.
	 *
	 * @param string &$text Text to prefill edit form with.
	 * @param Title $title
	 */
	public function onEditFormPreloadText( &$text, $title ) {
		/** Skip if there is already another preload text. */
		if ( $text || $title->getNamespace() !== NS_USER ) {
			return;
		}

		$msg1 = wfMessage( 'addpersonalurls-'
			. strtolower( $title->getSubpageText() ) . '-preload' );

		// If the page-specific message does not exist, do not preload anything.
		if ( !$msg1->exists() ) {
			return;
		}

		$msg2 = wfMessage( 'addpersonalurls-preload' );

		$text = '<!-- ' . trim( $msg1->text() . "\n\n" . $msg2->text() ) . ' -->';
	}

	/**
	 * SkinTemplateNavigation::Universal hook handler
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinTemplateNavigation::Universal
	 *
	 * This is the core of the extension which actually adds the URLs
	 * to the list of personal URLs.
	 *
	 * @param SkinTemplate $sktemplate
	 * @param array &$links
	 */
	public function onSkinTemplateNavigation__Universal( $sktemplate, &$links ): void {
		global $wgAddPersonalUrlsTable;

		$user = $sktemplate->getUser();
		$username = $user->getName();

		/** Consider logged-in users only. */
		if ( !$user->isNamed() ) {
			return;
		}

		$personal_urls = &$links['user-menu'];
		$title = $sktemplate->getTitle();
		$pageurl = $title->getLocalURL();

		/** Extract link to user page in order to keep it as first
		 *	item.
		 */
		$urls = [ 'userpage' => array_shift( $personal_urls ) ];

		foreach ( $wgAddPersonalUrlsTable as $id => $url ) {
			/** Ignore items were the target is unset. This allows
			 * to remove in `LocalSettings.php` items defined in
			 * extension.json.
			 */
			if ( $url === null ) {
				continue;
			}

			/** Replace $username with actual username. */
			$url = str_replace( '$username', $username, $url );

			/** Setup URL details, distinguishing between internal
			 *	and external links.
			 */
			if ( strpos( $url, '://' ) !== false ) {
				$href = $url;
				$class = 'external text';
				$active = false;
			} else {
				/** Split the URL at '?', if any. */
				$components = explode( '?', $url, 2 );
				$name = $components[0];
				$urlaction = $components[1] ?? null;

				$linkedTitle = Title::newFromText( $name );
				if ( !$linkedTitle ) {
					continue;
				}

				$exists = $linkedTitle->isSpecialPage() || $linkedTitle->exists();

				/** If a page does not exist and is not a special
				 *	page, open it for editing and format it as a
				 *	link to a new page.
				 */
				$href = $linkedTitle->getLocalURL( $exists ? $urlaction : 'action=edit' );
				$class = $exists ? null : 'new';
				$active = $href === $pageurl;
			}

			$text = $sktemplate->msg( $id )->text();
			$urls[$id] = [
				'text' => $text,
				'href' => $href,
				'active' => $active,
				'class' => $class,
			];
		}

		/** Prepend new URLs to existing ones. */
		$personal_urls = $urls + $personal_urls;
	}
}
