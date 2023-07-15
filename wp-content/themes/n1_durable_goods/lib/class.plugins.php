<?php namespace N1_Durable_Goods;

class Plugins {
    protected $auto_activate = array(
        'advanced-custom-fields-pro/acf.php',
        'classic-editor/classic-editor.php',
    );

    protected $auto_deactivate = array();

    public function __construct() {
        add_action('init', [$this, 'activate_plugins'], 10);
        add_action('switch_theme', [$this, 'deactivate_plugins']);
    }

    /**
     * Activate plugins
     */
    function activate_plugins(): void {
        activate_plugins($this->auto_activate);
        deactivate_plugins($this->auto_deactivate);
    }

    /**
     * Deactivate plugins
     */
    function deactivate_plugins(): void {
        deactivate_plugins($this->auto_activate);
    }

}

new Plugins();
