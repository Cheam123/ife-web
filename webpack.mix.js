const mix    = require('laravel-mix');
const lodash = require("lodash");
const folder = {
    src: "resources/", // source files
    dist: "public/", // build files
    dist_assets: "public/assets/" //build assets files
};

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

    var third_party_assets = {
        css_js: [
            {"name": "jquery",           "assets": ["./node_modules/jquery/dist/jquery.min.js"]},
            {"name": "bootstrap",        "assets": ["./node_modules/bootstrap/dist/js/bootstrap.bundle.js"]},
            {"name": "metismenu",        "assets": ["./node_modules/metismenu/dist/metisMenu.js"]},
            {"name": "simplebar",        "assets": ["./node_modules/simplebar/dist/simplebar.js"]},
            {"name": "node-waves",       "assets": ["./node_modules/node-waves/dist/waves.js"]},
            {"name": "waypoints",        "assets": ["./node_modules/waypoints/lib/jquery.waypoints.min.js"]},
            {"name": "jquery-counterup", "assets": ["./node_modules/jquery.counterup/jquery.counterup.min.js"]},
            {"name": "sweetalert2",      "assets": ["./node_modules/sweetalert2/dist/sweetalert2.min.js", "./node_modules/sweetalert2/dist/sweetalert2.min.css"]},
            {"name": "sortablejs",       "assets": ["./node_modules/sortablejs/Sortable.min.js"]},
        ]
    };

	//copying third party assets
    lodash(third_party_assets).forEach(function (assets, type) {
        if (type == "css_js") {
            lodash(assets).forEach(function (plugin) {
                var name = plugin['name'],
                    assetlist = plugin['assets'],
                    css = [],
                    js = [];
                lodash(assetlist).forEach(function (asset) {
                    var ass = asset.split(',');
					for (let i = 0; i < ass.length; ++i) {
                    	if(ass[i].substr(ass[i].length - 3)  == ".js") {
                    		js.push(ass[i]);
                    	} else {
                    		css.push(ass[i]);
                    	}
                	};
                });
            	if(js.length > 0){
            		mix.combine(js, folder.dist_assets + "/libs/" + name + "/" + name + ".min.js");
            	}
            	if(css.length > 0){
            		mix.combine(css, folder.dist_assets + "/libs/" + name + "/" + name + ".min.css");
            	}
            });
        }
    });

    // copy all fonts
    var out = folder.dist_assets + "fonts";
    mix.copyDirectory(folder.src + "fonts", out);

    // copy all images 
    var out = folder.dist_assets + "images";
    mix.copyDirectory(folder.src + "images", out);

    mix.sass('resources/scss/bootstrap.scss', folder.dist_assets + "css").minify(folder.dist_assets + "css/bootstrap.css");
    mix.sass('resources/scss/bootstrap-dark.scss', folder.dist_assets + "css").minify(folder.dist_assets + "css/bootstrap-dark.css");
    mix.sass('resources/scss/icons.scss', folder.dist_assets + "css").options({processCssUrls: false}).minify(folder.dist_assets + "css/icons.css");
    mix.sass('resources/scss/app.scss', folder.dist_assets + "css").options({processCssUrls: false}).minify(folder.dist_assets + "css/app.css");
    mix.sass('resources/scss/app-dark.scss', folder.dist_assets + "css").options({processCssUrls: false}).minify(folder.dist_assets + "css/app-dark.css");

    mix.combine('resources/js/app.js', folder.dist_assets + "js/app.min.js");