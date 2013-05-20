<?php

/*
Plugin Name: continuous rss scrolling
Plugin URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Description: This plug-in will scroll the RSS title in the wordpress website, <a href="http://www.gopiplus.com/work/" target="_blank">Live demo</a>.
Author: Gopi.R
Version: 9.0
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
	echo '<p>Continuous rss scrolling.<br> To change the setting goto Continuous rss scrolling link on Setting menu.';
	echo ' <a href="options-general.php?page=continuous-rss-scrolling/continuous-rss-scrolling.php">';
	echo 'click here</a></p>';
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
	global $wpdb;
	?>

	<div class="wrap">
    <h2>Continuous rss scrolling</h2>
    </div>
	<?php
	$crs_title = get_option('crs_title');
	$crs_display_width = get_option('crs_display_width');
	$crs_display_count = get_option('crs_display_count');
	$crs_record_height = get_option('crs_record_height');
	$crs_rss_url = get_option('crs_rss_url');
	
	if (@$_POST['crs_submit']) 
	{
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
	}
	
	?>
	<form name="crs_form" method="post" action="">
	<?php
	echo '<p>Title:<br><input  style="width: 200px;" type="text" value="';
	echo $crs_title . '" name="crs_title" id="crs_title" /></p>';
	
	echo '<p>Each scroller height in scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $crs_record_height . '" name="crs_record_height" id="crs_record_height" /> (default: 30) ';
	echo 'If any overlap in the announcement text at front end, <br>you should arrange(increase/decrease) the above height.</p>';
	
	echo '<p>Display number of record at the same time in scroll:<br><input  style="width: 100px;" type="text" value="';
	echo $crs_display_count . '" name="crs_display_count" id="crs_display_count" /></p>';
	
	echo '<p>Enter max character for each post/title:<br><input  style="width: 100px;" type="text" value="';
	echo $crs_display_width . '" name="crs_display_width" id="crs_display_width" /></p>';

	echo '<p>RSS url<br><input  style="width: 500px;" type="text" value="';
	echo $crs_rss_url . '" name="crs_rss_url" id="crs_rss_url" /></p>';

	echo '<input name="crs_submit" id="crs_submit" lang="publish" class="button-primary" value="Update Setting" type="Submit" />';
	?>
	</form>
    <?php include_once("help.php"); ?>
	<?php
}


function crs_add_to_menu() 
{
	add_options_page('Continuous rss scrolling', 'Continuous rss scrolling', 'manage_options', __FILE__, 'crs_admin_options' );
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