<?php
/*-------------------------------------------------------------------------------*/
/*   Frontend Register JS & CSS
/*-------------------------------------------------------------------------------*/
function nsr_reg_script() {
	wp_register_style( 'ewic-sldr', plugins_url( 'css/slider.css' , dirname(__FILE__) ), false, NRS_PLUGIN_VERSION );
	wp_register_style( 'ewic-introcss', plugins_url( 'css/introjs.min.css' , dirname(__FILE__) ), false, NRS_PLUGIN_VERSION );
	wp_register_script( 'ewic-introjs', plugins_url( 'js/jquery/intro.min.js' , dirname(__FILE__) ), false );
	//wp_register_style( 'ewic-bootstrap-css', plugins_url( 'css/bootstrap/css/bootstrap.min.css' , dirname(__FILE__) ), false, NRS_PLUGIN_VERSION );
	//wp_register_script( 'ewic-bootstrap-js', plugins_url( 'js/bootstrap/bootstrap.min.js' , dirname(__FILE__) ) );	
		
}
add_action( 'admin_init', 'nsr_reg_script' );



/*-------------------------------------------------------------------------------*/
/*   CHECK BROWSER VERSION ( IE ONLY )
/*-------------------------------------------------------------------------------*/
function nsr_check_browser_version_admin( $sid ) {
	
	if ( is_admin() && get_post_type( $sid ) == 'nivoslider_res' ){

		preg_match( '/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches );
		if ( count( $matches )>1 ){
			$version = explode(".", $matches[1]);
			switch(true){
				case ( $version[0] <= '8' ):
				$msg = 'ie8';

			break; 
			  
				case ( $version[0] > '8' ):
		  		$msg = 'gah';
			  
			break; 			  

			  default:
			}
			return $msg;
		} else {
			$msg = 'notie';
			return $msg;
			}
	}
}



?>