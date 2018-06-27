<?php

namespace CatalogManager\Sortable;

use CatalogManager\Toolkit as Toolkit;

class tl_module extends \Backend {


    public function getColumns( \DataContainer $dc ) {

        $strTable = $dc->activeRecord->catalogTablename;

        if ( $strTable && $this->Database->tableExists( $strTable ) ) {

            $arrColumns = $this->Database->listFields( $strTable );

            return Toolkit::parseColumns( $arrColumns );
        }

        return [];
    }
}