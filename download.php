<?php

function zk_get_file($id){
	$file_id = intval($id);
	if(!$file_id){
		return null;
	}

	$file = get_post($file_id);
	if(!$file || $file->post_type!="zk_file"){
		return null;
	}

	return $file;
}

function zk_get_valid_file(){
	if(isset($_GET['file'])){
		return zk_get_file($_GET['file']);
	}
	return null;
}

function zk_build_table($atts){
	$category=$atts['category'];
	$table_class=$atts['table_class'];

	$page_link = get_permalink();


	$category_slug=sanitize_title($category);

	$the_query = new WP_Query(array(
		'post_type'=>'zk_file',
		'tax_query'=>array(
			array(
				'taxonomy'=>'zk_file_category',
				'field'=>'slug',
				'terms'=>$category_slug
			)
		),
		'orderby'=>'title',
		'order'=>'ASC'
	));

	if($the_query->have_posts() && $category!=null){
		$file_table="<table class='$table_class'>";
		while($the_query->have_posts()){
			$the_query->the_post();
			$file_table.="<tr>";
			$file_table.="<td><a href='$page_link?file=".get_the_id()."'>".get_the_title()."</a></td>";
			$file_table.="<td>".get_the_content()."</td>";
			$file_table.="</tr>";

		}
		$file_table.="</table>";
		return $file_table;
	}
	elseif(!$category==null){
		return "No files in the category &quot;$category&quot;";
	}

	return "Please supply a category in the form [file_agreement_download category=&quot;Some Category&quot;]";
}

function zk_check_agreement($file){
	return isset($_POST['agreement']) && $_POST['agreement']==zk_get_button_text($file);
}

function zk_get_button_text($file){
	return str_replace("[name]", $file->post_title, get_option('zk_file_agreement_field_button',"I agree - download [name]"));
}

function zk_build_agreement_form($file){
	return
	"<p>".nl2br(get_option("zk_file_agreement_field_message"))."</p>
	<form method='post' action='".get_home_url().zk_get_download_path()."'>
		<input class='btn btn-primary' type='submit' name='agreement' value='".zk_get_button_text($file)."'>
		<input type='hidden' name='file' value='$file->ID'>
	</form>
	<p><a href='".get_permalink()."'>Return to download list</a></p>
	";
}



function zk_file_agreement_form( $user_atts ){

	$atts=shortcode_atts(array(
		'category'=>null,
		'table_class'=>null,
		),$user_atts);

	$file = zk_get_valid_file();

	if($file!=null){
		return zk_build_agreement_form($file);
	}

	return zk_build_table($atts);
	
}

function zk_get_download_path(){
	return '/'.get_option('zk_file_agreement_download_slug','download');
}

add_shortcode( 'file_agreement_download', 'zk_file_agreement_form' );


add_action('template_redirect','zk_download_redirect_check');
function zk_download_redirect_check() {
  if ($_SERVER['REQUEST_URI']==zk_get_download_path()) {
  	if(isset($_POST['file'])){
  		$file = zk_get_file($_POST['file']);
  		if($file!=null){
  			if(zk_check_agreement($file)){
		  		$file_path = zk_get_file_path(get_post_meta($file->ID, 'zk_file_attachment_url',true));
		  		$file_name = get_post_meta($file->ID, 'zk_file_attachment_name',true);
		  		$mime_type = mime_content_type($file_path);


		  		header("Content-type: $mime_content_type",true,200);
		  		header("Content-Disposition: attachment; filename=$file_name");
		  		header("Pragma: no-cache");
    			header("Expires: 0");
    			echo file_get_contents($file_path);

		  		exit();
		  	}
  		}
  	}
  	

    wp_redirect(get_home_url());
    exit();
  }
}