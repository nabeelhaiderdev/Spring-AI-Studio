<?php
/**
 * Functions for advanced custom fields plugin
 *
 * @link https://www.advancedcustomfields.com/resources/
 *
 * @package BaseTheme Package
 * @since 1.0.0
 *
 */


/**
 * Render custom Gutenberg blocks
 */

function glide_acf_block_callback( $block ) {
	// convert name ("acf/Testimonial") into path friendly slug ("Testimonial")
	$slug = str_replace( 'acf/', '', $block['name'] );

	// include a template part from within the "template-parts/block" folder
	if ( file_exists( get_theme_file_path( "/blocks/block-{$slug}.php" ) ) ) {
		include get_theme_file_path( "/blocks/block-{$slug}.php" );
	}
}


 /**
 * Register custom Gutenberg blocks category
 */
function glide_blocks_category( $categories ) {
    $custom_block = array(
        'slug'  => 'glide-blocks',
		'title' => __( 'Glide Blocks', 'basetheme_td' ),
		'icon'  => 'glide-blocks',
    );

    $categories_sorted = array();
    $categories_sorted[0] = $custom_block;

    foreach ($categories as $category) {
        $categories_sorted[] = $category;
    }

    return $categories_sorted;
}
add_filter( 'block_categories_all', 'glide_blocks_category', 10, 2);


/**
 * Build ACF based theme Options page
 */
if( function_exists('acf_add_options_page') ) {
  $option_page = acf_add_options_page(array(
		'page_title' 	=> __( 'Theme Options', 'basetheme_td' ),
		'menu_title' 	=> __( 'Theme Options', 'basetheme_td' ),
		'menu_slug' 	=> 'acf-options',
		'capability' 	=> 'edit_posts',
		'redirect' 		=> false,
		'position'		=> 2
	));
}

/**
 * Helper function that builds button from ACF link object
 */
function glide_acf_button($object, $classes="") {
	if($object['url']){
		$link = "";
		$link = "<a href='".esc_url($object['url']). "' title='".esc_html($object['title'])."' target='".$object['target']."' class='".$classes."'>".esc_html($object['title'])."</a>";
		return $link;
	}
	return null;
}


/**
 * Helper function to get escaped field from ACF
 * and also normalize values.
 *
 */
function get_fields_escaped($field_key, $escape_method = 'esc_html')
{
	if (function_exists('get_fields') ) {
		$field = get_fields($field_key);
	}
	/* Check for null and falsy values and always return space */
    if($field === NULL || $field === FALSE)
        $field = '';

	/* Handle arrays */
	if(is_array($field) || is_object($field))
	{
		$field_escaped = array();
		foreach($field as $key => $value)
		{
			if(is_array($value) || is_object($value)){
				$field_escaped[$key] =	get_sub_field_escaped($value, $escape_method);
			} else {
				$field_escaped[$key] =  if_exist( ($escape_method === NULL) ? $value : $escape_method($value) );
				// $field_escaped[$key] =   esc_html($value);
			}
		}
		return $field_escaped;
	}
	else{
		return if_exist( ($escape_method === NULL) ? $field : $escape_method($field) );
	}
}



/**
 * Helper function to get escaped field for a sub-field from ACF inside a parent
 * and also normalize values.
 *
 */
function get_sub_field_escaped($parent =null, $escape_method = 'esc_html' )
 {
	$field = $parent;
	/* Check for null and falsy values and always return space */
	if($field === NULL || $field === FALSE)
	$field = '';

	/* Handle arrays */
	if(is_array($field) || is_object($value))
	{
	$field_escaped = array();
	foreach($field as $key => $value)
	{
		if(is_array($value) || is_object($value)){
			if(is_object($value)){
				$obj=new \stdClass();;

				foreach ($value as $obj_k => $obj_v) {

					$obj->$obj_k= if_exist( ($escape_method === NULL) ? $obj_v : $escape_method($obj_v) );
				}
				$field_escaped[$key]=$obj;
			}else{
				$field_escaped[$key] =	get_sub_field_escaped($value, $escape_method);
			}
		} else {

		$field_escaped[$key] =  if_exist( ($escape_method === NULL) ? $value : $escape_method($value) );
		}
	}
		return $field_escaped;
	}
	else{
		return if_exist( ($escape_method === NULL) ? $field : $escape_method($field) );
	}

 }
