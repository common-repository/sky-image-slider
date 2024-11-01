<?php
/**
 * Add a custom Meta Box
 */
 
 include_once('functions/ewic-functions.php');

add_action( "admin_head", 'nsr_admin_head_script' );
add_action( 'admin_enqueue_scripts', 'nsr_load_script', 10, 1 );

function nsr_load_script() {
	if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
		if ( get_post_type( get_the_ID() ) == 'nivoslider_res' ) {
			wp_enqueue_media();
			wp_enqueue_script( 'ewic-ibutton-js', plugins_url( 'js/jquery/jquery.ibutton.js' , __FILE__ ) );
			wp_enqueue_style( 'ewic-ibutton-css', plugins_url( 'css/ibutton.css' , __FILE__ ), false, NRS_PLUGIN_VERSION );
			wp_enqueue_style( 'ewic-metacss', plugins_url( 'css/metabox.css' , __FILE__ ), false, '' );
			wp_enqueue_script( 'ewic-metascript', plugins_url( 'js/metabox/metabox.js' , __FILE__ ) );
			wp_enqueue_style( 'ewic-sldr' );		
			wp_enqueue_style( 'ewic-introcss' );	
			//wp_enqueue_style( 'ewic-bootstrap-css' );
			wp_enqueue_script( 'jquery-ui-slider' );
			wp_enqueue_script( 'ewic-introjs' );
			//wp_enqueue_script( 'ewic-bootstrap-js' );
        	wp_enqueue_script('jquery-effects-highlight');
			
					
			}
		}
}

function nsr_admin_head_script () {
	if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) || strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
		if ( get_post_type( get_the_ID() ) == 'nivoslider_res' ) {
			?>
            
			<style type="text/css" media="screen">
				a:focus {box-shadow: none !important; }
				#minor-publishing { display: none !important; }
				.media-toolbar-secondary .spinner { float: left; margin-right: 5px; }
				@media only screen and (min-width: 1150px) {	
		    		#side-sortables.fixed { position: fixed; top: 55px; right: 20px; width: 280px; }
				}	
            </style>
			
			<script type="text/javascript">
			/* Javascript/jQuery Code Here */
			jQuery(document).ready(function($) {
				jQuery('.images_list').sortable({
					opacity: 0.6,
					revert: true,
					placeholder: 'ui-sortable-placeholder',
					cursor: 'move',
					handle: '.ewic-shorters',
         			start: function(e, ui ){
             			ui.placeholder.width(ui.helper.outerWidth()-30);
         				},

					});
					
		    	var ewicrevPosition = $('#side-sortables').offset();
		    	$(window).scroll(function(){
			    if($(window).scrollTop() > ewicrevPosition.top)
			    	{
					$('#side-sortables').addClass('fixed');
			    		} 
			    	else 
			    		{
						$('#side-sortables').removeClass('fixed');
			    		}    
		    		});	
					
				});
                
             </script>  
                    
              <?php
              }
		}
} 
 
 
function nsr_add_meta_box( $meta_box )
{
    if ( !is_array( $meta_box ) ) return false;
    
    // Create a callback function
    $callback =  function() use ($meta_box) {
    	global $post;
    	return nsr_create_meta_box( $post, $meta_box);
	}; 

    add_meta_box( $meta_box['id'], $meta_box['title'], $callback, $meta_box['page'], $meta_box['context'], $meta_box['priority'], $meta_box );
}

/**
 * Create content for a custom Meta Box
 *
 * @param array $meta_box Meta box input data
 */
