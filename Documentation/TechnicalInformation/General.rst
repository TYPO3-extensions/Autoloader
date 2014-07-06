General
^^^^^^^

The autoloader has a simple API, that take care to triger the diferent loaders of the autoloader extension. In the ext_localconf and ext_tables files of your extension, you have to trigger the static loader function. Furthermore you **have to** set autolaoder in the dependencies of your extension (so the extensions are load in the right order).

To trigger the autoloader, please add the following lines to your ext_localconf.php and ext_tables.php files:

// in ext_localconf.php
\HDNET\Autoloader\Loader::extLocalconf('VENDORNAME', 'extension_key');

// in ext_tables.php
\HDNET\Autoloader\Loader::extTables('VENDORNAME', 'extension_key');

In the basic configuration this lines will trigger all loader of the autoloader extension. The loader are alsways split into three parts:
- prepare Loader information: The loader prepare complex information (reflection, file listings, search and replace, prepare information) and store that information into an array. The autoloader extension take care, that the array is cached, so the next calls are smart and fast.
- ext_tables excute: The code that is execute in the ext_tables
- ext_localconf execute: The code that is execute in the ext_localconf

Furthermore you have the possibility to select only a few Loaders to increase the performance of the autoloading process. This is possible by adding a array as third parameter including the names of the Loader that you need. Recommendation: Please use the same Loader for the ext_tables and ext_localconf file.

// Example in ext_localconf.php
\HDNET\Autoloader\Loader::extLocalconf('VENDORNAME', 'extension_key', array('Xclass', 'Slots'));

// Example in ext_tables.php
\HDNET\Autoloader\Loader::extTables('VENDORNAME', 'extension_key', array('Xclass', 'Slots'));