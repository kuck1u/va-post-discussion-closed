<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: VA Post Discussion Closed
Description: A comment, trackback, and the pingback are compulsorily closed by default.
Version: 0.0.1
Plugin URI: http://visualive.jp/download/wordpress/plugins/
Author: VisuAlive
Author URI: http://visualive.jp/
Text Domain: va_pdc
Domain Path: /languages
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

VisuAlive WordPress Plugin, Copyright (C) 2013 VisuAlive Inc

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! class_exists( 'VA_Post_Discussion_Closed' ) ) :
define( 'VA_PDC_VERSION', '0.0.1' );
define( 'VA_PDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'VA_PDC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
load_plugin_textdomain( 'va_pdc', false, VA_PDC_PLUGIN_PATH . '/languages' );

class VA_Post_Discussion_Closed {
	function __construct() {
		add_filter( 'comments_open', array( $this, '_va_pdc_comment_close' ), 9999, 2 );
		add_filter( 'pings_open', array( $this, '_va_pdc_comment_close' ), 9999, 2 );
		add_filter( 'trackback_url', array( $this, '_va_pdc_comment_close' ), 9999, 2 );
		add_filter( 'xmlrpc_methods', array( $this, '_va_pdc_xmlrpc_methods' ) );
		add_action( 'admin_notices', array( $this, '_va_pdc_show_admin_messages' ) );
		add_action( 'admin_menu', array( $this, '_va_pdc_remove_extra_meta_boxes' ) );
	}
	/**
	 * Disable Comments
	 * デフォルトで全固定ページのコメントをクローズ
	 * @link http://www.warna.info/archives/1199/
	 * @return boolean
	 */
	public function _va_pdc_comment_close( $open, $post_id ) {
		$post_id = (int)$post_id;
		$post = get_post( $post_id );

		if ( $post AND in_array( $post->post_type, array( 'page', 'attachment', 'link' ) ) ) {
			$open = false;
		}

		return $open;
	}
	/**
	 * XMLRPC経由でのPingbackを閉じる
	 * @link http://wpist.me/wp/wp-total-hacks/
	 * @return boolean
	 */
	public function _va_pdc_xmlrpc_methods( $methods ) {
		unset( $methods['pingback.ping'] );
		return $methods;
	}
	/**
	 * クローズ且つオープン不可である旨のメッセージを表示
	 * @link http://kachibito.net/wp-code/show-an-urgent-message-in-admin-panel
	 * @return string
	 */
	private function va_pdc_show_message( $message, $errormsg = false, $target_post_type = false ) {
		$post_type = get_post_type();

		if ( $target_post_type AND $post_type == $target_post_type ) {
			if ( $errormsg ) {
				echo '<div id="message" class="error">';
			} else {
				echo '<div id="message" class="updated fade">';
			}	echo "<p><strong>$message</strong></p></div>";
		}
	}
	/**
	 * クローズ且つオープン不可である旨のメッセージの内容
	 * @return callback
	 */
	public function _va_pdc_show_admin_messages() {
		$this->va_pdc_show_message( __("ディカッションにて「コメントの投稿を許可する」を有効にしても、固定ページではコメント投稿は有効になりません。", "va_pdc"), true, 'page' );
	}
	/**
	 * 固定ページの入稿画面から一部入力欄を削除
	 * @return callback
	 */
	public function _va_pdc_remove_extra_meta_boxes() {
		remove_meta_box( 'commentstatusdiv', 'page' , 'normal' );
		remove_meta_box( 'commentstatusdiv', 'page' , 'advanced' );
		remove_meta_box( 'commentstatusdiv', 'page' , 'side' );
		remove_meta_box( 'commentsdiv', 'page' , 'normal' );
		remove_meta_box( 'commentsdiv', 'page' , 'advanced' );
		remove_meta_box( 'commentsdiv', 'page' , 'side' );
	}
}
new VA_Post_Discussion_Closed;
endif; // VA_Post_Discussion_Closed
