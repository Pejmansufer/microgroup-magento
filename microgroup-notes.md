Microgroup Notes
================
Microgroup's build process is advanced, using super base and shell scripts to create csv files for magmi. The end result is quite simple though, an integrated wordpress and magento site.

WordPress
---------
The wordpress side on superbase still used the old system for WP, i.e. no `proj/` sub folder. Database changes are made like anyother wordpress site. wp-content changes can be committed to the wordpress folder.

### The integration
WP and Magento were integrated fully. Here's some key points on how it was done. First, they were installed in separate httpdoc folders and this was placed in the httpd.conf file:
```
  DocumentRoot "/srv/www/httpdocs"
  <Directory "/srv/www/httpdocs">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>

  Alias /store /srv/www/httpdocs-magento
  <Directory "/srv/www/httpdocs-magento">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>
```
Then in `httpdocs-magento/.htaccess` add: `RewriteBase /store/`

#### Magento in Wordpress
Now, to integrate magento into wordpress, an `magento()` function was created in `wp-content/themes/microgroup/functions.php`. This function bootstraps magento so it can be used in wp templates. Take a look at `wp-content/themes/microgroup/header.php`:

```
<?php $magento = magento(); ?
<!-- ... -->
Mage::getSingleton("checkout/session");
Mage::getDesign()->setPackageName('microgroup');
$layout= Mage::app()->getLayout();
$layout->getUpdate()
       ->addHandle('default')
       ->load();
$layout->generateXml()
       ->generateBlocks();
$skip_links.= $layout->getBlock('minicart_head')->toHtml();
```
That is how we get the minicart in the wordpress header.

#### Wordpress in Magento
To integrate magento into the wordpress layout, we will modify magento's header and footer templates to call the Wordpress header and footer. Open `/app/design/frontend/microgroup/default/template/page/html/header.phtml`
You'll see this code:
```
<?php
define('WP_USE_THEMES',false);
require_once('/srv/www/httpdocs-wp/wp-load.php');
ob_start();
ob_start();
```
Basically we bootstrap wordpress and start a double buffer. There were problems when only using a single `ob_start()`, so use two.

Then look in `/app/design/frontend/microgroup/default/template/page/html/footer.phtml`:
```
<?php
$content = ob_get_clean().ob_get_clean();
get_header();
echo $content;
get_footer();
```
What we do is grab the buffer content into the `$content` variable and then output it between the wordpress header and footer.


Magento
--------
The Magento side is more advanced, having the magento base and `proj/` sub folder. Theme changes should be done in the `proj/` folder as well as storing magento modules. 

### Catalog Generation 
The biggest mystery revolves around the catalog generation and management. I will go over the script that generated the catalog below. In my opinion, said script was great for initially generating the catalog, but is not necessary for everyday management. In other words, I would not suggest full catalog imports be done for updating products. Since MG's catalog doesn't change much, I would suggest manually editing of products in the back-end or custom spreadsheets for Magmi to only do the updates (i.e. sku + price csv)

#### The Code
The shell script that was used to generate the initial catalog is at: `magento/proj/include/shellscripts/setup-catalog.inc`. The basis of this script is that it generates the more complex magmi columns and rows that we do not want to expose to the customer in the master inventory spreadsheet. Such columns include  `'configurable_attributes', 'bundle_options', 'bundle_skus'`, etc... It also generates the rows for the grouped and bundle rows that are used for the grid ordering page.

The script relies on an exported CSV of the master inventory list found in `/magento/proj/catalog/excel/`. Each sheet in the latest version should be exported to `/magento/proj/catalog/csv/`. The names of the files must be exactly these: `'hypodermic_tubing.csv' ,'metric_stainless_tubing.csv' ,'fractional_stainless_tubing.csv' ,'wire_bar.csv', 'saw_blades.csv', 'tube_hose_fittings.csv', 'compression_fittings.csv', 'saw.csv', 'luer_fittings.csv'`

When the code is run, it reads the files in the csv folder and uses that info to generate more spreadsheets. It creates a few extra csv files in `/magento/proj/catalog/csv/`. They are prefixed with the letter 'y' or 'z' for alphabetical purposes. They are as follows:

##### y1-simple_name.csv
This part of the script makes a simple csv with only two columns sku and name. This ensures that each product has a name, since that column is not in the master spreadsheet (only display_name is).

##### z1-configurables.csv
This part of the code generated a configurable product for items ending in '-1FT', '-3FT', '-5FT', '-6FT'. In the master spreadsheet we only list the simple products, because it would be too redundant to have to manually define these configurable parent skus. The configurable parent products are needed for allows the user to select between different lengths.

##### z2-bundles.csv
This part of the code makes bundles of all the configurable products for a certain category. In other words, the grid order page is a bundled product that consists of configurable products.

##### z3-bundles.csv
This part of the code is for bundled products that do not consist of configurable products. In other words, the saw/saw blade pages, and the tube hose/compression fitting pages.

##### z4-bundles.csv
This part of the code is specifically for the luer products since they have many categories.

### Catalog Updates
I would not suggest managing the catalog by running the above script. Instead manual management or small magmi csv files should be used. For example, if you want to update prices, run the following through magmi:
```
"sku", "price"
"316-b062b125-6h2", "18.82"
"316-b062b188-6h2", "18.82"
"316-b125b125-6h3", "18.82"
"316-b125b188-6h3", "18.82"
"316-b125b250-6h3", "18.82"
"316-b188b188-6h3", "18.82"
"316-b188b250-6h3", "18.82"
"316-flb125-5h3", "30"
"316-flb188-6h3", "30"
"316-flb250-6h3", "20.77"
"316-flbhb062-6h2", "20.94"
...
``` 

If you want to disable a product, simply find it in the manage products area and disable it. Make sure to disable the configurable parent sku too!


Deploying Changes
----------
The microgroup proj and server are still using rsync and therefore have issues when rsyning because of the shared src/httpdocs folder and the wp-config.php being deleted. I would recommend scp'ing changes over and applying them manually. Otherwise, the vpw commands can be used as long as `vpw copy-config` is also run.

