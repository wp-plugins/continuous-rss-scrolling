<?php
/*
Plugin Name: Continuous rss scrolling
Plugin URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Description: This plug-in will scroll the RSS title in the wordpress website, <a href="http://www.gopiplus.com/work/" target="_blank">Live demo</a>.
Author: Gopi.R
Version: 9.2
Author URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Donate link: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Tags: Continuous, announcement, scroller, message, rss, xml
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

global $wpdb, $wp_version;

function crs() 
{
	global $wpdb;
	$crs_html = "";
	$crs_x = "";
	$crs_display_width = get_option('crs_display_width');
	$crs_display_count = get_option('crs_display_count');
	$crs_record_height = get_option('crs_record_height');
	if(!is_numeric($crs_display_width))
	{
		$crs_display_width = 200;
	} 
	if(!is_numeric($crs_record_height))
	{
		$crs_record_height = 30;
	}
	if(!is_numeric($crs_display_count))
	{
		$crs_display_count = 5;
	}

	if(get_option('crs_rss_url') <> "")
	{
		$url = get_option('crs_rss_url');
	}
	else
	{
		$url = "http://www.gopiplus.com/work/category/word-press-plug-in/feed/";
	}

	$xml = "";
	$cnt=0;
	$f = fopen( $url, 'r' );
	while( $data = fread( $f, 4096 ) ) { $xml .= $data; }
	fclose( $f );
	preg_match_all( "/\<item\>(.*?)\<\/item\>/s", $xml, $itemblocks );
	
	$cnt=0;
	
	if ( ! empty($itemblocks) ) 
	{
		$crs_count = 0;
		foreach( $itemblocks[1] as $block )
		{
			preg_match_all( "/\<title\>(.*?)\<\/title\>/",  $block, $title );
			preg_match_all( "/\<link\>(.*?)\<\/link\>/", $block, $link );
			$crs_post_title = $title[1][0];
			$crs_post_title = mysql_real_escape_string(trim($crs_post_title));
			$get_permalink = $link[1][0];
			$get_permalink = mysql_real_escape_string(trim($get_permalink));
			
			$crs_post_title = substr($crs_post_title, 0, $crs_display_width);

			$dis_height = $crs_record_height."px";
			$crs_html = $crs_html . "<div class='crs_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
			$crs_html = $crs_html . "<a target='_blank' href='$get_permalink'>$crs_post_title</a>";
			$crs_html = $crs_html . "</div>";
			
			$crs_post_title = trim($crs_post_title);
			$get_permalink = $get_permalink;
			$crs_x = $crs_x . "crs_array[$crs_count] = '<div class=\'crs_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a target=\'_blank\' href=\'$get_permalink\'>$crs_post_title</a></div>'; ";	
			$crs_count++;
			
		}
		$crs_record_height = $crs_record_height + 4;
		if($crs_count >= $crs_display_count)
		{
			$crs_count = $crs_display_count;
			$crs_height = ($crs_record_height * $crs_display_count);
		}
		else
		{
			$crs_count = $crs_count;
			$crs_height = ($crs_count*$crs_record_height);
		}
		$crs_height1 = $crs_record_height."px";
		?>	
		<div style="padding-top:8px;padding-bottom:8px;">
			<div style="text-align:left;vertical-align:middle;text-decoration: none;overflow: hidden; position: relative; margin-left: 1px; height: <?php echo $crs_height1; ?>;" id="crs_Holder">
				<?php echo $crs_html; ?>
			</div>
		</div>
		<script type="text/javascript">
		var crs_array	= new Array();
		var crs_obj	= '';
		var crs_scrollPos 	= '';
		var crs_numScrolls	= '';
		var crs_heightOfElm = '<?php echo $crs_record_height; ?>';
		var crs_numberOfElm = '<?php echo $crs_count; ?>';
		var crs_scrollOn 	= 'true';
		function crs_createscroll() 
		{
			<?php echo $crs_x; ?>
			crs_obj	= document.getElementById('crs_Holder');
			crs_obj.style.height = (crs_numberOfElm * crs_heightOfElm) + 'px';
			crs_content();
		}
		</script>
		<script type="text/javascript">
		crs_createscroll();
		</script>
		<?php
	}
	else
	{
		_e('No data available', 'continuous-rss-scrolling');
	}
}

function crs_install() 
{
	global $wpdb;
	add_option('crs_title', "RSS scroller");
	add_option('crs_display_width', "200");
	add_option('crs_display_count', "5");
	add_option('crs_record_height', "40");
	add_option('crs_rss_url', "http://www.gopiplus.com/work/category/word-press-plug-in/feed/");
}

function crs_control() 
{
	_e('Continuous rss scrolling', 'continuous-rss-scrolling');
}

function crs_widget($args) 
{
	extract($args);
	echo $before_widget . $before_title;
	echo get_option('crs_title');
	echo $after_title;
	crs();
	echo $after_widget;
}

function crs_admin_options() 
{
	?>
	<div class="wrap">
	  <div class="form-wrap">
		<div id="icon-edit" class="icon32 icon32-posts-post"></div>
		<h2><?php _e('Continuous rss scrolling', 'continuous-rss-scrolling'); ?></h2>
		<?php
		$crs_title = get_option('crs_title');
		$crs_display_width = get_option('crs_display_width');
		$crs_display_count = get_option('crs_display_count');
		$crs_record_height = get_option('crs_record_height');
		$crs_rss_url = get_option('crs_rss_url');
		
		if (isset($_POST['crs_form_submit']) && $_POST['crs_form_submit'] == 'yes')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('crs_form_setting');
			
			$crs_title = stripslashes($_POST['crs_title']);
			$crs_display_width = stripslashes($_POST['crs_display_width']);
			$crs_display_count = stripslashes($_POST['crs_display_count']);
			$crs_record_height = stripslashes($_POST['crs_record_height']);
			$crs_rss_url = stripslashes($_POST['crs_rss_url']);
			
			update_option('crs_title', $crs_title );
			update_option('crs_display_width', $crs_display_width );
			update_option('crs_display_count', $crs_display_count );
			update_option('crs_record_height', $crs_record_height );
			update_option('crs_rss_url', $crs_rss_url );
			
			?>
			<div class="updated fade">
				<p><strong><?php _e('Details successfully updated.', 'continuous-rss-scrolling'); ?></strong></p>
			</div>
			<?php
		}
		?>
		<h3><?php _e('Plugin setting', 'continuous-rss-scrolling'); ?></h3>
		<form name="crs_form" method="post" action="#">
		
			<label for="tag-title"><?php _e('Title', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_title" type="text" value="<?php echo $crs_title; ?>"  id="crs_title" size="70" maxlength="200">
			<p><?php _e('Please enter your widget title.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('Scroll height', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_record_height" type="text" value="<?php echo $crs_record_height; ?>"  id="crs_record_height" maxlength="3">
			<p><?php _e('If any overlap in the announcement text at front end, <br>you should arrange(increase/decrease) the above height.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('Display count', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_display_count" type="text" value="<?php echo $crs_display_count; ?>"  id="crs_display_count" maxlength="3">
			<p><?php _e('Please enter number of records you want to display at the same time in scroll.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('Display length', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_display_width" type="text" value="<?php echo $crs_display_width; ?>"  id="crs_display_width" maxlength="3">
			<p><?php _e('Please enter max number of character to display in the scroll.', 'continuous-rss-scrolling'); ?></p>
			
			<label for="tag-title"><?php _e('RSS url', 'continuous-rss-scrolling'); ?></label>
			<input name="crs_rss_url" type="text" value="<?php echo $crs_rss_url; ?>"  id="crs_rss_url" size="120">
			<p><?php _e('Please enter your RSS url.', 'continuous-rss-scrolling'); ?></p>
		
			<div style="height:10px;"></div>
			<input type="hidden" name="crs_form_submit" value="yes"/>
			<input name="crs_submit" id="crs_submit" class="button" value="Submit" type="submit" />
			<a class="button" target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/"><?php _e('Help', 'continuous-rss-scrolling'); ?></a>
			<?php wp_nonce_field('crs_form_setting'); ?>
		</form>
		</div>
		<h3><?php _e('Plugin configuration option', 'continuous-rss-scrolling'); ?></h3>
		<ol>
			<li><?php _e('Drag and drop the widget to your sidebar.', 'continuous-rss-scrolling'); ?></li>
			<li><?php _e('Add directly in to the theme using PHP code.', 'continuous-rss-scrolling'); ?></li>
		</ol>
	<p class="description"><?php _e('Check official website for more information', 'continuous-rss-scrolling'); ?> 
	<a target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/"><?php _e('click here', 'continuous-rss-scrolling'); ?></a></p>
	</div>
	<?php
}

function crs_add_to_menu() 
{
	add_options_page(__('Continuous rss scrolling', 'continuous-rss-scrolling'), 
			__('Continuous rss scrolling', 'continuous-rss-scrolling'), 'manage_options', 'continuous-rss-scrolling', 'crs_admin_options' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'crs_add_to_menu');
}

function crs_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('Continuous-rss-scrolling', __('Continuous rss scrolling', 'continuous-rss-scrolling'), 'crs_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('Continuous-rss-scrolling', array(__('Continuous rss scrolling', 'continuous-rss-scrolling'), 'widgets'), 'crs_control');
	} 
}

function crs_deactivation() 
{
	// No action required.
}

function crs_add_javascript_files() 
{
	if (!is_admin())
	{
		wp_enqueue_script( 'continuous-rss-scrolling', get_option('siteurl').'/wp-content/plugins/continuous-rss-scrolling/continuous-rss-scrolling.js');
	}
}

function crs_textdomain() 
{
	  load_plugin_textdomain( 'continuous-rss-scrolling', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('plugins_loaded', 'crs_textdomain');
add_action('init', 'crs_add_javascript_files');
add_action("plugins_loaded", "crs_init");
register_activation_hook(__FILE__, 'crs_install');
register_deactivation_hook(__FILE__, 'crs_deactivation');
?>