function if_exist($value){
	return (isset($value) && $value!='') ? $value : null;
}
function glide_page_title($pagetitle){
	global $fields;
	$pagetitle = (isset($fields[$pagetitle])  && $fields[$pagetitle]!='' ) ? $fields[$pagetitle] : null;
	if(!$pagetitle){
		$pagetitle = get_the_title();
	}
	return $pagetitle;
}
function html_entity_remove($string){
	return sanitize_text_field(html_entity_decode($string));
}

 /**
 * Add height field to ACF WYSIWYG
 */

function wysiwyg_render_field_settings( $field ) {
	acf_render_field_setting( $field, array(
		'label'			=> __('Height of Editor', 'basetheme_td'),
		'instructions'	=> __('Height of Editor after Init', 'basetheme_td'),
		'name'			=> 'wysiwyg_height',
		'type'			=> 'number',
	));
}
add_action('acf/render_field_settings/type=wysiwyg', 'wysiwyg_render_field_settings', 10, 1 );


/**
 * Render height on ACF WYSIWYG
 */

function wysiwyg_render_field( $field ) {
    $field_class = '.acf-'.str_replace('_', '-', $field['key']);
    $wysiwyg_height = (isset($field['wysiwyg_height'])) ? $field['wysiwyg_height'] : null;
    if(!$wysiwyg_height){
        $custom_acf_wysiwyg_height = '150';
    } else {
        $custom_acf_wysiwyg_height = $field['wysiwyg_height'];
    }
?>
    <style type="text/css">
    <?php echo $field_class; ?> iframe {
        min-height: <?php echo $custom_acf_wysiwyg_height; ?>px;
    }
    </style>
    <script type="text/javascript">
    jQuery(window).load(function() {
        jQuery('<?php echo $field_class; ?>').each(function() {
            jQuery('#'+jQuery(this).find('iframe').attr('id')).height( <?php echo $custom_acf_wysiwyg_height; ?> );
        });
    });
    </script>
<?php
}
add_action( 'acf/render_field/type=wysiwyg', 'wysiwyg_render_field', 10, 1 );






/*
* SUGGEST: USE THIS CODE TO CONTROL BLOCK SETTINGS, THEN USE THEME.JSON TO CONTROL DEFAULT STYLES
*/

