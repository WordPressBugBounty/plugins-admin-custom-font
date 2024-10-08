<?php
/*
Plugin Name: Admin Custom Font
Plugin URI: http://tehnoblog.org/how-to-change-font-in-wordpress-admin-dashboard/
Description: Simple plugin to customize and replace system font(s) in WordPress Admin Dashboard.
Author: TehnoBlog.org
Author URI: http://tehnoblog.org/
Text Domain: admin-custom-font
Version: 2.5.2
*/

// Direct Access Forbidden
if(!defined('ABSPATH')) {exit;}

## CONSTANTS

define('ADMIN_CUSTOM_FONT_PLUGIN_VER', '250');
define('ADMIN_CUSTOM_FONT_PLUGIN_DIR', dirname(__FILE__));
define('ADMIN_CUSTOM_FONT_PLUGIN_CSS_DIR', ADMIN_CUSTOM_FONT_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR);

## UTILITY SECTION

function admin_custom_font_compile_css($path, $content) {
	if(empty($path)) {
		return;
	}

	// write file
	$handle = fopen($path, 'w+');
	fwrite($handle, $content);
	fclose($handle);

	// update last modified & access time
	touch($path);
}

function admin_custom_font_clear_css($path) {
	if(empty($path)) {
		return;
	}

	// write file
	$handle = fopen($path, 'w');
	fclose($handle);

	// update last modified & access time
	touch($path);
}

## UPGRADE

function admin_custom_font_update() {
	$admin_custom_font_version = get_option('admin_custom_font_version');

	if(empty($admin_custom_font_version)) {
		$admin_custom_font_version = 0;
	}

	if($admin_custom_font_version < ADMIN_CUSTOM_FONT_PLUGIN_VER) {
		admin_custom_font_activate();
	}
}
add_action('plugins_loaded', 'admin_custom_font_update');

## INSTALL

