<?php 
/*
 * Plugin Name: Sky Image Slider
 * Plugin URI:  http://bPlugins.com
 * Description: easy to use image slider with caption
 * Version:     1.2
 * Author:      bPlugins LLC
 * Author URI:  http://bPlugins.com
 * License:     GPLv3
 */
 
/*Some Set-up*/
define('NSR_PLUGIN_DIR', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' ); 
define('NRS_PLUGIN_VERSION','1.0' ); 


function nsr_frontend_styles_and_js() {

	wp_enqueue_script( 'nsr-nivo-js', NSR_PLUGIN_DIR. 'nivo/js/jquery.nivo.slider.js', array( 'jquery' ),'10.1',true );
	wp_enqueue_script( 'nsr-nivo-custom-js', NSR_PLUGIN_DIR. 'nivo/js/nivo-custom.js', array( 'jquery' ),'10.1',true );
	wp_enqueue_style( 'nsr-nivo-theme-css', NSR_PLUGIN_DIR. 'nivo/themes/default/default.css' );
	wp_enqueue_style( 'nsr-nivo-css', NSR_PLUGIN_DIR. 'nivo/nivo-slider.css' );	
}
add_action( 'wp_enqueue_scripts', 'nsr_frontend_styles_and_js' );
function nsr_plugin_scripts() {
    wp_enqueue_style( 'style-name', NSR_PLUGIN_DIR."style/stl.css" );

}
add_action( 'wp_enqueue_scripts', 'nsr_plugin_scripts' );

//Remove post update massage and link
function nsr_plugin_updated_messages( $messages ) {
 $messages['nivoslider_res'][1] = __('Updated ');
return $messages;
}
add_filter('post_updated_messages','nsr_plugin_updated_messages');




/*-------------------------------------------------------------------------------*/
/*   Register Custom Post Types
/*-------------------------------------------------------------------------------*/	   
add_action( 'init', 'nsr_create_post_type' );
function nsr_create_post_type() {
		register_post_type( 'nivoslider_res',
				array(
						'labels' => array(
								'name' => __( 'Image Slider'),
								'singular_name' => __( 'Image Slider' ),
								'add_new' => __( 'Add New Slider' ),
								'add_new_item' => __( 'Add New Slider' ),
								'edit_item' => __( 'Edit' ),
								'new_item' => __( 'New Slider' ),
								'view_item' => __( 'View Slider' ),
								'search_items'       => __( 'Search Slider'),
								'not_found' => __( 'Sorry, we couldn\'t find the Slider you are looking for.' )
						),
				'public' => false,
				'show_ui' => true, 									
				'publicly_queryable' => true,
				'exclude_from_search' => true,
				'menu_position' => 14,
				'menu_icon' =>NSR_PLUGIN_DIR."img/icn.png",
				'has_archive' => false,
				'hierarchical' => false,
				'capability_type' => 'post',
				'rewrite' => array( 'slug' => 'nivoslider_res' ),
				'supports' => array( 'title' )
				)
		);
}	
			
/*-------------------------------------------------------------------------------*/
/*  Metabox
/*-------------------------------------------------------------------------------*/			
include_once('metabox/metaboxes.php');

/*-------------------------------------------------------------------------------*/
/* Lets register our shortcode
/*-------------------------------------------------------------------------------*/ 
function nsr_cpt_content_func($atts){
	extract( shortcode_atts( array(

		'id' => null,

	), $atts ) ); 
?>
<?php ob_start();?>

	<div class="theme-default">
		<div id="slider<?php echo $id; ?>" class="nivoSlider">
		<?php $img=get_post_meta($id,'nsr_meta_select_images', true) ; 
			foreach( $img as $slide ) {
				$slide['images'];  $img_url = wp_get_attachment_url( $slide['images'] );
				if(get_post_meta($id,'nsr_slides_caption',true)=="on"){$single_s='<img src="'.$img_url.'" alt="" title="'.$slide['ttl'].'" />';}else{$single_s='<img src="'.$img_url.'" alt=""/>';}
				echo $single_s;
			}

			?>
		</div>
	<script type="text/javascript">
	jQuery(window).load(function() {
    jQuery('#slider<?php echo $id; ?>').nivoSlider({
        <?php if(!empty(get_post_meta($id,'nsr_effects',true))){echo "effect:'".get_post_meta($id,'nsr_effects',true)."',"; }?> 
		<?php if(!empty(get_post_meta($id,'nsr_animation_speed',true))){echo "animSpeed:".get_post_meta($id,'nsr_animation_speed',true).","; }?>
		<?php if(!empty(get_post_meta($id,'nsr_slider_delay',true))){echo "pauseTime:".(get_post_meta($id,'nsr_slider_delay',true)*1000).","; }?>
		
		<?php if(!empty(get_post_meta($id,'nsr_pagination',true))){if(get_post_meta($id,'nsr_pagination',true)=="on"){$directionNav="true";}else{$directionNav="false";} echo "directionNav:".$directionNav.","; }?>

		<?php if(!empty(get_post_meta($id,'nsr_bullets',true))){if(get_post_meta($id,'nsr_bullets',true)=="on"){$controlNav="true";}else{$controlNav="false";} echo "controlNav:".$controlNav.","; }?>
		
		<?php if(!empty(get_post_meta($id,'nsr_hover_pause',true))){if(get_post_meta($id,'nsr_hover_pause',true)=="on"){$pauseOnHover="true";}else{$pauseOnHover="false";} echo "pauseOnHover:".$pauseOnHover.","; }?>
		
		<?php if(!empty(get_post_meta($id,'nsr_start_with',true))){echo "randomStart:".get_post_meta($id,'nsr_start_with',true).","; }?>

    });
});
	</script>		
	</div>
	<?php $output = ob_get_contents();
		ob_end_clean(); 
		return $output; ?>
<?php
}
add_shortcode('sky_slider','nsr_cpt_content_func');

/*-------------------------------------------------------------------------------*/
// Developer page
/*-------------------------------------------------------------------------------*/
add_action('admin_menu', 'nsr_custom_submenu_page');
function nsr_custom_submenu_page() {
	add_submenu_page( 'edit.php?post_type=nivoslider_res', 'Developer', 'Developer', 'manage_options', 'nsr_developer', 'nsr_submenu_page_callback' );
}

function nsr_submenu_page_callback() {
	
	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>Developer</h2>
		<h2>Md Abu hayat polash</h2>
		<h4>Professional Web Developer (Freelancer)</h4>
		<h5>Web : <a href="http://abuhayatpolash.com">www.abuhayatpolash.com</h5></a>
		<h5>Hire Me : <a href="http://fiverr.com/abuhayat">www.fiverr.com/abuhayat</h5></a>
		Email: <a href="mailto:abuhayat.du@gmail.com">abuhayat.du@gmail.com </a>
		<h5>Skype: ah_polash</h5> 
		<br />
		
		';
	echo '</div>';

}
/*-------------------------------------------------------------------------------*/
// How to page
/*-------------------------------------------------------------------------------*/
add_action('admin_menu', 'nsr_how_to_page');
function nsr_how_to_page() {
	add_submenu_page( 'edit.php?post_type=nivoslider_res', 'How To Use', 'How To Use', 'manage_options', 'nsr_howto', 'nsr_howto_page_callback' );
}

function nsr_howto_page_callback() {
	
	echo'<div class="wrap">
			<h2>How To Use The Plugin </h2>
			<div class="card pressthis" style="float:left; max-width:960px;!importent">
				<h3>Tutorial</h3>
				<p>Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon Comming soon </p>

			</div>
		</div>';
}
/*-------------------------------------------------------------------------------*/
// Pro Version demo page
/*-------------------------------------------------------------------------------*/
add_action('admin_menu', 'nsr_pro_page');
function nsr_pro_page() {
	add_submenu_page( 'edit.php?post_type=nivoslider_res', 'Pro Version Demo', 'Pro Version Demo', 'manage_options', 'nsr_pro', 'nsr_pro_page_callback' );
}

function nsr_pro_page_callback() {
	
	echo'<div class="wrap">
			<h2>Pro Version Demo.</h2>
			<div class="card pressthis" style="float:left; max-width:960px;!importent">
				<h3>Comming soon</h3>


			</div>
		</div>';
}

// ONLY MOVIE CUSTOM TYPE POSTS
add_filter('manage_nivoslider_res_posts_columns', 'ST4_columns_head_only_nivoslider_res', 10);
add_action('manage_nivoslider_res_posts_custom_column', 'ST4_columns_content_only_nivoslider_res', 10, 2);
 
// CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
function ST4_columns_head_only_nivoslider_res($defaults) {
    $defaults['directors_name'] = 'ShortCode';
    return $defaults;
}
function ST4_columns_content_only_nivoslider_res($column_name, $post_ID) {
    if ($column_name == 'directors_name') {
        // show content of 'directors_name' column
		echo '<input onClick="this.select();" value="[sky_slider id='.$post_ID .']" >';
    }
}

// Footer Review Request 

	add_filter( 'admin_footer_text','nsr_admin_footer');	 
	function nsr_admin_footer( $text ) {
		if ( 'nivoslider_res' == get_post_type() ) {
			$url = 'https://wordpress.org/support/plugin/sky-image-slider/reviews/?filter=5#new-post';
			$text = sprintf( __( 'If you like <strong>Sky Image Slider</strong> please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'h5ap-domain' ), $url );
		}

		return $text;
	}


add_action('edit_form_after_title','nsr_shortcode_area');
function nsr_shortcode_area(){
global $post;   
if($post->post_type=='nivoslider_res'){
?>  
<div>
    <label style="cursor: pointer;font-size: 13px; font-style: italic;" for="nsr_shortcode">Copy this shortcode and paste it into your post, page, or text widget content:</label>
    <span style="display: block; margin: 5px 0; background:#1e8cbe; ">
        <input type="text" id="nsr_shortcode" style="font-size: 12px; border: none; box-shadow: none;padding: 4px 8px; width:100%; background:transparent; color:white;"  onfocus="this.select();" readonly="readonly"  value="[sky_slider id=<?php echo $post->ID; ?>]" /> 
        
    </span>
</div>
 <?php   
}}