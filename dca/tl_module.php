<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'isSortable';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'useIndexes';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['isSortable'] = 'sortableSelector,sortableHandleItem,sortableDraggable';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['useIndexes'] = 'sortableIndexColumn,sortableIndexes';

$GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] = str_replace( 'catalogUseSocialSharingButtons;', 'catalogUseSocialSharingButtons;{sortable_legend},isSortable,useIndexes;', $GLOBALS['TL_DCA']['tl_module']['palettes']['catalogUniversalView'] );

$GLOBALS['TL_DCA']['tl_module']['fields']['isSortable'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['isSortable'],
    'inputType' => 'checkbox',

    'eval' => [

        'tl_class' => 'w50',
        'submitOnChange' => true
    ],

    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['useIndexes'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['useIndexes'],
    'inputType' => 'checkbox',

    'eval' => [

        'tl_class' => 'w50',
        'submitOnChange' => true
    ],

    'exclude' => true,
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['sortableSelector'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['sortableSelector'],
    'inputType' => 'text',

    'eval' => [

        'tl_class' => 'w50'
    ],

    'exclude' => true,
    'sql' => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['sortableHandleItem'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['sortableHandleItem'],
    'inputType' => 'text',

    'eval' => [

        'tl_class' => 'w50'
    ],

    'exclude' => true,
    'sql' => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['sortableDraggable'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['sortableDraggable'],
    'inputType' => 'text',

    'eval' => [

        'tl_class' => 'w50'
    ],

    'exclude' => true,
    'sql' => "varchar(255) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['sortableIndexColumn'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['sortableIndexColumn'],
    'inputType' => 'select',

    'eval' => [

        'chosen' => true,
        'mandatory' => true,
        'tl_class' => 'w50',
        'blankOptionLabel' => '-',
        'includeBlankOption' => true
    ],

    'options_callback' => [ 'CatalogManager\Sortable\tl_module', 'getColumns' ],

    'exclude' => true,
    'sql' => "varchar(128) NOT NULL default ''"
];

$GLOBALS['TL_DCA']['tl_module']['fields']['sortableIndexes'] = [

    'label' => &$GLOBALS['TL_LANG']['tl_module']['sortableIndexes'],
    'inputType' => 'listWizard',

    'eval' => [

        'mandatory' => true,
        'tl_class' => 'clr'
    ],

    'exclude' => true,
    'sql' => "text NULL"
];