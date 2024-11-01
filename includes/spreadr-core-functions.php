<?php
/**
 * Spreadr Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		Spreadr
 * @category 	Core
 * @package 	Spreadr/Functions
 * @version     0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function spreadr_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}







add_action('admin_menu', 'spreadr_dashboard');
function spreadr_dashboard() {
	global $submenu;
	
	if (get_option( 'spreadr_token') != "") {
		$spreadr_token =  get_option( 'spreadr_token');
		$spreadrDashboardUrl = SPREADR_APP_URL."verifyuser/".$spreadr_token;
		$submenu['woocommerce'][] = array(
		'<div id="spreadrDashboard">Spreadr</div>', 'manage_options', $spreadrDashboardUrl);

	}
	
	
	
}

add_action( 'admin_footer', 'spreadr_dashboard_blank' );    
function spreadr_dashboard_blank()
{
	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#spreadrDashboard').parent().attr('target','_blank');
			
		});
	</script>
	<?php
}


add_action( 'wp_ajax_spreadr_create_product', 'spreadr_create_product' );
add_action( 'wp_ajax_nopriv_spreadr_create_product', 'spreadr_create_product' );

function spreadr_create_product() {
	
	$product = sanitize_text_or_array_field_spreadr($_POST['product']);
	
	$title = $product['title'];
	$description = wp_kses_post($_POST['product']['body_html']);
	$feature_image = $product['images'][0];
	$images = $product['images'];

	$product_meta = $product['metafields'];
	$product_url = $product_meta[0]['value'];
	$tags = $product['tags'];
	$published = $product['published'];
	$price = $product['variants'][0]['price'];
	$compare_price = $product['variants'][0]['compare_at_price'];
	
	if($published == true){
		$published = 'publish';
	}else{
		$published = 'draft';
	}
	$post_id = wp_insert_post( array(
        'post_title' => $title,
        'post_content' => $description,
        'post_status' => $published,
        'post_type' => "product",
    ) );




    

   	$thumbnail_id = spreadr_image_import($feature_image['src'],$post_id);

   
    update_post_meta( $post_id, '_thumbnail_id', $thumbnail_id );

    if (count($images) > 1) {
    	 array_shift($images);
    	foreach ($images as $key => $image) {
    		$image_id = spreadr_image_import($image['src'],$post_id);;
			$product_images[] = $image_id;
    	}
    }


    if (!empty($product_images)) {
    	$gallery = implode(",",$product_images);
    	update_post_meta( $post_id, '_product_image_gallery', $gallery );
    }

   
    //update_post_meta( $post_id, 'junk', json_encode($product_meta) );
    //
    $producttype = 0;
    foreach ($product_meta as $metakey => $meta) {

    	update_post_meta( $post_id,$meta['key'], $meta['value'] );
    	if ($meta['key'] == 'spreadr_tags') {
    		
    		$spreadtags = explode(",", $meta['value']);
    		wp_set_object_terms($post_id, $spreadtags, 'product_tag');
    	} elseif ($meta['key'] == 'spreadr_category') {
    		
    		wp_set_object_terms($post_id, $meta['value'], 'product_cat');
    	}
		
		if ($meta['key'] == 'spreadr_product_button_type') {
    		
    		$producttype = $meta['value'];
    	}

    	$meta = array();
    }

    	
	
	if($producttype == 0){
		wp_set_object_terms( $post_id, 'external', 'product_type' );	
		update_post_meta( $post_id, '_virtual', 'yes' );
	}else{
		wp_set_object_terms( $post_id, 'simple', 'product_type' );	
	}
	
    
    update_post_meta( $post_id, '_visibility', 'visible' );
    update_post_meta( $post_id, '_stock_status', 'instock');
   
    if($compare_price != 0) {
      	update_post_meta( $post_id, '_regular_price', $compare_price );
    } else {
    	update_post_meta( $post_id, '_regular_price', $price );
    }
  
    update_post_meta($post_id, '_price', $price );
    update_post_meta($post_id, '_sale_price', $price );
    update_post_meta($post_id, '_backorders', 'no');
	update_post_meta($post_id, '_manage_stock', 'no');
	update_post_meta($post_id, '_product_url', $product_url);
			
	
	$data['product_id'] = $post_id;

	echo json_encode($data);
	exit();

}



add_action( 'wp_ajax_spreadr_update_product', 'spreadr_update_product' );
add_action( 'wp_ajax_nopriv_spreadr_update_product', 'spreadr_update_product' );

function spreadr_update_product() {
	
	$product = sanitize_text_or_array_field_spreadr($_POST['product']);
	$post_id = $product['product_id'];
	$title = $product['title'];
	$description = wp_kses_post($_POST['product']['body_html']);
	$feature_image = $product['images'][0];
	$images = $product['images'];

	$product_meta = $product['metafields'];
	$product_url = $product_meta[0]['value'];
	$tags = $product['tags'];
	$published = $product['published'];
	$price = $product['variants'][0]['price'];
	$compare_price = $product['variants'][0]['compare_at_price'];

	$productData = wp_update_post( array(
					'ID' => $post_id,
			        'post_title' => $title,
			        'post_content' => $description,
			    	) 
				);



    

   	$thumbnail_id = spreadr_image_import($feature_image['src'],$post_id);

   
    update_post_meta( $post_id, '_thumbnail_id', $thumbnail_id );

    if (count($images) > 1) {
    	 array_shift($images);
    	foreach ($images as $key => $image) {
    		$image_id = spreadr_image_import($image['src'],$post_id);;
			$product_images[] = $image_id;
    	}
    }


    if (!empty($product_images)) {
    	$gallery = implode(",",$product_images);
    	update_post_meta( $post_id, '_product_image_gallery', $gallery );
    }

   
    //update_post_meta( $post_id, 'junk', json_encode($product_meta) );
    foreach ($product_meta as $metakey => $meta) {

    	update_post_meta( $post_id,$meta['key'], $meta['value'] );
    	if ($meta['key'] == 'spreadr_tags') {
    		
    		$spreadtags = explode(",", $meta['value']);
    		wp_set_object_terms($post_id, $spreadtags, 'product_tag');
    	} elseif ($meta['key'] == 'spreadr_category') {
    		
    		wp_set_object_terms($post_id, $meta['value'], 'product_cat');
    	}

    	$meta = array();
    }

    	

    wp_set_object_terms( $post_id, 'external', 'product_type' );
    update_post_meta( $post_id, '_visibility', 'visible' );
    update_post_meta( $post_id, '_stock_status', 'instock');
    update_post_meta( $post_id, '_virtual', 'yes' );
    if($compare_price != 0) {
      	update_post_meta( $post_id, '_regular_price', $compare_price );
    } else {
    	update_post_meta( $post_id, '_regular_price', $price );
    }
  
    update_post_meta($post_id, '_price', $price );
    update_post_meta($post_id, '_sale_price', $price );
    update_post_meta($post_id, '_backorders', 'no');
	update_post_meta($post_id, '_manage_stock', 'no');
	update_post_meta($post_id, '_product_url', $product_url);
			
	
	$data['product_id'] = $post_id;

	echo json_encode($data);
	exit();

}




function spreadr_image_import($file,$parent_post_id) {
	

$filename = basename($file);



	if (file_get_contents($file)) {
		# test...

	$upload_file = wp_upload_bits($filename, null, file_get_contents($file));
	if (!$upload_file['error']) {
		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_parent' => $parent_post_id,
			'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );


		if (!is_wp_error($attachment_id)) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
			wp_update_attachment_metadata( $attachment_id,  $attachment_data );
		}

		return $attachment_id;
	}
	} else {

	    $image = wp_remote_retrieve_body( wp_remote_get($file) );
	   	$upload_file = wp_upload_bits($filename, null, $image);

		if (!$upload_file['error']) {
			$wp_filetype = wp_check_filetype($filename, null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_parent' => $parent_post_id,
				'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $parent_post_id );


			if (!is_wp_error($attachment_id)) {
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
			}

			return $attachment_id;
		}

	}
}


add_action( 'wp_ajax_spreadr_isproduct_available', 'spreadr_isproduct_available' );
add_action( 'wp_ajax_nopriv_spreadr_isproduct_available', 'spreadr_isproduct_available' );

function spreadr_isproduct_available() {
	$product_id = sanitize_text_or_array_field_spreadr($_POST['product_id']);

	$_product = wc_get_product( $product_id );
	if (!empty($_product)) {
		die(true);
	} else {
		die(false);
	}

}



add_action( 'wp_ajax_spreadr_installed', 'spreadr_installed' );
add_action( 'wp_ajax_nopriv_spreadr_installed', 'spreadr_installed' );

function spreadr_installed() {
	
	die(true);

}

//tmp
add_action( 'wp_ajax_spreadr_mark_product_outofstock', 'spreadr_mark_product_outofstock' );
add_action( 'wp_ajax_nopriv_spreadr_mark_product_outofstock', 'spreadr_mark_product_outofstock' );

function spreadr_mark_product_outofstock() {

	if ($_POST) {
		$product_id = sanitize_text_or_array_field_spreadr($_POST['product_id']);
		update_post_meta($product_id, '_stock_status', 'outofstock' );
		die(true);
	} else {
		die(false);
	}
	

}


add_action( 'wp_ajax_spreadr_mark_product_available', 'spreadr_mark_product_available' );
add_action( 'wp_ajax_nopriv_spreadr_mark_product_available', 'spreadr_mark_product_available' );

function spreadr_mark_product_available() {
	
	if ($_POST) {
		$product_id = sanitize_text_or_array_field_spreadr($_POST['product_id']);
		update_post_meta( $product_id, '_stock_status', 'instock' );
		die(true);
	} else {
		die(false);
	}

}

add_action( 'wp_ajax_spreadr_hide_product', 'spreadr_hide_product' );
add_action( 'wp_ajax_nopriv_spreadr_hide_product', 'spreadr_hide_product' );

function spreadr_hide_product() {
	

	if ($_POST) {
		$product_id = sanitize_text_or_array_field_spreadr($_POST['product_id']);	

		wp_update_post(array(
			'ID'    =>  $product_id,
			'post_status'   =>  'draft'
		));

		die(true);
	} else {
		die(false);
	}

}

add_action( 'wp_ajax_spreadr_delete_product', 'spreadr_delete_product' );
add_action( 'wp_ajax_nopriv_spreadr_delete_product', 'spreadr_delete_product' );

function spreadr_delete_product() {
	
	if ($_POST) {
		$product_id = sanitize_text_or_array_field_spreadr($_POST['product_id']);	

		wp_update_post(array(
			'ID'    =>  $product_id,
			'post_status'   =>  'trash'
		));

		die(true);
	} else {
		die(false);
	}

}

add_action( 'wp_ajax_spreadr_update_product_price', 'spreadr_update_product_price' );
add_action( 'wp_ajax_nopriv_spreadr_update_product_price', 'spreadr_update_product_price' );

function spreadr_update_product_price() {

	if ($_POST) {

	$product_id = sanitize_text_or_array_field_spreadr($_POST['product_id']);	

	$price = sanitize_text_or_array_field_spreadr($_POST['price']);
	$compare_price = sanitize_text_or_array_field_spreadr($_POST['compare_price']);
	if($compare_price != 0) {
      	update_post_meta( $product_id, '_regular_price', $compare_price );
    } else {
    	update_post_meta( $product_id, '_regular_price', $price );
    }
  
    update_post_meta($product_id, '_price', $price );

    if ($compare_price == 0) {
    		delete_post_meta($product_id, '_sale_price');

    } else  {
    		
    	 update_post_meta($product_id, '_sale_price', $price );
    }

	die(true);

	} else {
		die(false);
	}

}

add_action( 'wp_ajax_spreadr_review_settings', 'spreadr_review_settings' );
add_action( 'wp_ajax_nopriv_spreadr_review_settings', 'spreadr_review_settings' );

function spreadr_review_settings() {
	
	if ($_POST) {

	if( isset( $_POST['spreadr_review_token'] ) )
	update_option( 'spreadr_review_token', sanitize_text_or_array_field_spreadr($_POST['spreadr_review_token']) ,true );
	if( isset( $_POST['spreadr_review_userid'] ) )
	update_option( 'spreadr_review_userid', sanitize_text_or_array_field_spreadr($_POST['spreadr_review_userid']) ,true );
	if( isset( $_POST['spreadr_is_review_on'] ) )
	update_option( 'spreadr_is_review_on', sanitize_text_or_array_field_spreadr($_POST['spreadr_is_review_on']) ,true );
	if( isset( $_POST['spreadr_review_display'] ) )
	update_option( 'spreadr_review_display', sanitize_text_or_array_field_spreadr($_POST['spreadr_review_display']) ,true );
	
	

		die(true);
	} else {
		die(false);
	}

}




add_action( 'admin_notices', 'sp_subscriber_check_activation_notice' );
function sp_subscriber_check_activation_notice(){
     if( get_transient( 'spreadr_installing' ) ){
       echo '<script>window.open("'.SPREADR_APP_URL.'verifynewuser/'.get_option( 'spreadr_token').'", "_blank");</script>';
        delete_transient( 'spreadr_installing' );
    }
}


