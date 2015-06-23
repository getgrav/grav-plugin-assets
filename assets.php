<?php
namespace Grav\Plugin;

use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Common\Uri;
use \Grav\Common\Page\Page;

class AssetsPlugin extends Plugin
{
    protected $assets;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPageContentRaw' => ['onPageContentRaw', 0]
        ];
    }

    /**
     * Find inline assets in the following format:
     *
     * {assets:inline_css}
     * h1 {color: red !important;}
     * {/assets}
     *
     * {assets:css order:5}
     * //cdnjs.cloudflare.com/ajax/libs/1140/2.0/1140.css
     * //cdnjs.cloudflare.com/ajax/libs/1140/2.0/1141.css
     * //cdnjs.cloudflare.com/ajax/libs/1140/2.0/1142.css
     * {/assets}
     *
     * {assets:js}
     * //cdnjs.cloudflare.com/ajax/libs/angularFire/0.5.0/angularfire.min.js
     * {/assets}
     *
     * {assets:inline_js}
     * function initialize() {
     *   var mapCanvas = document.getElementById('map_canvas');
     *   var mapOptions = {
     *     center: new google.maps.LatLng(44.5403, -78.5463),
     *     zoom: 8,
     *     mapTypeId: google.maps.MapTypeId.ROADMAP
     *   }
     *   var map = new google.maps.Map(mapCanvas, mapOptions);
     * }
     * {/assets}
     */
    public function onPageContentRaw()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        $page = $this->grav['page'];

        $config = $this->mergeConfig($page);

        if ($config->get('enabled')) {
            $content = $page->content();

            preg_match_all('/(?:<p>)?{assets(?:\:(.+?))?(?: order:(.+?))?}\s?(.*?)\s?{\/assets}(?:<\/p>)?(?:\n)?/smi', $content, $matches);

            $count = count($matches[0]);
            if ($count) {
                for ($x=0; $x<$count; $x++) {
                    $action = $matches[1][$x] ?: null;
                    $order = $matches[2][$x] ?: null;

                    $data_string = trim(strip_tags($matches[3][$x], '<link><script>'));
                    $data = explode("\n", $data_string);
                    $content = str_replace($matches[0][$x], '', $content);

                    // if not a full URL try to find a page and use it's full path
                    if (in_array($action, ['css','js'])) {
                        foreach ($data as $key => $value) {
                            if (!$this->isValidUrl($value)) {
                                $path_parts = pathinfo($value);
                                if ($path_parts['dirname'] == '.') {
                                    $asset_page = $page;
                                } else {
                                    $asset_page = $this->grav['pages']->dispatch($path_parts['dirname'], true);
                                }

                                if ($asset_page) {
                                    $path = str_replace(GRAV_ROOT, '', $asset_page->path());
                                    $data[$key] =  $path . '/' . $path_parts['basename'];
                                }
                            }
                        }
                    }

                    if ($action == 'css' || $action == 'js' || $action == null) {
                        foreach ($data as $entry) {
                            $this->grav['assets']->add($entry, $order);
                        }
                    } elseif ($action == 'inline_css') {
                        $this->grav['assets']->addInlineCss($data[0]);
                    } elseif ($action == 'inline_js') {
                        $this->grav['assets']->addInlineJs($data[0]);
                    }

                }

                $page->content($content);
                unset($this->grav['page']);
                $this->grav['page'] = $page;
            }
        }

    }

    /**
     * @param $url
     *
     * @return bool
     */
    protected function isValidUrl($url)
    {
        $regex = '/^(?:(https?|ftp|telnet):)?\/\/((?:[a-z0-9@:.-]|%[0-9A-F]{2}){3,})(?::(\d+))?((?:\/(?:[a-z0-9-._~!$&\'\(\)\*\+\,\;\=\:\@]|%[0-9A-F]{2})*)*)(?:\?((?:[a-z0-9-._~!$&\'\(\)\*\+\,\;\=\:\/?@]|%[0-9A-F]{2})*))?(?:#((?:[a-z0-9-._~!$&\'\(\)\*\+\,\;\=\:\/?@]|%[0-9A-F]{2})*))?/';
        if (preg_match($regex, $url)) {
            return true;
        } else {
            return false;
        }
    }
}
