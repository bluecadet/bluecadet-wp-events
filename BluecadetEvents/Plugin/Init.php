<?php

namespace BluecadetEvents\Plugin;
use BluecadetEvents\Admin;
use BluecadetEvents\ICS;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Plugin\Settings;

class Init {


	protected string $plugin_root_dir;
  protected string $plugin_root_url;


  public function __construct() {
    Settings::__init();
    $this->plugin_root_dir = Settings::$plugin_dir;
    $this->plugin_root_url = Settings::$plugin_url;
    $this->initialize_plugin();

    add_action( 'after_setup_theme', [$this, 'run_after_theme_setup'] );

  }


  private function initialize_plugin() {

    new Admin\PostTypes\PostTypes;
    new Admin\Meta\RegisterMeta;

    if ( Hooks::hook_filter_use_event_locations() ) {
      new Admin\Meta\Locations\RegisterMeta;
    }

    if ( Hooks::hook_filter_use_event_series() ) {
      new Admin\Meta\Series\RegisterMeta;
    }


    new Admin\Editor\Gutenberg;
    new Admin\Editor\EditorAssets;
    new Admin\Editor\RestRoutes;
    new Admin\Editor\ClassicEditor\RegisterMetaBoxes;
    new Admin\Save\Events;
    new Admin\Trash\Events;

    BackgroundProcesses::__init();

    if ( \is_admin() ) {
      new Admin\Views\AdminEventsViews;      
    }

    new ICS\TemplateRedirect;
  }



  public function run_after_theme_setup() {

  }


}
