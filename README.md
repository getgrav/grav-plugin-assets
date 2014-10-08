# Grav Assets Plugin


`assets` is a [Grav](http://github.com/getgrav/grav) plugin that provides a convenient way to add CSS and JS assets directly from your pages.

# Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `highlight`.

You should now have all the plugin files under

	/your/site/grav/user/plugins/highlight

>> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav), the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) plugins, and a theme to be installed in order to operate.

# Usage

Once the plugin is installed you can use the following syntax to add various asset types, where default order is 10 for `js` and `css` types:

```
{assets:css order:5}
//cdnjs.cloudflare.com/ajax/libs/1140/2.0/1140.css
//theme/css/16pixels.min.css
http://somesite.com/js/cookies.min.css
{/assets}

{assets:js order:10}
//cdnjs.cloudflare.com/ajax/libs/1140/2.0/1140.min.js
//theme/css/16pixels.min.js
http://somesite.com/js/cookies.min.js
{/assets}

{assets:inline_css}
h1 {color: red !important;}
{/assets}

{assets:inline_js}
  function initialize() {
    var mapCanvas = document.getElementById('map_canvas');
    var mapOptions = {
      center: new google.maps.LatLng(44.5403, -78.5463),
      zoom: 8,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(mapCanvas, mapOptions);
  }
{/assets}
```

> Note: If you want to see this plugin in action, have a look at [Grav Learn Site](http://learn.getgrav.org)
