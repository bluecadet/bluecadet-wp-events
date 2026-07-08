<?php

namespace BluecadetEvents\Plugin;
use BluecadetEvents\Admin;
use BluecadetEvents\ICS;
use BluecadetEvents\Plugin\Hooks;
use BluecadetEvents\Plugin\Settings;

class Init {

	protected string $plugin_root_dir;
  protected string $plugin_root_url;

  public function init() : void {
    add_action( 'after_setup_theme', [$this, 'run_after_theme_setup'] );
    $this->initialize_plugin();
  }

  private function initialize_plugin() {
    
    Settings::init();
    BackgroundProcesses::init();

    $this->plugin_root_dir = Settings::$plugin_dir;
    $this->plugin_root_url = Settings::$plugin_url;

    $services = [
      new Admin\PostTypes\PostTypes,
      new Admin\Meta\Register\RegisterEventMeta,
      new Admin\Editor\Gutenberg,
      new Admin\Editor\EditorAssets,
      new Admin\Editor\RestRoutes,
      new Admin\Editor\ClassicEditor\RegisterMetaBoxes,
      new Admin\Save\Events,
      new Admin\Trash\Events,
      new ICS\TemplateRedirect,
    ];

    if ( Hooks::hook_filter_use_event_locations() ) {
      $services[] = new Admin\Meta\Register\RegisterLocationMeta;
    }

    if ( Hooks::hook_filter_use_event_series() ) {
      $services[] = new Admin\Meta\Register\RegisterSeriesMeta;
    }

    if ( \is_admin() ) {
      $services[] = new Admin\Views\AdminEventsViews;      
    }

    foreach ($services as $service) {
      $service->register();
    }

    foreach ($services as $service) {
      $service->boot();
    }

    
  }



  public function run_after_theme_setup() {

  }


}
