<?php
/**
 * Plugin Name: Wordpress Pivto
 * Plugin URI: http://www.pivto.com/install/wordpress
 * Description: Add Pivto experience to you blog.
 * Version: 0.2
 * Author: Pivto
 * Author URI: http://www.pivto.com
 * License: GPL2
 */
class wordpressPivto{

	function wordpressPivto(){

		//ADD MENU OPTION
		add_action('admin_menu',array($this,'wordpress_pivto_menu'));

		//REGISTER PIVTO ID OPTION
		add_action( 'admin_init', array( $this , 'wordpress_pivto_settings') );

		//ADD SCRIPT TO HEAD
		add_action('wp_head', array( $this , 'wordpress_pivto_head_script' ) );
		//ADD SCRIPT TO FOOTER
		add_action('comments_template', array( $this , 'wordpress_pivto_footer_script' ) );

		//DISABLE TEMPLATE COMMENTS TO PIVTO
		add_filter('comments_template', array( $this , 'wordpress_pivto_comments_template') );

	}
		//SETTINGS FUNCTIONS
		function wordpress_pivto_settings(){
			register_setting('wordpress-pivto-group', 'setting_pivto_id');
		}

		//ADMIN PANEL FUNCTIONS
		function wordpress_pivto_menu(){
			$hook = add_menu_page('Pivto','Pivto','manage_options','wordpress-pivto',array($this,'wordpress_pivto_content'));
			add_action('load-'.$hook,array( $this , 'wordpress_pivto_settings_update') );
		}
		function wordpress_pivto_settings_update(){
			if(isset($_GET['settings-updated']) && $_GET['settings-updated']){
				if(get_option("setting_pivto_id") != '' ) {
					$this->wordpress_pivto_create_page_profiles();
					$this->wordpress_pivto_create_page_social();
				}
				//die();
		    }
		}
		function wordpress_pivto_content(){
			if(!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			settings_fields('wordpress-pivto-group');
			?>
			<div class="wrap">
				<div id="icon-link-manager" class="icon32"></div>
				<h2>Pivto Setup Pannel</h2>
				<form method="post" action="options.php">
					<?php wp_nonce_field('update-options'); ?>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="setting_pivto_id" />
					<ul>
						<li>
							<label for="pivto_id"><span>Enter you Pivto ID</span></label>
							<input type="text" maxlength="100" size="100" id="setting_pivto_id" name="setting_pivto_id" value="<?php echo get_option('setting_pivto_id'); ?>"/>
						</li>
						<li>
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Activate Pivto Experience!') ?>" />
							</p>
						</li>
						<li>
							Pivto will do : <br/>
							1 .- Deactivate default comments<br/>
							2 .- Add script between &lt;header&gt; tag<br/>
							3 .- Add script in body to pivto comments <br/>
							4 .- Create a page with name "User" <br/>
							5 .- Create a page with name "Social"
						</li>
						<?php
							if(get_option("setting_pivto_id") != ''){
								?><li><h4>Pivto Experience is active in your site!!!</h4></li><?php
							}
						?>
					</ul>
				</form>
				<p>
					<a class="button-secondary pivto_iframe" href="http://www.pivto.com/install-now/" target="_blank" title="Get you pivto ID">Dont have yet? Get you pivto ID now!</a>
					<iframe src="http://www.pivto.com/install-now/" width="100%" height="700px" id="pivto_iframe" style="display:none"></iframe>
					<script>jQuery(function($){$(".pivto_iframe").click(function(e){e.preventDefault();$("#pivto_iframe").show();});});</script>
				</p>
			</div>
			<?php
		}
		//SCRIPT HEAD FUNCTION
		function wordpress_pivto_head_script(){
				if( get_option("setting_pivto_id") != ''){
					?><!-- Start Pivto Script --><script>(function(d, t) {var g = d.createElement(t), s = d.getElementsByTagName(t)[0]; g.src = "//api.pivto.com/widgets.js?vendor_id=<?php echo get_option('setting_pivto_id'); ?>"; s.parentNode.insertBefore(g, s);}(document, 'script'));</script> <!-- End Pivto Script -->
<?php
				}
		}
		//SCRIPT FOOTER FUNCTION
		function wordpress_pivto_footer_script(){
			if( get_option("setting_pivto_id") != ''){
				?>
				<!-- Start Pivto Comments Script -->
				<div hidden="hidden" class="pv:name"><?php the_title(); ?></div>
				<div hidden="hidden" class="pv:description"><?php the_content(); ?></div>
				<div class="pivto_widget pv-comments" data-image="<?php echo get_the_post_thumbnail(); ?>" data-permalink="<?php echo get_permalink(); ?>" <?php if ( has_post_thumbnail()) { $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large'); echo 'data-image="' . $large_image_url[0] . '"'; } ?> data-objid="<?php the_ID(); ?>" ></div>
				<!-- End Pivto Comments Script -->
				<?php
			}
		}
		//CREATE PAGE PROFILES
		function wordpress_pivto_create_page_profiles(){
			if( get_option("setting_pivto_id") != ''){
				if(! get_posts(array("name"=>"users","post_type"=>"page","posts_per_page" => 1))) {
					$page = array(
							post_author => wp_get_current_user()->ID
							,post_content => '<!-- Start Pivto Profiles Script --><div class="pivto_widget pv-profile"></div><!-- End Pivto Profiles Script -->'
							,post_name => 'users'
							,post_status => 'publish'
							,post_title => ''
							,post_type => 'page'
						);
					wp_insert_post($page);
				}
			}
		}
		//CREATE PAGE SOCIAL
		function wordpress_pivto_create_page_social(){
			if( get_option("setting_pivto_id") != ''){
				if(! get_posts(array("name"=>"social","post_type"=>"page","posts_per_page" => 1))) {
					$page = array(
							post_author => ''
							,post_content => '<!-- Start Pivto Social Feed Script --><div class="pivto_widget pv-newsfeed"></div><!-- End Pivto Social Feed Script -->'
							,post_name => 'social'
							,post_status => 'publish'
							,post_title => 'Social'
							,post_type => 'page'
						);
					wp_insert_post($page);
				}
			}
		}
		//DEACTIVE DEFAULT COMMENTS
		function wordpress_pivto_comments_template($template){
			return dirname(__FILE__) . '/pivto_comments.php';
		}
}
$wordpressPivto = new wordpressPivto();
?>