//filter by global settings or user roles
function wp_block_editor_settings( $editor_settings, $editor_context ) {

	$user = wp_get_current_user();
	$roles = $user->roles;

	$theme_json = WP_Theme_JSON_Resolver::get_merged_data( $editor_settings );

	if ( WP_Theme_JSON_Resolver::theme_has_support() ):
		$editor_settings['styles'][] = array(
			'css'            => $theme_json->get_stylesheet( 'block_styles' ),
			'__unstableType' => 'globalStyles',
		);
		$editor_settings['styles'][] = array(
			'css'                     => $theme_json->get_stylesheet( 'css_variables' ),
			'__experimentalNoWrapper' => true,
			'__unstableType'          => 'globalStyles',
		);
	endif;

	$editor_settings['__experimentalFeatures'] = $theme_json->get_settings();
//if(!in_array('administrator',$roles) && !in_array('editor',$roles)):

	if(in_array( $editor_context->post->post_type, [ 'post' ], true )): //filter by post type

		$editor_settings['allowedBlockTypes'] = array(
			'core/freeform' //only show classic editor
		);

	else:
		$block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
		$types=array();
		foreach ($block_types as $key => $item) {
			if(explode('/',$key)[0]=='acf'){
				$types[]=$key;
			}
		}
		$allowed= array(
			'acf/image-alongside-text',
			'core/paragraph',
			'core/image',
			'core/gallery',
			'core/cover',
			'core/video',
			'core/list',
			'core/heading',
			'core/buttons',
			'core/columns',
			'core/separator',
			'core/spacer',
		);
		$editor_settings['allowedBlockTypes'] = array_merge($allowed,$types);
	endif;

	$editor_settings['__experimentalFeatures']['appearanceTools'] = 0;
	$editor_settings['__experimentalFeatures']['className'] = 0;
	$editor_settings['__experimentalFeatures']['customClassName'] = 0;
	$editor_settings['__experimentalFeatures']['anchor'] = 0;

	$editor_settings['__experimentalFeatures']['border']['color'] = 0;
	$editor_settings['__experimentalFeatures']['border']['radius']  = 0;
	$editor_settings['__experimentalFeatures']['border']['style']  = 0;
	$editor_settings['__experimentalFeatures']['border']['width']  = 0;

	$editor_settings['__experimentalFeatures']['color']['text'] = 0;
	//$editor_settings['__experimentalFeatures']['color']['background'] = 0;
	$editor_settings['__experimentalFeatures']['color']['link'] = 0;
	//$editor_settings['__experimentalFeatures']['color']['custom'] = 0;
	$editor_settings['__experimentalFeatures']['color']['customDuotone'] = 0;
	$editor_settings['__experimentalFeatures']['color']['customGradient'] = 0;
	$editor_settings['__experimentalFeatures']['color']['defaultGradients'] = 0;
	//$editor_settings['__experimentalFeatures']['color']['defaultPalette'] = 0;
	$editor_settings['__experimentalFeatures']['color']['defaultDuotone'] = 0;

	$editor_settings['__experimentalFeatures']['spacing']['blockGap'] = 0;
	$editor_settings['__experimentalFeatures']['spacing']['margin'] = 0;
	$editor_settings['__experimentalFeatures']['spacing']['padding'] = 0;
	$editor_settings['__experimentalFeatures']['spacing']['units'] = [];

	$editor_settings['__experimentalFeatures']['typography']['customFontSize'] = 0;
	$editor_settings['__experimentalFeatures']['typography']['dropCap'] = 0;
	$editor_settings['__experimentalFeatures']['typography']['fontStyle'] = 0;
	$editor_settings['__experimentalFeatures']['typography']['fontWeight'] = 0;
	$editor_settings['__experimentalFeatures']['typography']['letterSpacing'] = 0;
	$editor_settings['__experimentalFeatures']['typography']['lineHeight'] = 0;
	$editor_settings['__experimentalFeatures']['typography']['textDecoration'] = 0;
	$editor_settings['__experimentalFeatures']['typography']['textTransform'] = 0;

	//still working on these:

	$editor_settings['__experimentalFeatures']['blocks']['core/button']['spacing'] = 0;
	$editor_settings['__experimentalFeatures']['blocks']['core/button']['typography'] = 0;
	$editor_settings['__experimentalFeatures']['blocks']['core/button']['border']['radius'] = 0;
	$editor_settings['__experimentalFeatures']['blocks']['core/button']['color']['background'] = 0;
	$editor_settings['__experimentalFeatures']['blocks']['core/button']['color']['custom'] = 0;
	$editor_settings['__experimentalFeatures']['blocks']['core/button']['width'] = 0;
	$editor_settings['__experimentalFeatures']['blocks']['core/button']['defaultStylePicker'] = 0;

	//endif;


	// echo '<pre>';
	// print_r($editor_settings['__experimentalFeatures']);
	// echo '</pre>';
	// exit;


	return $editor_settings;
}

// add_filter( 'block_editor_settings_all', 'wp_block_editor_settings', 10, 2 );

