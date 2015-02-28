<?php
/**
 * Plugin Name: Pro Writer Lite
 * Plugin URI: http://pro-writer.co/
 * Description: Edit blog posts and pages on-screen, without entering the WordPress admin
 * Version: 1.036
 * Author: Greg Bowen
 * Author URI: http://prowriter.co/
 * License: GPLv2 or later
 */

add_action('wp_head','hook_css');


//add relevant stylesheets
function hook_css()
{
	wp_enqueue_media();
	wp_register_script('pro-writer-editor-js', plugin_dir_url( __FILE__ ) .'/js/pro-writer-editor-lite.js', array('jquery'));
	wp_enqueue_script('pro-writer-editor-js');
	wp_enqueue_style( 'pro-writer-editor', plugin_dir_url( __FILE__ ) .'/css/pro-writer-editor.css' );
}


add_filter('plugin_row_meta',  'Register_Plugins_Links', 10, 2);
//add support link to WordPress admin
function Register_Plugins_Links ($links, $file) {
               $base = plugin_basename(__FILE__);
               if ($file == $base) {
                       $links[] = '<a href="http://prowriter.co/pro-writer-lite-help/">' . __('Support') . '</a>';
                      
               }
               return $links;
       }



//Process post data - insert post

function remove_xss($headers) {

    if (!is_admin()) {
        $headers['X-XSS-Protection'] = 0;    
    }

    return $headers;     
}


function plloaded() {
	
	
	if ( current_user_can( 'edit_posts' ) ) {
		
		if (isset($_POST['ose-submit-trigger']) && !empty($_POST['ose-submit-trigger']) && $_POST['ose-submit-trigger']=='edit') {
			
			
			
			if ( ! isset( $_POST['ose_submit_field'] ) || ! wp_verify_nonce( $_POST['ose_submit_field'], 'ose_submit' )) {
				die( 'Sorry, your nonce did not verify.');
			exit;
			}
			update_option('ose_edit_mode',true);
			
		}
			
			add_filter('wp_headers', 'remove_xss'); // video issue
		

		global $_POST;
		
		if (isset($_POST['ose-content-post']) && !empty($_POST['ose-content-post'])) {
			if ( ! isset( $_POST['ose_field'] ) || ! wp_verify_nonce( $_POST['ose_field'], 'ose_action' )) {
			die( 'Sorry, your nonce did not verify.');
			exit;
			}
			
	
			$content=$_POST['ose-content-post'];
			
		
				
			$content = stripslashes($content);
				
			if (class_exists('Tidy')) {
   				  $tidy = new Tidy();
				  $options = array('indent' => true, 'show-body-only' =>true, 'quote-marks' => false, 'raw' => true, 'wrap'=>0);
				 
				  $tidy->parseString($content, $options,'utf8');
				  $tidy->cleanRepair();
				 
				  $content=$tidy;
				}
				
			$id=$_POST['ose-content-id'];
			
			$my_post = array(
				'ID'           => $id,
				'post_content' => $content
			);
			
			global $allowedposttags;
			
			$allowedposttags['div'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['h1'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['h2'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['span'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['h3'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['h4'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['p'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['a'] = array('align' => array (), 'class' => array (), 'id' => array (), 'dir' => array (), 'lang' => array(), 'style' => array (), 'xml:lang' => array() );
			$allowedposttags['iframe'] = array('src' => array (), 'href' => array (),'width' => array (),'height' => array (),'frameborder' => array (),'marginwidth' => array (),'marginheight' => array ());
			
			wp_update_post( $my_post );
		}
	}
}

	
// wrap page content in a custom container

function post_add_wysi_div( $content ) {
	
	if(get_option('ose_edit_mode')==true) {
		
		remove_all_shortcodes(); 
	
		global $post;
		$custom_content = '<div id="wysi-content">'.$content.'</div>';
		return $custom_content;
	}
	else {
		return $content;
	}
	
   
}


add_filter( 'the_content', 'post_add_wysi_div' );

add_action('wp_loaded', 'plloaded','999');


//add context sensative menu

function ose_js() {
	global $post;
	$postID = $post->ID;

	$nonce= wp_nonce_field('ose_action','ose_field');

$nonce_submit= wp_nonce_field('ose_submit','ose_submit_field');

$str="<div id=\"quote\"contenteditable=\"false\" class=\"ose_quote_box\">

<a href=\"javascript:void(0);\" onmousedown=\"SetToH1 ();\" class=\"ose_button ose_h1\" ></a>
<a href=\"javascript:void(0);\" onmousedown=\"SetToH2 ();\" class=\"ose_button ose_h2\" ></a>
<a href=\"javascript:void(0);\" onmousedown=\"makeItal ();\" class=\"ose_button ose_italic\" ></a>
<a href=\"javascript:void(0);\" onmousedown=\"makeBold ();\" class=\"ose_button ose_bold\" ></a>
<a href=\"javascript:void(0);\" onmousedown=\"makeQuote ();\" class=\"ose_button ose_quote\" ></a>

<a href=\"javascript:void(0);\" id=\"upload_image_button\" class=\"ose_button ose_image\" ></a>
<a href=\"javascript:void(0);\" onmousedown=\"addEmbed ();\" class=\"ose_button ose_embed\" ></a>
<a href=\"javascript:void(0);\" onmousedown=\"addLink ();\" class=\"ose_button ose_link\" ></a>
<a href=\"javascript:void(0);\" onmousedown=\"ClearBlock ();\" class=\"ose_button ose_clear\" ></a>

    </div>
	
	<form id='ose-form' action='' method='post' >".$nonce_submit."
		<input type='hidden' name='ose-submit-trigger' value='edit'/>
		<input id='edit-icon-button' style='color: transparent' class='ose-edit-button' type='image' src='".plugin_dir_url( __FILE__ ) ."images/editicon.png' border='0' alt='Submit' />
		</form>
		<form id='ose-form-submit' action='' method='post' onsubmit='return swapHTML();'>".$nonce."
		<input id='edit-icon-button' style='color: transparent' class='ose-save-button' type='image' src='".plugin_dir_url( __FILE__ ) ."images/saveicon.png' border='0' alt='Submit' />

		<input type='hidden' name='ose-content-post' id='content-replace' value=''/>
		<input type='hidden' name='ose-content-id' value='".$postID."' />
		
		</form>

<div style=\"clear: both\"></div>

";

if(get_option('ose_edit_mode')=='true') {
	
	echo('<script>
	jQuery(window).load(function($) {
    	ose_edit_html();
	});
</script>');

}


update_option('ose_edit_mode',false);
echo($str);
}

if ( ! is_admin() ) {
	add_action('wp_footer', 'ose_js', 100);
}




?>