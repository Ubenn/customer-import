# Magento 2 CLI Customer Import Module

This module allows importing customers and their addresses from CSV and JSON files via a Magento 2 CLI command. It is designed to be extendable, following SOLID principles, and is compatible with the latest versions of Magento 2 and PHP.

## Requirements

- Magento 2.4.7
- PHP 8.3
- Composer 2.7.1


## Credit and contact
ubedullah Bennishirur
88929504**
ubedullahbenni@gmail.com


## Additinal steps.

1.Add repositories as below in root composer.json file
<pre>
"repositories": [
       {
           "type": "vcs",
           "url": "https://github.com/ubenn/customer-import.git"
       }
   ],
   </pre>

2.run 
composer require vml/customer-import:dev-main

3.finaly 

<pre>
php bin/magento module:enable VML_CustomerImport
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:clean

php bin/magento customer:import sample-csv /var/www/html/sample.csv
php bin/magento customer:import sample-json /var/www/html/sample.json

</pre>





