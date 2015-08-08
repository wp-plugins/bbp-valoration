<?php 
/*
Plugin Name: bbp-valoration
Plugin URI: http://zeidan.info/bbp-valoration
Description: Adds thumbsup, visits and replies count to topics on bbpress, display results on a widget.
Version: 0.1.1
Author: Eric Zeidan
Author URI: http://zeidan.info/
License: GPL2
*/

/*  Copyright 2015 Eric Zeidan  (email : k2klettern@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('plugins_loaded', 'bbpv_text');

function bbpv_text() {
    load_plugin_textdomain('bbpv', false, basename(dirname(__FILE__)) . '/langs');
}

// Make sure we don't expose any info if called directly

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
//requirements check

if(!is_plugin_active('bbpress/bbpress.php')) {
        echo "<div class=\"error\"> <p>".__('This Plugin needs BBpress to work, pls. install it first and activate.','bbpv')."</p></div>";
        exit;
}


if ( !function_exists( 'add_action' ) ) {
	_e('Hi there!  I\'m just a plugin, not much I can do when called directly.','bbpv');
	exit;
}

require_once('functions.php');

