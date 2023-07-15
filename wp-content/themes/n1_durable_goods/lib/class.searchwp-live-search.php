<?php namespace N1_Durable_Goods;

class N1_SearchWP_Live_Search {
    public $configs = [
        'default' => [
            'engine'  => 'default',                      // search engine to use (if SearchWP is available)
            'input'   => [
                'delay'     => 500,                 // wait 500ms before triggering a search
                'min_chars' => 3,                   // wait for at least 3 characters before triggering a search
            ],
            //            'parent_el' => 'body',                      // selector of the parent element
            // for the results container
            'results' => [
                'position' => 'bottom',            // where to position the results (bottom|top)
                'width'    => 'css',              // whether the width should automatically match the input (auto|css)
                'offset'   => [
                    'x' => -50,                   // x offset (in pixels)
                    'y' => -200                    // y offset (in pixels)
                ],
            ],
            'spinner' => [ // Powered by http://spin.js.org/
                           'lines'     => 13,                                 // The number of lines to draw
                           'length'    => 38,                                 // The length of each line
                           'width'     => 17,                                 // The line thickness
                           'radius'    => 45,                                 // The radius of the inner circle
                           'scale'     => .3,                                  // Scales overall size of the spinner
                           'corners'   => 1,                                  // Corner roundness (0..1)
                           'color'     => '#333333',                          // CSS color or array of colors
                           'fadeColor' => 'transparent',                      // CSS color or array of colors
                           'speed'     => 1,                                  // Rounds per second
                           'rotate'    => 0,                                  // The rotation offset
                           'animation' => 'searchwp-spinner-line-fade-quick', // The CSS animation name for the lines
                           'direction' => 1,                                  // 1: clockwise, -1: counterclockwise
                           'zIndex'    => 2e9,                                // The z-index (defaults to 2000000000)
                           'className' => 'spinner',                          // The CSS class to assign to the spinner
                           'top'       => '50%',                              // Top position relative to parent
                           'left'      => '50%',                              // Left position relative to parent
                           'shadow'    => '0 0 1px transparent',              // Box-shadow for the lines
                           'position'  => 'absolute'                          // Element positioning
            ],
        ]
    ];

    public function __construct() {
        add_filter( 'searchwp_live_search_configs', function ( $configs ) {
            return $this->configs;
        } );

        add_action( 'wp_enqueue_scripts', function () {
            wp_add_inline_style( 'searchwp-live-search',
                /** @lang CSS */ <<< EOD
/*@keyframes searchwp-spinner-line-fade-quick {
  0%, 39%, 100% {
    opacity: 0.25;
  }
  40% {
    opacity: 1;
  }
}*/
.searchwp-live-search-results {
  position: fixed !important;
  top: 100px !important;
  width: 400px;
}

.searchwp-live-search-results .spinner {
  height: auto; 
  background: unset; 
  display: block; 
}

.searchwp-live-search-result,
.searchwp-live-search-no-results,
 .searchwp-live-search-no-min-chars:after {
    font-size: 16px;
}

.searchwp-live-search-result a {  
  border: none;
  white-space: normal;
}

.searchwp-live-search-result p.post-author {
  padding: 15px 10px 10px 15px !important;
  border-bottom: none;
}

.searchwp-live-search-result p.post-author a {
  display: inline;
}

.searchwp-live-search-result h1.post-title {
    padding: 0 10px 0 15px;
    font-size: 2rem !important;
}
EOD
            );
        }, 20 );
    }
}

new N1_SearchWP_Live_Search();
