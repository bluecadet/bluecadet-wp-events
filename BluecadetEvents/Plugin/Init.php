<?php

namespace BluecadetEvents\Plugin;
use BluecadetEvents\Admin;

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
    new Admin\Editor\Gutenberg;
    new Admin\Editor\EditorAssets;
    new Admin\Editor\RestRoutes;

    BackgroundProcesses::__init();

    if ( \is_admin() ) {
      new Admin\Views\AdminEventsViews;
      new Admin\Save\Events;      
    }
  }



  public function run_after_theme_setup() {

  }


}
