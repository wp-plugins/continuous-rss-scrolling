<?php
/*
Plugin Name: Continuous rss scrolling
Plugin URI: http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/
Description: This plug-in will scroll the RSS title continuously in the wordpress website, we can use this plugin as a widget.
Author: Gopi Ramasamy
Version: 9.4
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
	$cnt = 0;
	$crs_count = 0;
	//$content = @file_get_contents($url);
	//if (strpos($http_response_header[0], "200")) 
	//{ 		
		$maxitems = 0;
		include_once( ABSPATH . WPINC . '/feed.php' );
		$rss = fetch_feed( $url );
		if ( ! is_wp_error( $rss ) )
		{
			$cnt = 0;
			$maxitems = $rss->get_item_quantity( 10 ); 
			$rss_items = $rss->get_items( 0, $maxitems );
			if ( $maxitems > 0 )
			{
				foreach ( $rss_items as $item )
				{
					$get_permalink = $item->get_permalink();
					$crs_post_title = $item->get_title();
					
					$crs_post_title = substr($crs_post_title, 0, $crs_display_width);
					$dis_height = $crs_record_height."px";
					$crs_html = $crs_html . "<div class='crs_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
					$crs_html = $crs_html . "<a target='_blank' href='$get_permalink'>$crs_post_title</a>";
					$crs_html = $crs_html . "</div>";
					
					$crs_post_title = trim($crs_post_title);
					$get_permalink = $get_permalink;
					$crs_x = $crs_x . "crs_array[$crs_count] = '<div class=\'crs_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a target=\'_blank\' href=\'$get_permalink\'>$crs_post_title</a></div>'; ";	
					$crs_count++;
					
					$cnt++;
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
		else
		{
			_e('No data available', 'continuous-rss-scrolling');
		}
	//}
	//else
	//{
	//	_e('RSS url is invalid or broken', 'rss-news-display');
	//}
}

function crs_shortcode( $atts ) 
{
	$crs_html = "";
	$crs_x = "";
	if ( is_array( $atts ) )
	{
		foreach(array_keys($atts) as $key)
		{
			if($key == "width")
			{
				$crs_display_width = $atts["width"];
			}
			elseif($key == "count")
			{
				$crs_display_count = $atts["count"];
			}
			elseif($key == "height")
			{
				$crs_record_height = $atts["height"];
			}
			elseif($key == "url")
			{
				$url = $atts["url"];
			}
		}
	}

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

	$xml = "";
	$cnt = 0;
	$crs_count = 0;
	$maxitems = 0;
	include_once( ABSPATH . WPINC . '/feed.php' );
	$rss = fetch_feed( $url );
	if ( ! is_wp_error( $rss ) )
	{
		$cnt = 0;
		$maxitems = $rss->get_item_quantity( 10 ); 
		$rss_items = $rss->get_items( 0, $maxitems );
		if ( $maxitems > 0 )
		{
			foreach ( $rss_items as $item )
			{
				$get_permalink = $item->get_permalink();
				$crs_post_title = $item->get_title();
				
				$crs_post_title = substr($crs_post_title, 0, $crs_display_width);
				$dis_height = $crs_record_height."px";
				$crs_html = $crs_html . "<div class='crs_div' style='height:$dis_height;padding:2px 0px 2px 0px;'>"; 
				$crs_html = $crs_html . "<a target='_blank' href='$get_permalink'>$crs_post_title</a>";
				$crs_html = $crs_html . "</div>";
				
				$crs_post_title = trim($crs_post_title);
				$get_permalink = $get_permalink;
				$crs_x = $crs_x . "crs_array[$crs_count] = '<div class=\'crs_div\' style=\'height:$dis_height;padding:2px 0px 2px 0px;\'><a target=\'_blank\' href=\'$get_permalink\'>$crs_post_title</a></div>'; ";	
				$crs_count++;
				
				$cnt++;
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
			<input name="crs_rss_url" type="text" value="<?php echo $crs_rss_url; ?>"  id="crs_rss_url" size="70">
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

class crs_widget_register extends WP_Widget 
{
	function __construct() 
	{
		$widget_ops = array('classname' => 'widget_text crs-widget', 
						'description' => __('Create the vertical scroll in the widget using given rss feed', 
							'continuous-rss-scrolling'), 'continuous-rss-scrolling');
		parent::__construct('ContinuousRssScrolling', __('Continuous rss scrolling', 'continuous-rss-scrolling'), $widget_ops);
	}
	
	function widget( $args, $instance ) 
	{
		extract( $args, EXTR_SKIP );

		$title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$width	= $instance['width'];
		$count	= $instance['count'];
		$height	= $instance['height'];
		$url	= $instance['url'];

		echo $args['before_widget'];
		if ( ! empty( $title ) )
		{
			echo $args['before_title'] . $title . $args['after_title'];
		}
		// Call widget method
		$arr = array();
		$arr["width"] 	= $width;
		$arr["count"] 	= $count;
		$arr["height"] 	= $height;
		$arr["url"] 	= $url;
		crs_shortcode($arr);
		// Call widget method
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) 
	{
		$instance 			= $old_instance;
		$instance['title'] 	= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['width'] 	= ( ! empty( $new_instance['width'] ) ) ? strip_tags( $new_instance['width'] ) : '';
		$instance['count'] 	= ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
		$instance['height'] = ( ! empty( $new_instance['height'] ) ) ? strip_tags( $new_instance['height'] ) : '';
		$instance['url'] 	= ( ! empty( $new_instance['url'] ) ) ? strip_tags( $new_instance['url'] ) : '';
		return $instance;
	}
	
	function form( $instance ) 
	{
		$defaults = array(
			'title' 	=> '',
            'width' 	=> '',
            'count' 	=> '',
            'height' 	=> '',
			'url' 		=> ''
        );
		
		$instance 	= wp_parse_args( (array) $instance, $defaults);
        $title 		= $instance['title'];
        $width 		= $instance['width'];
        $count 		= $instance['count'];
        $height 	= $instance['height'];
		$url 		= $instance['url'];
	
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('RSS url', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $url; ?>" />
		Enter your rss url.
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Display length', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
		Enter max number of character to display in the scroll.
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Display count', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" />
		Enter number of records you want to display at the same time in scroll.
        </p>
		<p>
		<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height', 'continuous-rss-scrolling'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />
		If any overlap in the scroll at front end, you should arrange(increase/decrease) this height.
        </p>
		<p><a target="_blank" href="http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/"><?php _e('click here', 'continuous-rss-scrolling'); ?></a></p>
		<?php
	}
}

function crs_widget_loading()
{
	register_widget( 'crs_widget_register' );
}

add_action('plugins_loaded', 'crs_textdomain');
add_action('init', 'crs_add_javascript_files');
add_action( 'widgets_init', 'crs_widget_loading');
register_activation_hook(__FILE__, 'crs_install');
register_deactivation_hook(__FILE__, 'crs_deactivation');
?>