function admin_custom_font_activate() {

	// get current options (if exist)

	$admin_custom_font_version = get_option('admin_custom_font_version');
	$admin_custom_font_family  = get_option('admin_custom_font_family');
	$admin_custom_font_size    = get_option('admin_custom_font_size');
	$admin_custom_font_weight  = get_option('admin_custom_font_weight');

	// or set defaults

	if(empty($admin_custom_font_family)) {
		$admin_custom_font_family = (string) 'Open Sans';
		add_option('admin_custom_font_family', $admin_custom_font_family);
	}

	if(empty($admin_custom_font_size)) {
		$admin_custom_font_size = (string) 'default';
		add_option('admin_custom_font_size', $admin_custom_font_size);
	}

	if(empty($admin_custom_font_weight)) {
		$admin_custom_font_weight = (string) 'default';
		add_option('admin_custom_font_weight', $admin_custom_font_weight);
	}

	// construct default css options, paths & files @ install

	$font_family                        = '';
	$font_size                          = '';
	$font_weight                        = '';

	$admin_custom_font_main_css         = '';
	$admin_custom_font_login_css        = '';
	$admin_custom_font_toolbar_css      = '';

	$admin_custom_font_main_css_path    = ADMIN_CUSTOM_FONT_PLUGIN_CSS_DIR . 'admin-custom-font.css';
	$admin_custom_font_login_css_path   = ADMIN_CUSTOM_FONT_PLUGIN_CSS_DIR . 'admin-custom-font-login.css';
	$admin_custom_font_toolbar_css_path = ADMIN_CUSTOM_FONT_PLUGIN_CSS_DIR . 'admin-custom-font-toolbar.css';

	// construct default css options @ install

	if(!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') {
		$font_family = 'font-family:' . '"' . $admin_custom_font_family . '"' . ',' . 'sans-serif' . ' ' . '!important' . ';';
	}

	if(!empty($admin_custom_font_size) && $admin_custom_font_size !== 'default') {
		$font_size = 'font-size:' . $admin_custom_font_size . 'px' . ' ' . '!important' . ';';
	}

	if(!empty($admin_custom_font_weight) && $admin_custom_font_weight !== 'default') {
		$font_weight = 'font-weight:' . $admin_custom_font_weight . ' ' . '!important' . ';';
	}

	// construct default css templates @ install

	if(!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') {
		$admin_custom_font_main_css = 'body,#wpadminbar *:not([class="ab-icon"]),.wp-core-ui,.media-menu,.media-frame *,.media-modal *,.code, code, input, select, textarea *:not([class="wp-editor-area"]), button.button,button.primary{' . $font_family . '}';
	}

	if(!empty($font_size) || !empty($font_weight)) {
		$admin_custom_font_main_css .= 'body, div, a, span, td, button, input *:not([id="titlediv"]), select, p, li, strong, .form-table *{' . $font_size . $font_weight . '}';
		$admin_custom_font_main_css .= PHP_EOL . 'select{line-height:initial !important; height:initial !important;' . '}';
		if ($admin_custom_font_size >= 15) {
		$admin_custom_font_main_css .= PHP_EOL . '#adminmenu span.awaiting-mod > span.plugin-count, #adminmenu span.update-plugins > span.plugin-count{line-height:normal; vertical-align:inherit;}';
		}
	}

	if( (!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') || (!empty($admin_custom_font_size) && $admin_custom_font_size !== 'default') || (!empty($admin_custom_font_weight) && $admin_custom_font_weight !== 'default') ) {
		$admin_custom_font_login_css = 'body*:not([class="dashicons"]),.wp-core-ui *:not([class="dashicons"]){' . $font_family . $font_size . $font_weight . '}';
		$admin_custom_font_toolbar_css = '#wpadminbar *:not([class="ab-icon"]){' . $font_family . $font_size . $font_weight . '}';
	}

	// compile default css files @ install

	admin_custom_font_compile_css($admin_custom_font_main_css_path, $admin_custom_font_main_css);
	admin_custom_font_compile_css($admin_custom_font_login_css_path, $admin_custom_font_login_css);
	admin_custom_font_compile_css($admin_custom_font_toolbar_css_path, $admin_custom_font_toolbar_css);

	// set plugin version

	if(empty($admin_custom_font_version)) {
		add_option('admin_custom_font_version', ADMIN_CUSTOM_FONT_PLUGIN_VER);
	} else {
		update_option('admin_custom_font_version', ADMIN_CUSTOM_FONT_PLUGIN_VER);
	}

}
register_activation_hook(__FILE__, 'admin_custom_font_activate');

## UNINSTALL

function admin_custom_font_uninstall() {
	delete_option('admin_custom_font_version');
	delete_option('admin_custom_font_family');
	delete_option('admin_custom_font_size');
	delete_option('admin_custom_font_weight');
}
register_uninstall_hook(__FILE__, 'admin_custom_font_uninstall');

## LANGUAGE TRANSLATIONS

function admin_custom_font_language_init() {
	$lang_dir = basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lang';
	load_plugin_textdomain('admin-custom-font', false, $lang_dir);
}
add_action('plugins_loaded', 'admin_custom_font_language_init');

## ADMIN SECTION

// create admin page
function admin_custom_font_settings_menu() {
	add_options_page('Admin Custom Font Menu', 'Admin Font', 'manage_options', 'admin-custom-font-settings', 'admin_custom_font_options');
}
add_action('admin_menu', 'admin_custom_font_settings_menu');

// admin page options
function admin_custom_font_options() {
	// user privileges check
	if(!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.'));
	}

	// set variables
	$admin_custom_font_family = '';
	$admin_custom_font_size   = '';
	$admin_custom_font_weight = '';

	# UPDATE SETTINGS

	if(isset($_POST) && !empty($_POST)) {
		$admin_custom_font_nonce = $_POST['admin_custom_font_nonce_input_txt'];

		if(!wp_verify_nonce($admin_custom_font_nonce, 'admin_custom_font_nonce')) {
			wp_die(__('Security Check Failed. Please log out and log in back into WordPress.', 'admin-custom-font'));
		}

		$admin_custom_font_family = ((isset($_POST['admin_custom_font_family_option']) && !empty($_POST['admin_custom_font_family_option'])) ? $_POST['admin_custom_font_family_option'] : '');
		$admin_custom_font_size   = ((isset($_POST['admin_custom_font_size_option'])   && !empty($_POST['admin_custom_font_size_option'])  ) ? $_POST['admin_custom_font_size_option']   : '');
		$admin_custom_font_weight = ((isset($_POST['admin_custom_font_weight_option']) && !empty($_POST['admin_custom_font_weight_option'])) ? $_POST['admin_custom_font_weight_option'] : '');

		// update font family
		if(isset($admin_custom_font_family) && !empty($admin_custom_font_family)) {
			update_option('admin_custom_font_family', (string) $admin_custom_font_family);
		}

		// update font size
		if(isset($admin_custom_font_size) && !empty($admin_custom_font_size)) {
			update_option('admin_custom_font_size', (string) $admin_custom_font_size);
		}

		// update font weight
		if(isset($admin_custom_font_weight) && !empty($admin_custom_font_weight)) {
			update_option('admin_custom_font_weight', (string) $admin_custom_font_weight);
		}

		# COMPILE CSS FILES

		// construct css options, files & paths

		$font_family                        = '';
		$font_size                          = '';
		$font_weight                        = '';

		$admin_custom_font_main_css         = '';
		$admin_custom_font_login_css        = '';
		$admin_custom_font_toolbar_css      = '';

		$admin_custom_font_main_css_path    = ADMIN_CUSTOM_FONT_PLUGIN_CSS_DIR . 'admin-custom-font.css';
		$admin_custom_font_login_css_path   = ADMIN_CUSTOM_FONT_PLUGIN_CSS_DIR . 'admin-custom-font-login.css';
		$admin_custom_font_toolbar_css_path = ADMIN_CUSTOM_FONT_PLUGIN_CSS_DIR . 'admin-custom-font-toolbar.css';

		// construct css options

		if(!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') {
			$font_family = 'font-family:' . '"' . $admin_custom_font_family . '"' . ',' . 'sans-serif' . ' ' . '!important' . ';';
		}

		if(!empty($admin_custom_font_size) && $admin_custom_font_size !== 'default') {
			$font_size = 'font-size:' . $admin_custom_font_size . 'px' . ' ' . '!important' . ';';
		}

		if(!empty($admin_custom_font_weight) && $admin_custom_font_weight !== 'default') {
			$font_weight = 'font-weight:' . $admin_custom_font_weight . ' ' . '!important' . ';';
		}

		// construct templates

		if(!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') {
			$admin_custom_font_main_css = 'body,#wpadminbar *:not([class="ab-icon"]),.wp-core-ui,.media-menu,.media-frame *,.media-modal *,.code, code, input, select, textarea *:not([class="wp-editor-area"]), button.button,button.primary{' . $font_family . '}';
		}

		if(!empty($font_size) || !empty($font_weight)) {
			$admin_custom_font_main_css .= 'body, div, a, span, td, button, input *:not([id="titlediv"]), select, p, li, strong, .form-table *{' . $font_size . $font_weight . '}';
			$admin_custom_font_main_css .= PHP_EOL . 'select{line-height:initial !important; height:initial !important;' . '}';
			if ($admin_custom_font_size >= 15) {
			$admin_custom_font_main_css .= PHP_EOL . '#adminmenu span.awaiting-mod > span.plugin-count, #adminmenu span.update-plugins > span.plugin-count{line-height:normal; vertical-align:inherit;}';
			}
		}

		if( (!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') || (!empty($admin_custom_font_size) && $admin_custom_font_size !== 'default') || (!empty($admin_custom_font_weight) && $admin_custom_font_weight !== 'default') ) {
			$admin_custom_font_login_css = 'body*:not([class="dashicons"]),.wp-core-ui *:not([class="dashicons"]){' . $font_family . $font_size . $font_weight . '}';
			$admin_custom_font_toolbar_css = '#wpadminbar *:not([class="ab-icon"]){' . $font_family . $font_size . $font_weight . '}';
		}

		// compile css files

		admin_custom_font_compile_css($admin_custom_font_main_css_path, $admin_custom_font_main_css);
		admin_custom_font_compile_css($admin_custom_font_login_css_path, $admin_custom_font_login_css);
		admin_custom_font_compile_css($admin_custom_font_toolbar_css_path, $admin_custom_font_toolbar_css);
	}

?>

<div #id="admin-custom-font-wrapper">
	<h1>Admin Custom Font</h1>

	<?php
	// get plugin options
	$admin_custom_font_family = get_option('admin_custom_font_family');
	$admin_custom_font_size   = get_option('admin_custom_font_size');
	$admin_custom_font_weight = get_option('admin_custom_font_weight');
	?>

	<form id="admin_custom_font_form" name="admin_custom_font_form" method="post">
		<div id="form-wrapper" style="display:block; clear:both; margin:20px 0;">
			<label style="display:block; clear:both; color:#F00;" for="admin_custom_font_family_option"><?php _e('Font Family', 'admin-custom-font'); ?></label>
			<select style="width:50%; min-width:200px; max-width:500px;" id="admin_custom_font_family_option" name="admin_custom_font_family_option">
				<optgroup label="Default">
					<option value="default"><?php _e('WordPress Default', 'admin-custom-font'); ?></option>
				</optgroup>

				<optgroup label="Standard">
					<option value="Open Sans" <?php if($admin_custom_font_family == 'Open Sans') { echo 'selected="selected"'; } ?> >Open Sans (Google Font)</option>
					<option value="Droid Sans" <?php if($admin_custom_font_family == 'Droid Sans') { echo 'selected="selected"'; } ?> >Droid Sans (Google Font)</option>
					<option value="Work Sans" <?php if($admin_custom_font_family == 'Work Sans') { echo 'selected="selected"'; } ?> >Work Sans (Google Font)</option>
					<option value="Prompt" <?php if($admin_custom_font_family == 'Prompt') { echo 'selected="selected"'; } ?> >Prompt (Google Font)</option>
					<option value="Roboto" <?php if($admin_custom_font_family == 'Roboto') { echo 'selected="selected"'; } ?> >Roboto (Google Font)</option>
					<option value="Ubuntu" <?php if($admin_custom_font_family == 'Ubuntu') { echo 'selected="selected"'; } ?> >Ubuntu (Google Font)</option>
				</optgroup>

				<optgroup label="Tech">
					<option value="Exo" <?php if($admin_custom_font_family == 'Exo') { echo 'selected="selected"'; } ?> >Exo (Google Font)</option>
					<option value="Play" <?php if($admin_custom_font_family == 'Play') { echo 'selected="selected"'; } ?> >Play (Google Font)</option>
					<option value="Jura" <?php if($admin_custom_font_family == 'Jura') { echo 'selected="selected"'; } ?> >Jura (Google Font)</option>
					<option value="Orbitron" <?php if($admin_custom_font_family == 'Orbitron') { echo 'selected="selected"'; } ?> >Orbitron (Google Font)</option>
					<option value="Quantico" <?php if($admin_custom_font_family == 'Quantico') { echo 'selected="selected"'; } ?> >Quantico (Google Font)</option>
					<option value="Electrolize" <?php if($admin_custom_font_family == 'Electrolize') { echo 'selected="selected"'; } ?> >Electrolize (Google Font)</option>
					<option value="Titillium Web" <?php if($admin_custom_font_family == 'Titillium Web') { echo 'selected="selected"'; } ?> >Titillium Web (Google Font)</option>
					<option value="Share Tech" <?php if($admin_custom_font_family == 'Share Tech') { echo 'selected="selected"'; } ?> >Share Tech (Google Font)</option>
					<option value="Share Tech Mono" <?php if($admin_custom_font_family == 'Share Tech Mono') { echo 'selected="selected"'; } ?> >Share Tech Mono (Google Font)</option>
				</optgroup>

				<optgroup label="Misc">
					<option value="Rubik" <?php if($admin_custom_font_family == 'Rubik') { echo 'selected="selected"'; } ?> >Rubik (Google Font)</option>
					<option value="Maven Pro" <?php if($admin_custom_font_family == 'Maven Pro') { echo 'selected="selected"'; } ?> >Maven Pro (Google Font)</option>
					<option value="Advent Pro" <?php if($admin_custom_font_family == 'Advent Pro') { echo 'selected="selected"'; } ?> >Advent Pro (Google Font)</option>
					<option value="Lato" <?php if($admin_custom_font_family == 'Lato') { echo 'selected="selected"'; } ?> >Lato (Google Font)</option>
					<option value="Audiowide" <?php if($admin_custom_font_family == 'Audiowide') { echo 'selected="selected"'; } ?> >Audiowide (Google Font)</option>
					<option value="Aldrich" <?php if($admin_custom_font_family == 'Aldrich') { echo 'selected="selected"'; } ?> >Aldrich (Google Font)</option>
					<option value="Nunito" <?php if($admin_custom_font_family == 'Nunito') { echo 'selected="selected"'; } ?> >Nunito (Google Font)</option>
					<option value="Cabins" <?php if($admin_custom_font_family == 'Cabins') { echo 'selected="selected"'; } ?> >Cabins (Google Font)</option>
					<option value="Poppins" <?php if($admin_custom_font_family == 'Poppins') { echo 'selected="selected"'; } ?> >Poppins (Google Font)</option>
					<option value="Montserrat" <?php if($admin_custom_font_family == 'Montserrat') { echo 'selected="selected"'; } ?> >Montserrat (Google Font)</option>
					<option value="Open Sans Condensed" <?php if($admin_custom_font_family == 'Open Sans Condensed') { echo 'selected="selected"'; } ?> >Open Sans Condensed (Google Font)</option>
					<option value="Source Sans Pro" <?php if($admin_custom_font_family == 'Source Sans Pro') { echo 'selected="selected"'; } ?> >Source Sans Pro (Google Font)</option>
				</optgroup>

				<optgroup label="Artistic">
					<option value="Sofia" <?php if($admin_custom_font_family == 'Sofia') { echo 'selected="selected"'; } ?> >Sofia (Google Font)</option>
					<option value="Spirax" <?php if($admin_custom_font_family == 'Spirax') { echo 'selected="selected"'; } ?> >Spirax (Google Font)</option>
					<option value="Paprika" <?php if($admin_custom_font_family == 'Paprika') { echo 'selected="selected"'; } ?> >Paprika (Google Font)</option>
					<option value="Playball" <?php if($admin_custom_font_family == 'Playball') { echo 'selected="selected"'; } ?> >Playball (Google Font)</option>
					<option value="Indie Flower" <?php if($admin_custom_font_family == 'Indie Flower') { echo 'selected="selected"'; } ?> >Indie Flower (Google Font)</option>
					<option value="Crafty Girls" <?php if($admin_custom_font_family == 'Crafty Girls') { echo 'selected="selected"'; } ?> >Crafty Girls (Google Font)</option>
					<option value="Architects Daughter" <?php if($admin_custom_font_family == 'Architects Daughter') { echo 'selected="selected"'; } ?> >Architects Daughter (Google Font)</option>
				</optgroup>

				<optgroup label="Alphabetical (ALL)">
				<?php
				$aGoogleFontsTop = array('Open Sans','Droid Sans','Work Sans','Prompt','Roboto','Ubuntu','Exo','Play','Jura','Orbitron','Quantico','Electrolize','Titillium Web','Share Tech','Share Tech Mono','Rubik','Maven Pro','Advent Pro','Lato','Audiowide','Aldrich','Nunito','Cabins','Poppins','Montserrat','Open Sans Condensed','Source Sans Pro','Sofia','Spirax','Paprika','Playball','Indie Flower','Crafty Girls','Architects Daughter');
				$aGoogleFontsAll = array('ABeeZee','Abel','Abhaya Libre','Abril Fatface','Aclonica','Acme','Actor','Adamina','Advent Pro','Aguafina Script','Akaya Kanadaka','Akaya Telivigala','Akronim','Aladin','Alata','Alatsi','Aldrich','Alef','Alegreya','Alegreya SC','Alegreya Sans','Alegreya Sans SC','Aleo','Alex Brush','Alfa Slab One','Alice','Alike','Alike Angular','Allan','Allerta','Allerta Stencil','Allison','Allura','Almarai','Almendra','Almendra Display','Almendra SC','Alumni Sans','Amarante','Amaranth','Amatic SC','Amethysta','Amiko','Amiri','Amita','Anaheim','Andada Pro','Andika','Andika New Basic','Angkor','Annie Use Your Telescope','Anonymous Pro','Antic','Antic Didone','Antic Slab','Anton','Antonio','Arapey','Arbutus','Arbutus Slab','Architects Daughter','Archivo','Archivo Black','Archivo Narrow','Are You Serious','Aref Ruqaa','Arima Madurai','Arimo','Arizonia','Armata','Arsenal','Artifika','Arvo','Arya','Asap','Asap Condensed','Asar','Asset','Assistant','Astloch','Asul','Athiti','Atkinson Hyperlegible','Atma','Atomic Age','Aubrey','Audiowide','Autour One','Average','Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Azeret Mono','B612','B612 Mono','Bad Script','Bahiana','Bahianita','Bai Jamjuree','Ballet','Baloo 2','Baloo Bhai 2','Baloo Bhaina 2','Baloo Chettan 2','Baloo Da 2','Baloo Paaji 2','Baloo Tamma 2','Baloo Tammudu 2','Baloo Thambi 2','Balsamiq Sans','Balthazar','Bangers','Barlow','Barlow Condensed','Barlow Semi Condensed','Barriecito','Barrio','Basic','Baskervville','Battambang','Baumans','Bayon','Be Vietnam','Be Vietnam Pro','Bebas Neue','Belgrano','Bellefair','Belleza','Bellota','Bellota Text','BenchNine','Benne','Bentham','Berkshire Swash','Besley','Beth Ellen','Bevan','Big Shoulders Display','Big Shoulders Inline Display','Big Shoulders Inline Text','Big Shoulders Stencil Display','Big Shoulders Stencil Text','Big Shoulders Text','Bigelow Rules','Bigshot One','Bilbo','Bilbo Swash Caps','BioRhyme','BioRhyme Expanded','Birthstone','Birthstone Bounce','Biryani','Bitter','Black And White Picture','Black Han Sans','Black Ops One','Blinker','Bodoni Moda','Bokor','Bona Nova','Bonbon','Bonheur Royale','Boogaloo','Bowlby One','Bowlby One SC','Brawler','Bree Serif','Brygada 1918','Bubblegum Sans','Bubbler One','Buda','Buenard','Bungee','Bungee Hairline','Bungee Inline','Bungee Outline','Bungee Shade','Butcherman','Butterfly Kids','Cabin','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Cairo','Caladea','Calistoga','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One','Capriola','Caramel','Carattere','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One','Castoro','Catamaran','Caudex','Caveat','Caveat Brush','Cedarville Cursive','Ceviche One','Chakra Petch','Changa','Changa One','Chango','Charm','Charmonman','Chathura','Chau Philomene One','Chela One','Chelsea Market','Chenla','Cherish','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','Chilanka','Chivo','Chonburi','Cinzel','Cinzel Decorative','Clicker Script','Coda','Coda Caption','Codystar','Coiny','Combo','Comfortaa','Comic Neue','Coming Soon','Commissioner','Concert One','Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Cormorant','Cormorant Garamond','Cormorant Infant','Cormorant SC','Cormorant Unicase','Cormorant Upright','Courgette','Courier Prime','Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Pro','Crimson Text','Croissant One','Crushed','Cuprum','Cute Font','Cutive','Cutive Mono','DM Mono','DM Sans','DM Serif Display','DM Serif Text','Damion','Dancing Script','Dangrek','Darker Grotesque','David Libre','Dawning of a New Day','Days One','Dekko','Dela Gothic One','Delius','Delius Swash Caps','Delius Unicase','Della Respira','Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Do Hyeon','Dokdo','Domine','Donegal One','Doppio One','Dorsa','Dosis','DotGothic16','Dr Sugiyama','Duru Sans','Dynalight','EB Garamond','Eagle Lake','East Sea Dokdo','Eater','Economica','Eczar','El Messiri','Electrolize','Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Encode Sans','Encode Sans Condensed','Encode Sans Expanded','Encode Sans SC','Encode Sans Semi Condensed','Encode Sans Semi Expanded','Engagement','Englebert','Enriqueta','Ephesis','Epilogue','Erica One','Esteban','Euphoria Script','Ewert','Exo','Exo 2','Expletus Sans','Explora','Fahkwang','Fanwood Text','Farro','Farsan','Fascinate','Fascinate Inline','Faster One','Fasthand','Fauna One','Faustina','Federant','Federo','Felipa','Fenix','Festive','Finger Paint','Fira Code','Fira Mono','Fira Sans','Fira Sans Condensed','Fira Sans Extra Condensed','Fjalla One','Fjord One','Flamenco','Flavors','Fleur De Leah','Fondamento','Fontdiner Swanky','Forum','Francois One','Frank Ruhl Libre','Fraunces','Freckle Face','Fredericka the Great','Fredoka One','Freehand','Fresca','Frijole','Fruktur','Fugaz One','Fuggles','GFS Didot','GFS Neohellenic','Gabriela','Gaegu','Gafata','Galada','Galdeano','Galindo','Gamja Flower','Gayathri','Gelasio','Gemunu Libre','Gentium Basic','Gentium Book Basic','Geo','Georama','Geostar','Geostar Fill','Germania One','Gideon Roman','Gidugu','Gilda Display','Girassol','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Glory','Gluten','Goblin One','Gochi Hand','Goldman','Gorditas','Gothic A1','Gotu','Goudy Bookletter 1911','Gowun Batang','Gowun Dodum','Graduate','Grand Hotel','Grandstander','Gravitas One','Great Vibes','Grechen Fuemen','Grenze','Grenze Gotisch','Grey Qo','Griffy','Gruppo','Gudea','Gugi','Gupter','Gurajada','Habibi','Hachi Maru Pop','Hahmlet','Halant','Hammersmith One','Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Harmattan','Headland One','Heebo','Henny Penny','Hepta Slab','Herr Von Muellerhoff','Hi Melody','Hina Mincho','Hind','Hind Guntur','Hind Madurai','Hind Siliguri','Hind Vadodara','Holtwood One SC','Homemade Apple','Homenaje','IBM Plex Mono','IBM Plex Sans','IBM Plex Sans Arabic','IBM Plex Sans Condensed','IBM Plex Sans Devanagari','IBM Plex Sans Hebrew','IBM Plex Sans KR','IBM Plex Sans Thai','IBM Plex Sans Thai Looped','IBM Plex Serif','IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English','IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer','IM Fell Great Primer SC','Ibarra Real Nova','Iceberg','Iceland','Imbue','Imprima','Inconsolata','Inder','Indie Flower','Inika','Inknut Antiqua','Inria Sans','Inria Serif','Inter','Irish Grover','Istok Web','Italiana','Italianno','Itim','Jacques Francois','Jacques Francois Shadow','Jaldi','JetBrains Mono','Jim Nightshade','Jockey One','Jolly Lodger','Jomhuria','Jomolhari','Josefin Sans','Josefin Slab','Jost','Joti One','Jua','Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here','K2D','Kadwa','Kaisei Decol','Kaisei HarunoUmi','Kaisei Opti','Kaisei Tokumin','Kalam','Kameron','Kanit','Kantumruy','Karantina','Karla','Karma','Katibeh','Kaushan Script','Kavivanar','Kavoon','Kdam Thmor','Keania One','Kelly Slab','Kenia','Khand','Khmer','Khula','Kirang Haerang','Kite One','Kiwi Maru','Klee One','Knewave','KoHo','Kodchasan','Koh Santepheap','Kosugi','Kosugi Maru','Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Krub','Kufam','Kulim Park','Kumar One','Kumar One Outline','Kumbh Sans','Kurale','La Belle Aurore','Lacquer','Laila','Lakki Reddy','Lalezar','Lancelot','Langar','Lateef','Lato','League Script','Leckerli One','Ledger','Lekton','Lemon','Lemonada','Lexend','Lexend Deca','Lexend Exa','Lexend Giga','Lexend Mega','Lexend Peta','Lexend Tera','Lexend Zetta','Libre Barcode 128','Libre Barcode 128 Text','Libre Barcode 39','Libre Barcode 39 Extended','Libre Barcode 39 Extended Text','Libre Barcode 39 Text','Libre Barcode EAN13 Text','Libre Baskerville','Libre Caslon Display','Libre Caslon Text','Libre Franklin','Life Savers','Lilita One','Lily Script One','Limelight','Linden Hill','Literata','Liu Jian Mao Cao','Livvic','Lobster','Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Long Cang','Lora','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy','Lusitana','Lustria','M PLUS 1p','M PLUS Rounded 1c','Ma Shan Zheng','Macondo','Macondo Swash Caps','Mada','Magra','Maiden Orange','Maitree','Major Mono Display','Mako','Mali','Mallanna','Mandali','Manjari','Manrope','Mansalva','Manuale','Marcellus','Marcellus SC','Marck Script','Margarine','Markazi Text','Marko One','Marmelad','Martel','Martel Sans','Marvel','Mate','Mate SC','Material Icons','Maven Pro','McLaren','Meddon','MedievalSharp','Medula One','Meera Inimai','Megrim','Meie Script','Merienda','Merienda One','Merriweather','Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga','Miltonian','Miltonian Tattoo','Mina','Miniver','Miriam Libre','Mirza','Miss Fajardose','Mitr','Modak','Modern Antiqua','Mogra','Molengo','Molle','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','MonteCarlo','Montez','Montserrat','Montserrat Alternates','Montserrat Subrayada','Moul','Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mr De Haviland','Mrs Saint Delafield','Mrs Sheppards','Mukta','Mukta Mahee','Mukta Malar','Mukta Vaani','Mulish','MuseoModerno','Mystery Quest','NTR','Nanum Brush Script','Nanum Gothic','Nanum Gothic Coding','Nanum Myeongjo','Nanum Pen Script','Nerko One','Neucha','Neuton','New Rocker','New Tegomin','News Cycle','Newsreader','Niconne','Niramit','Nixie One','Nobile','Nokora','Norican','Nosifer','Notable','Nothing You Could Do','Noticia Text','Noto Kufi Arabic','Noto Music','Noto Naskh Arabic','Noto Nastaliq Urdu','Noto Rashi Hebrew','Noto Sans','Noto Sans Adlam','Noto Sans Adlam Unjoined','Noto Sans Anatolian Hieroglyphs','Noto Sans Arabic','Noto Sans Armenian','Noto Sans Avestan','Noto Sans Balinese','Noto Sans Bamum','Noto Sans Bassa Vah','Noto Sans Batak','Noto Sans Bengali','Noto Sans Bhaiksuki','Noto Sans Brahmi','Noto Sans Buginese','Noto Sans Buhid','Noto Sans Canadian Aboriginal','Noto Sans Carian','Noto Sans Caucasian Albanian','Noto Sans Chakma','Noto Sans Cham','Noto Sans Cherokee','Noto Sans Coptic','Noto Sans Cuneiform','Noto Sans Cypriot','Noto Sans Deseret','Noto Sans Devanagari','Noto Sans Display','Noto Sans Duployan','Noto Sans Egyptian Hieroglyphs','Noto Sans Elbasan','Noto Sans Elymaic','Noto Sans Georgian','Noto Sans Glagolitic','Noto Sans Gothic','Noto Sans Grantha','Noto Sans Gujarati','Noto Sans Gunjala Gondi','Noto Sans Gurmukhi','Noto Sans HK','Noto Sans Hanifi Rohingya','Noto Sans Hanunoo','Noto Sans Hatran','Noto Sans Hebrew','Noto Sans Imperial Aramaic','Noto Sans Indic Siyaq Numbers','Noto Sans Inscriptional Pahlavi','Noto Sans Inscriptional Parthian','Noto Sans JP','Noto Sans Javanese','Noto Sans KR','Noto Sans Kaithi','Noto Sans Kannada','Noto Sans Kayah Li','Noto Sans Kharoshthi','Noto Sans Khmer','Noto Sans Khojki','Noto Sans Khudawadi','Noto Sans Lao','Noto Sans Lepcha','Noto Sans Limbu','Noto Sans Linear A','Noto Sans Linear B','Noto Sans Lisu','Noto Sans Lycian','Noto Sans Lydian','Noto Sans Mahajani','Noto Sans Malayalam','Noto Sans Mandaic','Noto Sans Manichaean','Noto Sans Marchen','Noto Sans Masaram Gondi','Noto Sans Math','Noto Sans Mayan Numerals','Noto Sans Medefaidrin','Noto Sans Meroitic','Noto Sans Miao','Noto Sans Modi','Noto Sans Mongolian','Noto Sans Mono','Noto Sans Mro','Noto Sans Multani','Noto Sans Myanmar','Noto Sans N Ko','Noto Sans Nabataean','Noto Sans New Tai Lue','Noto Sans Newa','Noto Sans Nushu','Noto Sans Ogham','Noto Sans Ol Chiki','Noto Sans Old Hungarian','Noto Sans Old Italic','Noto Sans Old North Arabian','Noto Sans Old Permic','Noto Sans Old Persian','Noto Sans Old Sogdian','Noto Sans Old South Arabian','Noto Sans Old Turkic','Noto Sans Oriya','Noto Sans Osage','Noto Sans Osmanya','Noto Sans Pahawh Hmong','Noto Sans Palmyrene','Noto Sans Pau Cin Hau','Noto Sans Phags Pa','Noto Sans Phoenician','Noto Sans Psalter Pahlavi','Noto Sans Rejang','Noto Sans Runic','Noto Sans SC','Noto Sans Samaritan','Noto Sans Saurashtra','Noto Sans Sharada','Noto Sans Shavian','Noto Sans Siddham','Noto Sans Sinhala','Noto Sans Sogdian','Noto Sans Sora Sompeng','Noto Sans Soyombo','Noto Sans Sundanese','Noto Sans Syloti Nagri','Noto Sans Symbols','Noto Sans Symbols 2','Noto Sans Syriac','Noto Sans TC','Noto Sans Tagalog','Noto Sans Tagbanwa','Noto Sans Tai Le','Noto Sans Tai Tham','Noto Sans Tai Viet','Noto Sans Takri','Noto Sans Tamil','Noto Sans Tamil Supplement','Noto Sans Telugu','Noto Sans Thaana','Noto Sans Thai','Noto Sans Thai Looped','Noto Sans Tifinagh','Noto Sans Tirhuta','Noto Sans Ugaritic','Noto Sans Vai','Noto Sans Wancho','Noto Sans Warang Citi','Noto Sans Yi','Noto Sans Zanabazar Square','Noto Serif','Noto Serif Ahom','Noto Serif Armenian','Noto Serif Balinese','Noto Serif Bengali','Noto Serif Devanagari','Noto Serif Display','Noto Serif Dogra','Noto Serif Ethiopic','Noto Serif Georgian','Noto Serif Grantha','Noto Serif Gujarati','Noto Serif Gurmukhi','Noto Serif Hebrew','Noto Serif JP','Noto Serif KR','Noto Serif Kannada','Noto Serif Khmer','Noto Serif Lao','Noto Serif Malayalam','Noto Serif Myanmar','Noto Serif Nyiakeng Puachue Hmong','Noto Serif SC','Noto Serif Sinhala','Noto Serif TC','Noto Serif Tamil','Noto Serif Tangut','Noto Serif Telugu','Noto Serif Thai','Noto Serif Tibetan','Noto Serif Yezidi','Noto Traditional Nushu','Nova Cut','Nova Flat','Nova Mono','Nova Oval','Nova Round','Nova Script','Nova Slim','Nova Square','Numans','Nunito','Nunito Sans','Odibee Sans','Odor Mean Chey','Offside','Oi','Old Standard TT','Oldenburg','Oleo Script','Oleo Script Swash Caps','Open Sans','Open Sans Condensed','Oranienbaum','Orbitron','Oregano','Orelega One','Orienta','Original Surfer','Oswald','Otomanopee One','Over the Rainbow','Overlock','Overlock SC','Overpass','Overpass Mono','Ovo','Oxanium','Oxygen','Oxygen Mono','PT Mono','PT Sans','PT Sans Caption','PT Sans Narrow','PT Serif','PT Serif Caption','Pacifico','Padauk','Palanquin','Palanquin Dark','Palette Mosaic','Pangolin','Paprika','Parisienne','Passero One','Passion One','Pathway Gothic One','Patrick Hand','Patrick Hand SC','Pattaya','Patua One','Pavanam','Paytone One','Peddana','Peralta','Permanent Marker','Petit Formal Script','Petrona','Philosopher','Piazzolla','Piedra','Pinyon Script','Pirata One','Plaster','Play','Playball','Playfair Display','Playfair Display SC','Podkova','Poiret One','Poller One','Poly','Pompiere','Pontano Sans','Poor Story','Poppins','Port Lligat Sans','Port Lligat Slab','Potta One','Pragati Narrow','Prata','Preahvihear','Press Start 2P','Pridi','Princess Sofia','Prociono','Prompt','Prosto One','Proza Libre','Public Sans','Puritan','Purple Purse','Qahiri','Quando','Quantico','Quattrocento','Quattrocento Sans','Questrial','Quicksand','Quintessential','Qwigley','Racing Sans One','Radley','Rajdhani','Rakkas','Raleway','Raleway Dots','Ramabhadra','Ramaraja','Rambla','Rammetto One','Rampart One','Ranchers','Rancho','Ranga','Rasa','Rationale','Ravi Prakash','Recursive','Red Hat Display','Red Hat Text','Red Rose','Redressed','Reem Kufi','Reenie Beanie','Reggae One','Revalia','Rhodium Libre','Ribeye','Ribeye Marrow','Righteous','Risque','Roboto','Roboto Condensed','Roboto Mono','Roboto Slab','Rochester','Rock Salt','RocknRoll One','Rokkitt','Romanesco','Ropa Sans','Rosario','Rosarivo','Rouge Script','Rowdies','Rozha One','Rubik','Rubik Beastly','Rubik Mono One','Ruda','Rufina','Ruge Boogie','Ruluko','Rum Raisin','Ruslan Display','Russo One','Ruthie','Rye','STIX Two Text','Sacramento','Sahitya','Sail','Saira','Saira Condensed','Saira Extra Condensed','Saira Semi Condensed','Saira Stencil One','Salsa','Sanchez','Sancreek','Sansita','Sansita Swashed','Sarabun','Sarala','Sarina','Sarpanch','Satisfy','Sawarabi Gothic','Sawarabi Mincho','Scada','Scheherazade','Scheherazade New','Schoolbell','Scope One','Seaweed Script','Secular One','Sedgwick Ave','Sedgwick Ave Display','Sen','Sevillana','Seymour One','Shadows Into Light','Shadows Into Light Two','Shanti','Share','Share Tech','Share Tech Mono','Shippori Mincho','Shippori Mincho B1','Shojumaru','Short Stack','Shrikhand','Siemreap','Sigmar One','Signika','Signika Negative','Simonetta','Single Day','Sintony','Sirin Stencil','Six Caps','Skranji','Slabo 13px','Slabo 27px','Slackey','Smokum','Smythe','Sniglet','Snippet','Snowburst One','Sofadi One','Sofia','Solway','Song Myung','Sonsie One','Sora','Sorts Mill Goudy','Source Code Pro','Source Sans Pro','Source Serif Pro','Space Grotesk','Space Mono','Spartan','Special Elite','Spectral','Spectral SC','Spicy Rice','Spinnaker','Spirax','Squada One','Sree Krushnadevaraya','Sriracha','Srisakdi','Staatliches','Stalemate','Stalinist One','Stardos Stencil','Stick','Stick No Bills','Stint Ultra Condensed','Stint Ultra Expanded','Stoke','Strait','Style Script','Stylish','Sue Ellen Francisco','Suez One','Sulphur Point','Sumana','Sunflower','Sunshiney','Supermercado One','Sura','Suranna','Suravaram','Suwannaphum','Swanky and Moo Moo','Syncopate','Syne','Syne Mono','Syne Tactile','Tajawal','Tangerine','Taprom','Tauri','Taviraj','Teko','Telex','Tenali Ramakrishna','Tenor Sans','Text Me One','Texturina','Thasadith','The Girl Next Door','Tienne','Tillana','Timmana','Tinos','Titan One','Titillium Web','Tomorrow','Tourney','Trade Winds','Train One','Trirong','Trispace','Trocchi','Trochut','Truculenta','Trykker','Tulpen One','Turret Road','Ubuntu','Ubuntu Condensed','Ubuntu Mono','Uchen','Ultra','Uncial Antiqua','Underdog','Unica One','UnifrakturCook','UnifrakturMaguntia','Unkempt','Unlock','Unna','Urbanist','VT323','Vampiro One','Varela','Varela Round','Varta','Vast Shadow','Vesper Libre','Viaoda Libre','Vibes','Vibur','Vidaloka','Viga','Voces','Volkhov','Vollkorn','Vollkorn SC','Voltaire','Waiting for the Sunrise','Wallpoet','Walter Turncoat','Warnes','Wellfleet','Wendy One','WindSong','Wire One','Work Sans','Xanh Mono','Yaldevi','Yanone Kaffeesatz','Yantramanav','Yatra One','Yellowtail','Yeon Sung','Yeseva One','Yesteryear','Yomogi','Yrsa','Yusei Magic','ZCOOL KuaiLe','ZCOOL QingKe HuangYou','ZCOOL XiaoWei','Zen Antique','Zen Antique Soft','Zen Dots','Zen Kaku Gothic Antique','Zen Kaku Gothic New','Zen Kurenaido','Zen Loop','Zen Maru Gothic','Zen Old Mincho','Zen Tokyo Zoo','Zeyada','Zhi Mang Xing','Zilla Slab','Zilla Slab Highlight');
				foreach ($aGoogleFontsAll as $GoogleFont) { ?>
					<option value="<?php echo $GoogleFont; ?>" <?php if($admin_custom_font_family == $GoogleFont && !in_array($GoogleFont, $aGoogleFontsTop, TRUE)) { echo 'selected="selected"'; } ?> ><?php echo $GoogleFont; ?> (Google Font)</option>
				<?php } ?>
				</optgroup>
			</select>
		</div>

		<div id="form-wrapper" style="display:block; clear:both; margin:20px 0;">
			<label style="display:block; clear:both; color:#00F;" for="admin_custom_font_size_option"><?php _e('Font Size', 'admin-custom-font'); ?></label>
			<select style="width:50%; min-width:200px; max-width:500px;" id="admin_custom_font_size_option" name="admin_custom_font_size_option">
				<option value="default"><?php _e('WordPress Default', 'admin-custom-font'); ?></option>
				<?php
				for ($i=12; $i<=24; $i++) {
					$selected = ($i == $admin_custom_font_size) ? 'selected="selected"' : '';
					echo '<option value="' . $i . '"' . ' ' . $selected . '>' . $i . '</option>' . PHP_EOL;
				}
				?>
			</select>
		</div>

		<div id="form-wrapper" style="display:block; clear:both; margin:20px 0;">
			<label style="display:block; clear:both; color:#4C8;" for="admin_custom_font_weight_option"><?php _e('Font Weight', 'admin-custom-font'); ?></label>
			<select style="width:50%; min-width:200px; max-width:500px;" id="admin_custom_font_weight_option" name="admin_custom_font_weight_option">
				<option value="default"><?php _e('WordPress Default', 'admin-custom-font'); ?></option>
				<option value="500" <?php if($admin_custom_font_weight == '500') { echo 'selected="selected"'; } ?> >Normal</option>
				<option value="700" <?php if($admin_custom_font_weight == '700') { echo 'selected="selected"'; } ?> >Bold</option>
			</select>
		</div>

		<input type="hidden" name="admin_custom_font_nonce_input_txt" value="<?php echo wp_create_nonce('admin_custom_font_nonce'); ?>" />
		<input class="button-primary" type="submit" value="<?php _e('Apply Settings', 'admin-custom-font'); ?>" />
	</form>

	<div class="clear"></div>
	<br/>
	<hr/>

	<div class="help">
		<p style="font-size:17px !important;"><span class="dashicons dashicons-update"></span> <?php _e('Try to hard refresh page (CTRL + F5 or hold CTRL + click Reload) or clear browser cache to see changes.', 'admin-custom-font'); ?></p>
		<p style="font-size:17px !important;"><span class="dashicons dashicons-editor-code"></span> <?php _e('Activate browser\'s DevTools (F12) + disable cache in it\'s settings (F1) and you will see instant changes while dev console is open.', 'admin-custom-font'); ?></p>
		<p style="font-size:17px !important;"><span class="dashicons dashicons-chart-bar"></span> <?php _e('Because of different font scaling not all font families appear nice and equal in size. Experiment with font size and weight.', 'admin-custom-font'); ?></p>
		<p style="font-size:17px !important;"><span class="dashicons dashicons-editor-spellcheck"></span> <?php _e('Not all Google Fonts come with complete character sets in case you use non-latin language. Plugin will try to load them, if available.', 'admin-custom-font'); ?></p>
		<p style="font-size:17px !important;"><span class="dashicons dashicons-admin-generic"></span> <?php _e('Plugin re-compiles new CSS files every time you make some changes in settings. Make sure your permissions and ownerships are in order.', 'admin-custom-font'); ?></p>
	</div>

	<div class="clear"></div>
	<hr/>

	<div class="rating">
		<p style="font-size:17px !important;"><a style="text-decoration:none;" href="https://wordpress.org/support/plugin/admin-custom-font/reviews/"><span class="dashicons dashicons-star-filled"></span> <?php _e('We have invested resources developing and improving this plugin. If you find it useful, please rate it and leave a review. Thanks!', 'admin-custom-font'); ?></a></p>
	</div>
</div>

<script type="text/javascript">jQuery(document).ready(function() { jQuery("select[id^=admin_custom_font_]").change(function() { this.form.submit(); }); });</script>

<?php }

## FONT SECTION

// WordPress Custom Font Scripts
function admin_custom_font_scripts($location = '') {

	// get admin custom font options
	$admin_custom_font_family  = get_option('admin_custom_font_family');
	$admin_custom_font_size    = get_option('admin_custom_font_size');
	$admin_custom_font_weight  = get_option('admin_custom_font_weight');

	// use $_POST[] value first to get immediate change effect
	if(isset($_POST['admin_custom_font_family_option']) && !empty($_POST['admin_custom_font_family_option']) && $_POST['admin_custom_font_family_option'] !== 'default') {
		$admin_custom_font_family = $_POST['admin_custom_font_family_option'];
	}

	// load font
	if(!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') {
		// format font URL param
		$admin_custom_font_family_format_url_param = urlencode($admin_custom_font_family);

		// Google Font
		wp_register_style('admin-custom-font-files', 'https://fonts.googleapis.com/css?family=' . $admin_custom_font_family_format_url_param . ':400,700&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese', false, $admin_custom_font_family_format_url_param);
		wp_enqueue_style('admin-custom-font-files');
	}

	// CSS
	switch ($location) {
		case 'admin':
			if( (!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') || (!empty($admin_custom_font_size) && $admin_custom_font_size !== 'default') || (!empty($admin_custom_font_weight) && $admin_custom_font_weight !== 'default') ) {
				wp_register_style('admin-custom-font', plugin_dir_url(__FILE__) . 'css/admin-custom-font.css', false, '');
				wp_enqueue_style('admin-custom-font');
			}
		break;

		case 'admin-login':
			if( (!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') || (!empty($admin_custom_font_size) && $admin_custom_font_size !== 'default') || (!empty($admin_custom_font_weight) && $admin_custom_font_weight !== 'default') ) {
				wp_register_style('admin-custom-font-login', plugin_dir_url(__FILE__) . 'css/admin-custom-font-login.css', false, '');
				wp_enqueue_style('admin-custom-font-login');
			}
		break;

		case 'admin-toolbar':
			if( (!empty($admin_custom_font_family) && $admin_custom_font_family !== 'default') || (!empty($admin_custom_font_size) && $admin_custom_font_size !== 'default') || (!empty($admin_custom_font_weight) && $admin_custom_font_weight !== 'default') ) {
				wp_register_style('admin-custom-font-frontend-toolbar', plugin_dir_url(__FILE__) . 'css/admin-custom-font-toolbar.css', false, '');
				wp_enqueue_style('admin-custom-font-frontend-toolbar');
			}
		break;

		default:
		break;
	}

}

// WordPress Custom Font @ Admin
function admin_custom_font() {
	if(current_user_can('read')) {
		admin_custom_font_scripts('admin');
	}
}
add_action('admin_enqueue_scripts', 'admin_custom_font');

// WordPress Custom Font @ Admin Login
function admin_custom_font_login() {
	if(stripos($_SERVER["SCRIPT_NAME"], strrchr(wp_login_url(), '/')) !== false) {
		admin_custom_font_scripts('admin-login');
	}
}
add_action('login_enqueue_scripts', 'admin_custom_font_login');

// WordPress Custom Font @ Admin Frontend Toolbar
function admin_custom_font_frontend_toolbar() {
	if(!is_admin() && current_user_can('read')) {
		admin_custom_font_scripts('admin-toolbar');
	}
}
add_action('wp_enqueue_scripts', 'admin_custom_font_frontend_toolbar');
?>