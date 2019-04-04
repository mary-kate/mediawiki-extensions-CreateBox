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
class CreateBox {

	public static function onParserFirstCallInit( $parser ) {
		$parser->setHook( 'createbox', [ __CLASS__, 'makeBox' ] );
	}

	public static function makeBox( $input, $argv, $parser ) {
		global $wgRequest, $wgScript;

		if ( $wgRequest->getVal( 'action' ) == 'create' ) {
			$prefix = self::getOption( $input, 'prefix' );
			$preload = self::getOption( $input, 'preload' );
			$editintro = self::getOption( $input, 'editintro' ); 
			$text = $parser->getTitle()->getPrefixedText();
			if ( $prefix && strpos( $text, $prefix ) === 0 ) {
				$text = substr( $text, strlen( $prefix ) );
			}
		} else {
			$prefix = self::getOption( $input, 'prefix' );
			$preload = self::getOption( $input, 'preload' );
			$editintro = self::getOption( $input, 'editintro' );
			$text = self::getOption( $input, 'default' );
		}

		$submit = htmlspecialchars( $wgScript );
		$width = self::getOption( $input, 'width', 0 );
		$align = self::getOption( $input, 'align', 'center' );
		$br = ( ( self::getOption( $input, 'break', 'no' ) == 'no' ) ? '' : '<br />' );
		$label = self::getOption( $input, 'buttonlabel', wfMessage( 'createbox-create' )->escaped() );
		$output = <<<ENDFORM
<div class="createbox" align="{$align}">
<form name="createbox" action="{$submit}" method="get" class="createboxForm">
<input type='hidden' name="action" value="create">
<input type="hidden" name="prefix" value="{$prefix}" />
<input type="hidden" name="preload" value="{$preload}" />
<input type="hidden" name="editintro" value="{$editintro}" />
<input class="createboxInput" name="title" type="text" value="{$text}" size="{$width}"/>{$br}
<input type='submit' name="create" class="createboxButton" value="{$label}"/>
</form></div>
ENDFORM;

		return $parser->replaceVariables( $output );
	}

	private static function getOption( $input, $name, $value = null ) {
		if ( preg_match( "/^\s*$name\s*=\s*(.*)/mi", $input, $matches ) ) {
			if ( is_int( $value ) ) {
				return intval( $matches[1] );
			} else {
				return htmlspecialchars( $matches[1] );
			}
		}
		return $value;
	}

}