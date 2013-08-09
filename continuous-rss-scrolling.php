<?php

/*
Plugin Name: continuous rss scrolling
Plugin URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Description: This plug-in will scroll the RSS title in the wordpress website, <a href="http://www.gopiplus.com/work/" target="_blank">Live demo</a>.
Author: Gopi.R
Version: 9.1
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
		var crs_heightOfElm = '<?php echo $crs_record_height; ?>'; // Height of each element (px)
		var crs_numberOfElm = '<?php echo $crs_count; ?>';
		var crs_scrollOn 	= 'true';
		function crs_createscroll() 
		{
			<?php echo $crs_x; ?>
			crs_obj	= document.getElementById('crs_Holder');
			crs_obj.style.height = (crs_numberOfElm * crs_heightOfElm) + 'px'; // Set height of DIV
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
		echo "<div style='padding-bottom:5px;padding-top:5px;'>No data available!</div>";
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
	echo '<p>Continuous rss scrolling.  <a href="options-general.php?page=continuous-rss-scrolling">click here</a> to update.</p>';
	?>
	Check official website for live demo and more information <a target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/">click here</a>
	<?php
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
		<h2>Continuous rss scrolling</h2>
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
				<p><strong>Details successfully updated.</strong></p>
			</div>
			<?php
		}
		?>
		<h3>Plugin setting</h3>
		<form name="crs_form" method="post" action="#">
		
			<label for="tag-title">Title</label>
			<input name="crs_title" type="text" value="<?php echo $crs_title; ?>"  id="crs_title" size="70" maxlength="200">
			<p>Please enter your widget title.</p>
			
			<label for="tag-title">Scroll height</label>
			<input name="crs_record_height" type="text" value="<?php echo $crs_record_height; ?>"  id="crs_record_height" maxlength="3">
			<p>If any overlap in the announcement text at front end, <br>you should arrange(increase/decrease) the above height.</p>
			
			<label for="tag-title">Display count</label>
			<input name="crs_display_count" type="text" value="<?php echo $crs_display_count; ?>"  id="crs_display_count" maxlength="3">
			<p>Please enter number of records you want to display at the same time in scroll.</p>
			
			<label for="tag-title">Display length</label>
			<input name="crs_display_width" type="text" value="<?php echo $crs_display_width; ?>"  id="crs_display_width" maxlength="3">
			<p>Please enter max number of character to display in the scroll.</p>
			
			<label for="tag-title">RSS url</label>
			<input name="crs_rss_url" type="text" value="<?php echo $crs_rss_url; ?>"  id="crs_rss_url" size="120">
			<p>Please enter your RSS url.</p>
		
			<div style="height:10px;"></div>
			<input type="hidden" name="crs_form_submit" value="yes"/>
			<input name="crs_submit" id="crs_submit" class="button" value="Submit" type="submit" />
			<a class="button" target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/">Help</a>
			<?php wp_nonce_field('crs_form_setting'); ?>
		</form>
		</div>
		<h3>Plugin configuration option</h3>
		<ol>
			<li>Drag and drop the widget to your sidebar.</li>
			<li>Add directly in to the theme using PHP code.</li>
		</ol>
	<p class="description">Check official website for more information <a target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/">click here</a></p>
	</div>
	<?php
}

function crs_add_to_menu() 
{
	add_options_page('Continuous rss scrolling', 'Continuous rss scrolling', 'manage_options', 'continuous-rss-scrolling', 'crs_admin_options' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'crs_add_to_menu');
}

function crs_init()
{
	if(function_exists('wp_register_sidebar_widget')) 
	{
		wp_register_sidebar_widget('Continuous-rss-scrolling', 'Continuous rss scrolling', 'crs_widget');
	}
	
	if(function_exists('wp_register_widget_control')) 
	{
		wp_register_widget_control('Continuous-rss-scrolling', array('Continuous rss scrolling', 'widgets'), 'crs_control');
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
		wp_enqueue_script( 'javascript', get_option('siteurl').'/wp-content/plugins/continuous-rss-scrolling/continuous-rss-scrolling.js');
	}
}

add_action('init', 'crs_add_javascript_files');
add_action("plugins_loaded", "crs_init");
register_activation_hook(__FILE__, 'crs_install');
register_deactivation_hook(__FILE__, 'crs_deactivation');
?>