function nsr_create_meta_box( $post, $meta_box )
{
	
    if ( !is_array( $meta_box ) ) return false;
    
    if ( isset( $meta_box['description'] ) && $meta_box['description'] != '' ){
    	echo '<p>'. $meta_box['description'] .'</p>';
    }
    
	wp_nonce_field( basename( __FILE__ ), 'nsr_meta_box_nonce' );
	echo '<table class="form-table ewic-metabox-table">';
 
	foreach ( $meta_box['fields'] as $field ){
		// Get current post meta data
		$meta = get_post_meta( $post->ID, $field['id'], true );
		if ( isset( $field['isfull'] ) && $field['isfull'] == 'yes' ) {
			$isfull = '';
		} else {
			$isfull = '<th><label for="'. $field['id'] .'"><strong>'. $field['name'] .'<br></strong><span>'. $field['desc'] .'</span></label></th>';	
		}
		echo '<tr class="'. $field['id'] .'">'.$isfull.'';
		
		switch( $field['type'] ){	
		
			case 'select':
				echo'<td><select style="width:300px;" name="nsr_meta['. $field['id'] .']" id="'. $field['id'] .'">';
				foreach ( $field['options'] as $key => $option ){
					echo '<option value="' . $option . '"';
					if ( $meta ){ 
						if ( $meta == $option ) echo ' selected="selected"'; 
					} else {
						
					}
					echo'>'. $option .'</option>';
				}
				echo'</select></td>';
				break;
		
			case 'imagepicker':
			
				echo '<td>
				<div style="width:100%;"><span style="display:inline-block;" id="intro1" class="nsr_add_images">Add Images</span><span class="view-switch" style="float:right;" id="ewic-thumb-view"><a title="List Mode" id="ewiclist" class="view-list" href="#"></a><a title="Grid Mode" id="ewicgrid" class="view-grid" href="#"></a></span></div>
				<div id="nsr_images_container">
				<ul data-nonce="'.wp_create_nonce( 'ewic-remove' ).'" data-postid="'.$post->ID.'" class="images_list ui-sortable">';
				

				if ( is_array( $meta ) ) {
					foreach( $meta as $img_id ) {
						$img_data = get_post( $img_id['images'] );
						$img_url = wp_get_attachment_thumb_url( $img_id['images'] );
						
						echo '
						<li class="ewicthumbhandler" data-attachment_id="'.$img_id['images'].'">
							<input type="hidden" name="nsr_meta[nsr_meta_select_images]['.$img_id['images'].'][images]" value="'.$img_id['images'].'" />
							<div class="ewic-shorters">
							<img src="'.$img_url.'" />
							<span class="ewic-del-images"></span>
							<label for="title-for-'.$img_id['images'].'">Caption </label>
							<input id="title-for-'.$img_id['images'].'" class="images-title" type="text" name="nsr_meta[nsr_meta_select_images]['.$img_id['images'].'][ttl]" value="'.$img_id['ttl'].'"/></div>
						</li>';			
						}
				} else {echo '<p class="noimgs">No images selected... </p>';}
				
				echo '<input type="hidden" id="image_list_mode" name="nsr_meta[nsr_meta_list_mode]" value="'.get_post_meta( $post->ID, 'nsr_meta_list_mode', true ).'" />';

				echo '</ul></div></td>';
				
				if ( get_post_meta( $post->ID, 'nsr_meta_list_mode', true ) ) {
				
	?>	
    
				  <script type="text/javascript">
				  /*<![CDATA[*/
				  
				 jQuery(document).ready(function($) {
					 
					 jQuery('#<?php echo get_post_meta( $post->ID, 'nsr_meta_list_mode', true );?>').trigger('click');
					 
				  });				

				  /*]]>*/
                  </script> <?php
				  
				}
				
				break;	
	
	
			case 'slider': 
			echo '<td>';
	?>	
    
				  <script type="text/javascript">
				  /*<![CDATA[*/
				  
				 jQuery(document).ready(function($) { 
				  
/* Slider init */
		jQuery(function() {
	
        jQuery( '#<?php echo $field['id']; ?>_slider' ).slider({
            range: 'min',
            min: <?php echo $field['min']; ?>,
            max: <?php echo $field['max']; ?>,
			<?php if ( $field['usestep'] == '1' ) { ?>
			step: <?php echo $field['step']; ?>,
			<?php } ?>
            value: '<?php if ( $meta != "") { echo $meta; } else { echo $field['std']; } ?>',
            slide: function( event, ui ) {
                jQuery( "#<?php echo $field['id']; ?>" ).val( ui.value );
            	}
        	});
		});
				  
				  });				

				  /*]]>*/
                  </script>   
    
    <div class="nsr_metaslider"><div id="<?php echo $field['id']; ?>_slider" ></div><input style="margin-left:10px; margin-right:5px !important; width:40px !important;" name="nsr_meta[<?php echo $field['id']; ?>]" id="<?php echo $field['id']; ?>" type="text" value="<?php if ( $meta != "") { echo $meta; } else { echo $field['std']; } ?>" /><?php echo $field['pixopr']; ?></div> 
  
                <?php
			

				echo '</td>';
			    break;
					
				
			case 'radio':
				echo '<td>';
				
				if ( nsr_check_browser_version_admin( get_the_ID() ) != 'ie8' ) {
					foreach ( $field['options'] as $key => $option ){
						echo '<input id="'. $key .'" type="radio" name="nsr_meta['. $field['id'] .']" value="'. $key .'" class="css-checkbox"';
						if ( $meta ){
							if ( $meta == $key ) echo ' checked="checked"'; 
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /><label for="'. $key .'" class="css-label">'. $option .'</label> ';
								}
							}
							
				else {
					foreach ( $field['options'] as $key => $option ){
						echo '<label class="radio-label"><input type="radio" name="nsr_meta['. $field['id'] .']" value="'. $key .'" class="radio"';
						if ( $meta ){
							if ( $meta == $key ) echo ' checked="checked"';
							} else {
								if ( $field['std'] == $key ) echo ' checked="checked"';
								}
								echo ' /> '. $option .'</label> ';
								}
							}							
												
				echo '</td>';
				
				break;
				
				
			case 'checkbox':
			    echo '<td>';
			    $val = '';
                if ( $meta ) {
                    if ( $meta == 'on' ) $val = ' checked="checked"';
                } else {
                    if ( $field['std'] == 'on' ) $val = ' checked="checked"';
                }

                echo '<input type="hidden" name="nsr_meta['. $field['id'] .']" value="off" />
                <input class="ewicswitch" type="checkbox" id="'. $field['id'] .'" name="nsr_meta['. $field['id'] .']" value="on"'. $val .' /> ';
			    echo '</td>';
			    break;	
				
			case 'customsize':
			
			    echo '<td>';
				
				if ( is_array( $meta ) ) {
					$sw = $meta['width'];
					$sh = $meta['height'];
					} else {
						$sw = $field['stdw'];
						$sh = $field['stdh'];
					}
				
                echo '<div id="cscontw"><strong>Width</strong> <input style="margin-right:5px !important; margin-left:3px; width:43px !important; float:none !important;" name="nsr_meta[nsr_meta_thumbsizelt][width]" id="'. $field['id'] .'_w" type="text" value="'.$sw.'" />  ' .$field['pixopr']. '</div>

<span id="cssep" style="border-right:solid 1px #CCC;margin-left:9px; margin-right:10px !important; "></span>
 	<div id="csconth"><strong>Height</strong> <input style="margin-left:3px; margin-right:5px !important; width:43px !important; float:none !important;" name="nsr_meta[nsr_meta_thumbsizelt][height]" id="'. $field['id'] .'_h" type="text" value="'.$sh.'" /> ' .$field['pixopr']. '';
				echo '</div>';
			    echo '</td>';
			    break;
				
	
	
/*-----------------------------------------------------------------------------------*/	
		}
		
		echo '</tr>';
	}
 
	echo '</table>';
}

/*-----------------------------------------------------------------------------------*/
/*	Register related Scripts and Styles
/*-----------------------------------------------------------------------------------*/

	// SELECT MEDIA METABOX
add_action( 'add_meta_boxes', 'nsr_metabox_work' );
function nsr_metabox_work(){

// Image Picker
	    $meta_box = array(
		'id' => 'nsr_meta_images',
		'title' =>  __( 'Select/Upload Images', 'image-slider-widget' ),
		'description' => __( '<span class="ewic-introjs"><span class="ewic-intro-help"></span><a href="javascript:void(0);" onclick="startIntro();">Click Here to learn How to Create Slider</a></span><br /><br />Click <strong><i>Add Images</i></strong> button below and select an images that you want to show in your widget area.<br />Press <strong>Ctrl + click on each images</strong> to select multiple images.', 'image-slider-widget' ),
		/*'description' => __( '<span class="ewic-introjs"><span class="ewic-intro-help"></span><a href="javascript:void(0);" onclick="startIntro();">Click Here to learn How to Create Slider</a></span><br /><br /><div class="ewicinfobox">Upgrade to PRO VERSION and you will get awesome slider options like <a href="http://demo.ghozylab.com/content/ewicpro.html?utm_source=procp&utm_medium=settingspage&utm_campaign=gotodemoprocp" target="_blank">this</a>. You will able to create elegant slider like the following example:<ul><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-thumbnails-at-the-bottom/" target="_blank">Image Slider with Thumbnails at The Bottom
</a></li><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-bullet-navigation/" target="_blank">Image Slider with Bullet Navigation
</a></li><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-thumbnails-on-left/" target="_blank">Image Slider with Thumbnails on Left
</a></li><li><a href="http://demo.ghozylab.com/plugins/easy-image-slider-plugin/image-slider-with-thumbnails-on-right/" target="_blank">Image Slider with Thumbnails on Right</a></li></ul></div><br /><br />Click <strong><i>Add Images</i></strong> button below and select an images that you want to show in your widget area.<br />Press <strong>Ctrl + click on each images</strong> to select multiple images.', 'image-slider-widget' ),*/
		'page' => 'nivoslider_res',
		'context' => 'normal',
		'priority' => 'default',
		'fields' => array(
		
			array(
		
					'name' => '',
					'isfull' => 'yes',
					'desc' => '',
					'id' => 'nsr_meta_select_images',
					'type' => 'imagepicker',
					'std' => ''
					
				 ),

			)
	);
    nsr_add_meta_box( $meta_box );
	

// Config 	
	    $meta_box = array(
		'id' => 'nsr_meta_settings',
		'title' =>  __( 'Settings', 'image-slider-widget' ),
		'description' => 'You can change the look of your image slider to fit your needs here.<br /><div class="ewicinfobox">Upgrade to PRO VERSION and you will get awesome slider options like <a href="#" target="_blank">this screenshot</a></div>',
		'page' => 'nivoslider_res',
		'context' => 'normal',
		'priority' => 'default',
		'fields' => array(

				 
			array(
					'name' => __( 'Slider Effect / Easing', 'image-slider-widget' ),
					'isfull' => 'no',
					'desc' => __( 'Choose an entrance animation or effect and pass it to the slider. Default : slideInLeft', 'image-slider-widget' ),
					'id' => 'nsr_effects',
					'type' => 'select',
					'options' => array( "slideInLeft","slideInRight", "sliceDown", "sliceDownLeft", "sliceUp", "sliceUpLeft", "sliceUpDown", "sliceUpDownLeft", "fold", "fade","boxRandom", "boxRain", "boxRainReverse", "boxRainGrow", "boxRainGrowReverse","random"),
					'std' => 'slideInLeft'
				 ),
				 
			array(
					'name' => __( 'Slider Delay', 'image-slider-widget' ),
					'desc' => __( 'How long each slide will show  (in sec). Default : 3 sec', 'image-slider-widget' ),
					'id' => 'nsr_slider_delay',
					'type' => 'slider',
					'std' => '3',
					'max' => '240',
					'min' => '1',
					'step' => '1',
					'usestep' => '1',
					'pixopr' => 'Seconds',
					),
			array(
					'name' => __( 'Animation speed', 'image-slider-widget' ),
					'desc' => __( 'Slide transition speed (in Mili Second ). Default is 500 Mili second', 'image-slider-widget' ),
					'id' => 'nsr_animation_speed',
					'type' => 'slider',
					'std' => '500',
					'max' => '5000',
					'min' => '10',
					'step' => '10',
					'usestep' => '1',
					'pixopr' => 'Milisecond',
					),	
			array(
					'name' => __( 'Control button', 'image-slider-widget' ),
					'desc' => __( 'Turn on or off the Next & Prev navigation', 'image-slider-widget' ),
					'id' => 'nsr_pagination',
					'type' => 'checkbox',
					'std' => 'on'
					),						
					
			array(
					'name' => __( 'Bottom Nvigation bullets', 'image-slider-widget' ),
					'desc' => __( 'Turn on / off the Bottom Nvigation bullets', 'image-slider-widget' ),
					'id' => 'nsr_bullets',
					'type' => 'checkbox',
					'std' => 'on'
					),	
			array(
					'name' => __( 'Slide captions', 'image-slider-widget' ),
					'desc' => __( 'Turn On or Off caption of each slider. ', 'image-slider-widget' ),
					'id' => 'nsr_slides_caption',
					'type' => 'checkbox',
					'std' => 'on'
					),						
			array(
					'name' => __( 'Pause on Hover', 'image-slider-widget' ),
					'desc' => __( 'If on, its Stop the animation while hovering', 'image-slider-widget' ),
					'id' => 'nsr_hover_pause',
					'type' => 'checkbox',
					'std' => 'on'
					),						
					
					
			array(
					'name' => __( 'Slides Start with ', 'image-slider-widget' ),
					'desc' => __( 'From which slide slider start sliding ?', 'image-slider-widget' ),
					'id' => 'nsr_start_with',
					'type' => 'radio',
					'options' => array (	
										'false'=> 'First slide',
										'true'=> 'Random',	),	
					'std' => 'false'
					),		
			)
	);
    nsr_add_meta_box( $meta_box );
	
}

//-----------------------------------------------------------------------------------------------------------------

/**
 * Save custom Meta Box
 *
 * @param int $post_id The post ID
 */
function nsr_save_meta_box( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
	
	if ( !isset( $_POST['nsr_meta'] ) || !isset( $_POST['nsr_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['nsr_meta_box_nonce'], basename( __FILE__ ) ) )
		return;
	
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) ) return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	}
			
		// save data
		foreach( $_POST['nsr_meta'] as $key => $val ) {
			delete_post_meta( $post_id, $key );
			add_post_meta( $post_id, $key, $_POST['nsr_meta'][$key], true ); 
		}
}
add_action( 'save_post', 'nsr_save_meta_box' );




?>