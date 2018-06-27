<?php

namespace CatalogManager\Sortable;

use CatalogManager\Toolkit as Toolkit;
use CatalogManager\CatalogController as CatalogController;

class Sortable extends CatalogController {


    public function __construct() {

        parent::__construct();

        $this->import( 'Database' );
        $this->import( 'SQLQueryBuilder' );
    }


    public function initialize( $objModule ) {

        if ( $objModule->sortableIndexes ) $objModule->sortableIndexes = Toolkit::deserialize( $objModule->sortableIndexes );

        if ( \Input::get( 'sortItems' . $objModule->id ) ) {

            if ( $objModule->useIndexes && is_array( $objModule->sortableIndexes ) && !empty( $objModule->sortableIndexes ) ) {

                $arrState = $this->sortByIndexes( $objModule->sortableIndexes, $objModule->sortableIndexColumn );
            }

            else {

                $arrState = $this->sortBySorting();
            }

            header('Content-Type: application/json');

            echo json_encode( $arrState, 512 );

            exit;
        }

        $strDelimiter = '?';
        $strUrl = ampersand( \Environment::get( 'indexFreeRequest' ) );

        if ( strpos( $strUrl, '?' ) !== false ) $strDelimiter = '&';

        $strUrl = $strUrl . $strDelimiter . 'sortItems' . $objModule->id . '=1';

        $arrOptions = [

            'url' => $strUrl,
            'id' => $objModule->id,
            'class' => 'sortable_' . $objModule->id,
            'selector' => $objModule->sortableSelector,
            'handle' => $objModule->sortableHandleItem,
            'draggable' => $objModule->sortableDraggable
        ];

        if ( !$objModule->isSortable ) return null;

        $objModule->setCss( $arrOptions['class'] );

        $GLOBALS['TL_HEAD'][] = '<script>var arrSortableModules = arrSortableModules || [];arrSortableModules.push('. json_encode( $arrOptions ) .');</script>';

        $GLOBALS['TL_JAVASCRIPT']['catalogManagerSortableScript'] = $GLOBALS['TL_CONFIG']['debugMode']
            ? 'system/modules/catalog-manager-sortable/assets/sortable.js'
            : 'system/modules/catalog-manager-sortable/assets/sortable.min.js';

        $GLOBALS['TL_JAVASCRIPT']['catalogManagerSortableMain'] = $GLOBALS['TL_CONFIG']['debugMode']
            ? 'system/modules/catalog-manager-sortable/assets/main.js'
            : 'system/modules/catalog-manager-sortable/assets/main.js'; // @todo

    }


    protected function sortBySorting() {

        $arrState = [ 'ok' => false ];
        $strModuleId = \Input::get( 'sortableId' );
        $numNewPosition = (int) \Input::get( 'sortableNew' );
        $numOldPosition = (int) \Input::get( 'sortableOld' );
        
        if ( !$strModuleId || Toolkit::isEmpty( $numNewPosition ) || Toolkit::isEmpty( $numOldPosition ) ) return $arrState;

        $arrEntities = $this->getEntitiesByModuleId( $strModuleId, $numOldPosition, $numNewPosition );
        $strTable = $arrEntities[1];
        $arrRows = $arrEntities[0];

        foreach ( $arrRows as $numPosition => $strID ) {

            $this->Database
                ->prepare( 'UPDATE '. $strTable .' %s WHERE id = ?' )
                ->set([ 'sorting' => ( $numPosition + 1 ) * 32  ])
                ->execute( $strID );
        }

        $arrState['ok'] = true;

        return $arrState;
    }


    protected function sortByIndexes( $arrIndexes, $strColumn ) {

        $arrState = [ 'ok' => false ];
        $strModuleId = \Input::get( 'sortableId' );
        $numNewPosition = (int) \Input::get( 'sortableNew' );
        $numOldPosition = (int) \Input::get( 'sortableOld' );

        if ( !$strModuleId || Toolkit::isEmpty( $numNewPosition ) || Toolkit::isEmpty( $numOldPosition ) ) return $arrState;

        $arrEntities = $this->getEntitiesByModuleId( $strModuleId, $numOldPosition, $numNewPosition );
        $strTable = $arrEntities[1];
        $arrRows = $arrEntities[0];

        foreach ( $arrRows as $numPosition => $strID ) {

            $arrSet = [];
            $arrSet[ $strColumn ] = $arrIndexes[ $numPosition ] ?: '';

            $this->Database
                ->prepare( 'UPDATE '. $strTable .' %s WHERE id = ?' )
                ->set( $arrSet )
                ->execute( $strID );
        }

        $arrState['ok'] = true;

        return $arrState;
    }


    protected function getEntitiesByModuleId( $strModuleId, $numOldPosition = 0, $numNewPosition = 0 ) {

        $arrRows = [];
        $objModule = $this->Database->prepare( 'SELECT * FROM tl_module WHERE id = ?' )->limit( 1 )->execute( $strModuleId );

        if ( !$objModule->numRows ) return [ $arrRows, '' ];

        $objModule->catalogTaxonomies = Toolkit::deserialize( $objModule->catalogTaxonomies );
        $objModule->catalogOrderBy = Toolkit::deserialize( $objModule->catalogOrderBy );

        $arrQuery = [

            'table' => $objModule->catalogTablename,
            'where' => []
        ];

        if ( !empty( $objModule->catalogTaxonomies['query'] ) && is_array( $objModule->catalogTaxonomies['query'] ) && $objModule->catalogUseTaxonomies ) {

            $arrQuery['where'] = Toolkit::parseQueries( $objModule->catalogTaxonomies['query'] );
        }

        if ( $objModule->catalogEnableParentFilter ) {

            if ( \Input::get( 'pid' ) ) {

                $arrQuery['where'][] = [

                    'field' => 'pid',
                    'operator' => 'equal',
                    'value' => \Input::get( 'pid' )
                ];
            }
        }

        if ( is_array( $objModule->catalogOrderBy ) && !empty( $objModule->catalogOrderBy ) ) {

            foreach ( $objModule->catalogOrderBy as $arrOrderBy ) {

                if ( $arrOrderBy['key'] && $arrOrderBy['value'] ) {

                    $arrQuery['orderBy'][] = [

                        'field' => $arrOrderBy['key'],
                        'order' => $arrOrderBy['value']
                    ];
                }
            }
        }

        $arrQuery['pagination'] = [

            'limit' => $objModule->catalogPerPage,
            'offset' => $objModule->catalogOffset
        ];

        $intIndex = 0;
        $strTempId = null;
        $objEntities = $this->SQLQueryBuilder->execute( $arrQuery );

        if ( !$objEntities->numRows ) return [ $arrRows, $objModule->catalogTablename ];

        while ( $objEntities->next() ) {

            if ( $numOldPosition == $intIndex ) {

                $strTempId = $objEntities->id;

            } else {

                $arrRows[] = $objEntities->id;
            }

            $intIndex++;
        }

        array_insert( $arrRows, $numNewPosition, $strTempId );

        return [ $arrRows, $objModule->catalogTablename ];
    }
}