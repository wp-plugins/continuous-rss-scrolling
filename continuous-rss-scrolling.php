<?php

/*
Plugin Name: continuous rss scrolling
Plugin URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Description: This plug-in will scroll the RSS title in the wordpress website, <a href="http://www.gopiplus.com/work/" target="_blank">Live demo</a>.
Author: Gopi.R
Version: 1.0
Author URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Donate link: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Tags: Continuous, announcement, scroller, message, flash news, rss, xml
*/


//####################################################################################################
//###### Project   : Continuous rss scrolling  													######
//###### File Name : continuous-rss-scrolling.php                  								######
//###### Purpose   : This is the main page for this plugin.  									######
//###### Created   : Sep 4th 2010                  												######
//###### Modified  : Sep 4th 2010                  												######
//###### Author    : Gopi.R (http://www.gopiplus.com/work/)                       				######
//###### Link      : http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/  		######
//####################################################################################################


global $wpdb, $wp_version;

function crs() 
{
	
	global $wpdb;
	
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
		$url = "http://music.msn.com/rss/entnews/";
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
			$crs_html = $crs_html . "<a href='$get_permalink'>$crs_post_title</a>";
			$crs_html = $crs_html . "</div>";
			
			$crs_post_title = trim($crs_post_title);
			$get_permalink = $get_permalink;
			$crs_x = $crs_x . "crs_array[$crs_count] = '<div class=\'crs_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a href=\'$get_permalink\'>$crs_post_title</a></div>'; ";	
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
		<script type="text/javascript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/continuous-rss-scrolling/continuous-rss-scrolling.js"></script>
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
	add_option('crs_rss_url', "http://news.google.com/news?ned=us&topic=h&output=rss");
}

function crs_control() 
{
	echo '<p>Continuous rss scrolling.<br> To change the setting goto Continuous rss scrolling link under SETTINGS tab.';
	echo ' <a href="options-general.php?page=continuous-rss-scrolling/continuous-rss-scrolling.php">';
	echo 'click here</a></p>';
	?>
	<h2><?php echo wp_specialchars( 'About Plugin!' ); ?></h2>
	Plug-in created by <a target="_blank" href='http://www.gopiplus.com/work/'>Gopi</a>.<br />
	<a target="_blank" href='http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/'>Click here</a> to see More information.<br />
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
    <h2><?php echo wp_specialchars("Continuous rss scrolling"); ?></h2>
    </div>
	<?php
	$crs_title = get_option('crs_title');
	$crs_display_width = get_option('crs_display_width');
	$crs_display_count = get_option('crs_display_count');
	$crs_record_height = get_option('crs_record_height');
	$crs_rss_url = get_option('crs_rss_url');
	
	if ($_POST['crs_submit']) 
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
	add_options_page('Continuous rss scrolling', 'Continuous rss scrolling', 7, __FILE__, 'crs_admin_options' );
}

function crs_init()
{
	if(function_exists('register_sidebar_widget')) 
	{
		register_sidebar_widget('Continuous rss scrolling', 'crs_widget');
	}
	
	if(function_exists('register_widget_control')) 
	{
		register_widget_control(array('Continuous rss scrolling', 'widgets'), 'crs_control');
	} 
}

function crs_deactivation() 
{
	delete_option('crs_title');
	delete_option('crs_display_count');
	delete_option('crs_display_width');
	delete_option('crs_record_height');
}

add_action("plugins_loaded", "crs_init");
register_activation_hook(__FILE__, 'crs_install');
register_deactivation_hook(__FILE__, 'crs_deactivation');
add_action('admin_menu', 'crs_add_to_menu');
?>