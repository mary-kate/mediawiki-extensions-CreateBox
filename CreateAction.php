<?php
/**
 * CreateBox v2 -- Specialized Inputbox for page creation
 *
 * Author: Ross McClure
 * https://www.mediawiki.org/wiki/User:Algorithm
 *
 * InputBox written by Erik Moeller <moeller@scireview.de>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * https://www.gnu.org/copyleft/gpl.html
 */
class CreateAction extends Action {

	public function getName() {
		return 'create';
	}

	public function show() {
		$out = $this->getOutput();
		$request = $this->getRequest();

		$prefix = $request->getVal( 'prefix' );
		$text = $request->getVal( 'title' );
		if ( $prefix ) {
		// if ( $prefix && strpos( $text, $prefix ) !== 0 ) {
			// ashley 4 April 2019: $prefix is already included in $text, so having it
			// included again results in...the namespace name being included twice.
			// Who knew?
			$title = Title::newFromText( $text );
			// $title = Title::newFromText( $prefix . $text );
			if ( is_null( $title ) ) {
				global $wgTitle;
				$wgTitle = SpecialPage::getTitleFor( 'Badtitle' );
				throw new ErrorPageError( 'badtitle', 'badtitletext' );
			} elseif ( $title->getArticleID() == 0 ) {
				$this->redirect( $title, 'edit' );
			} else {
				$this->redirect( $title, 'create' );
			}
		} elseif ( $request->getVal( 'section' ) == 'new' || $this->getTitle()->getID() == 0 ) {
			$this->redirect( $this->getTitle(), 'edit' );
		} else {
			$text = $this->getTitle()->getPrefixedText();
			$out->setPageTitle( $text );
			$out->setHTMLTitle( $this->msg( 'pagetitle', $text . ' - ' . $this->msg( 'createbox-create' ) ) );
			$out->addWikiMsg( 'createbox-exists' );
		}
	}

	private function redirect( $title, $action ) {
		$out = $this->getOutput();
		$request = $this->getRequest();

		$query = [
			'action' => $action,
			'prefix' => $request->getVal( 'prefix' ),
			'preload' => $request->getVal( 'preload' ),
			'editintro' => $request->getVal( 'editintro' ),
			'section' => $request->getVal( 'section' )
		];

		$out->setCdnMaxage( 1200 );
		$out->redirect( $title->getFullURL( $query ), '301' );
	}

}
