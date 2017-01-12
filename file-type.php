<?php
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'zk_file',
    array(
      'labels' => array(
        'name' => __( 'Protected Files' ),
        'singular_name' => __( 'Protected File' ),
        'add_new_item' => __('Add New File Protected File'),
        'new_item_name' => __("New Protected File"),
        'edit_item'=>__('Edit Protected File')

      ),
      'public' => false,
      'has_archive' => false,
      'show_ui'=>true,
      'supports'=>['title','editor'],
      'register_meta_box_cb'=>'zk_add_file_upload_box'
    )
  );
}

function zk_add_file_upload_box() {
    add_meta_box('zk_file_attachment', 'File', 'zk_file_upload_box', 'zk_file', 'normal', 'default');

}

// The Event Location Metabox

function zk_file_upload_box() {
  global $post;

  wp_nonce_field(plugin_basename(__FILE__), 'zk_file_upload_nonce');

  // Get the location data if its already been entered
  // $location = get_post_meta($post->ID, '_location', true);
  ?>
  
  
  

  <?php
  $current_file_path = get_post_meta($post->ID, 'zk_file_attachment_url',true);
  $current_file_name = get_post_meta($post->ID, 'zk_file_attachment_name',true);
  if($current_file_path!=null){

    ?>
    <p><a href='<?= $current_file_path ?>' target='_blank'><?= $current_file_name ?></a></p>
    <p class='description'>Upload new file...</p>
    <?php
  }

  ?>
  <input type="file" name="zk_file_attachment" id='zk_file_attachment' class="widefat" />
  <?php



}

function zk_save_file($id) {

    if(get_post_type($id)!='zk_file'){
      return;
    }

    /* --- security verification --- */
    if(!wp_verify_nonce($_POST['zk_file_upload_nonce'], plugin_basename(__FILE__))) {
      return $id;
    } // end if
       
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $id;
    } // end if
       
    if(!current_user_can('edit_post', $id)) {
      return $id;
    } // end if
    if(!empty($_FILES['zk_file_attachment']['name'])) {
         
        //allowed MIME types
        include('allowed-mime-types.php');
        // Get the file type of the upload
        $arr_file_type = wp_check_filetype(basename($_FILES['zk_file_attachment']['name']));
        $uploaded_type = $arr_file_type['type'];
         
        // Check if the type is supported. If not, throw an error.
        if(in_array($uploaded_type, $supported_types)) {
 
            // Use the WordPress API to upload the file

            $upload = wp_upload_bits(wp_generate_password().$_FILES['zk_file_attachment']['name'], null, file_get_contents($_FILES['zk_file_attachment']['tmp_name']));
     
            if(isset($upload['error']) && $upload['error'] != 0) {
                wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
            } else {

                if(!add_post_meta($id, 'zk_file_attachment_url', $upload['url'], true)){
                  zk_delete_file(get_post_meta($id, 'zk_file_attachment_url',true));
                  update_post_meta($id, 'zk_file_attachment_url', $upload['url']);  
                }
                if(!add_post_meta($id, 'zk_file_attachment_name', $_FILES['zk_file_attachment']['name'], true)){
                  update_post_meta($id, 'zk_file_attachment_name', $_FILES['zk_file_attachment']['name']);  
                }
            } // end if/else
 
        } else {
            wp_die("The file type that you've uploaded is not allowed.");
        } // end if/else
         
    } // end if

     
} // end save_custom_meta_data

add_action('save_post', 'zk_save_file');

add_action( 'post_edit_form_tag' , 'zk_edit_form_tag' );

function zk_edit_form_tag( ) {
  global $post;
  if(get_post_type($id)=='zk_file'){
    echo ' enctype="multipart/form-data"';
  }
}

function zk_delete_file($url){
  $file_path = zk_get_file_path($url);
  echo $file_path;
  unlink($file_path);
}


add_action( 'init', 'zk_create_file_taxonomy', 0 );

function zk_create_file_taxonomy() {
    register_taxonomy(
        'zk_file_category',
        'zk_file',
        array(
            'labels' => array(
                'name' => 'File Category',
                'add_new_item' => 'Add New File Category',
                'new_item_name' => "New File Category"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => false
        )
    );
}

add_action( 'before_delete_post', 'zk_before_delete_file' );
function zk_before_delete_file( $postid ){

    // We check if the global post type isn't ours and just return
    global $post_type;   
    if ( $post_type != 'zk_file' ) return;

    zk_delete_file(get_post_meta($postid, 'zk_file_attachment_url',true));
    
}
?>