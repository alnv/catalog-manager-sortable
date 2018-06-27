<?php

$GLOBALS['TL_HOOKS']['catalogManagerBeforeInitializeView'][] = [ 'CatalogManager\Sortable\Sortable', 'initialize' ];