<?php

ClassLoader::addNamespace( 'CatalogManager\Sortable' );

ClassLoader::addClasses([

    'CatalogManager\Sortable\Sortable' => 'system/modules/catalog-manager-sortable/Sortable.php',
    'CatalogManager\Sortable\tl_module' => 'system/modules/catalog-manager-sortable/classes/tl_module.php'
]);