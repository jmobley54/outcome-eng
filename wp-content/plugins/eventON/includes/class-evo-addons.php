<?php
/**
 * 
 * eventon addons class
 * This will be used to control everything about eventon addons
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Classes
 * @version     2.5
 */

if(class_exists('evo_addons')) return;

class evo_addons{

	private $addon_data;
	private $urls;
	private $notice_code;

	function __construct($arr=''){
		// assign initial values for instance of addon
		$this->addon_data = $arr;

		// when first time addon installed and updated from old version
		$this->addon_first_acquaintance();

		// run updater
		if(is_admin()){
			$this->updater();
		}		
	}

	// Check for eventon compatibility
		function evo_version_check(){
			global $eventon;
			
			if( version_compare($eventon->version, $this->addon_data['evo_version']) == -1 ){
				$this->notice_code = '01';
				add_action('admin_notices', array($this, 'notice'));
				return false;
			}

			return true;
		}
		public function notice(){
			if( empty($this->notice_code) ) return false;
			?>
	        <div class="message error"><p><?php printf(__('EventON %s is disabled! - '), $this->addon_data['name']); echo $this->notice_message($this->notice_code);?></p></div>
	        <?php
		}
		public function notice_message($code){
			$decypher = array(
				'01'=>	$this->addon_data['name'].' need EventON version <b>'.$this->addon_data['evo_version'].'</b> or higher to work correctly, please update EventON.',
				'02'=>	'EventON version is older than what is suggested for this addon. Please update EventON.',
			);
			return $decypher[$code];
		}

	// check if addon updated or installed
		function addon_first_acquaintance(){
			$evo_products = get_option('_evo_products');

			// new install
			if(empty($evo_products[$this->addon_data['slug']]['version'] ) ){
				// update options with new version values
				$this->update_version_number($evo_products);

			}elseif( version_compare($this->addon_data['version'], 
				$evo_products[$this->addon_data['slug']]['version']) > 0
			){
				// updating to a new version
				$this->update_version_number($evo_products);
			}
		}
		function update_version_number($products_array=''){
			$new_products = array();
			$new_products[$this->addon_data['slug']]['version']  = $this->addon_data['version'];
			$new_data = (is_array($products_array))? array_merge($products_array, $new_products): $new_products;
			update_option('_evo_products',$new_data);

			do_action('evo_addon_version_change', $this->addon_data['version']);
		}

	// return eventon version
		public function get_eventon_version(){
			global $eventon;
			return $eventon->version;
		}

	/// the MAIN updater function
		public function updater(){
			global $pagenow, $eventon;
			
			$__needed_pages = $this->get_check_pages();

			// only for admin
			if(is_admin() && !empty($pagenow) && in_array($pagenow, $__needed_pages) ){
				
				if($pagenow == 'admin.php' && isset($_GET['tab']) && $_GET['tab']=='evcal_4' 
					|| $pagenow!='admin.php'){
					
					// INITIATE Updater for addon product
					$path = AJDE_EVCAL_PATH;
					require_once( $path .'/includes/admin/class-evo-updater.php' );

					$this->evo_updater = new evo_updater( 
						array(
							'version'=>$this->addon_data['version'], 
							'slug'=>$this->addon_data['slug'],
							'plugin_slug'=>$this->addon_data['plugin_slug'],
							'name'=>$this->addon_data['name'],
							'guide_file'=> !empty($this->addon_data['guide_file'])?
								$this->addon_data['guide_file']:
								(( file_exists($this->addon_data['plugin_path'].'/guide.php') )? 
								$this->addon_data['plugin_url'].'/guide.php':null),
						)
					);	
				}
			}
		}

	// Deactivate Addon from eventon products
		public function remove_addon(){
			return evo_license()->deactivate($this->addon_data['slug']);
		}
	// return the current page names that should be used to check updates
		function get_check_pages(){
			return array('update-core.php',
				'admin-ajax.php', 'plugin-install.php','admin.php');
		}


}

?>