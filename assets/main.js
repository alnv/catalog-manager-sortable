var SortableEvents = SortableEvents || {};

(function () {

    'use strict';

    if ( typeof window.addEventListener !== 'undefined' ) {

        window.addEventListener( 'DOMContentLoaded', initialize, false );
    }

    function initialize() {

        if ( typeof arrSortableModules === 'object' && arrSortableModules.length ) {

            for ( var numIndex = 0; numIndex < arrSortableModules.length; numIndex++ ) {

                var arrOptions = arrSortableModules[ numIndex ];
                var objSortableElement = document.querySelector( '.' + arrOptions.class + ( arrOptions.selector ? ' ' + arrOptions.selector : '' ) );

                if ( objSortableElement !== null ) {

                    Sortable.create( objSortableElement, {

                        draggable: arrOptions.draggable,
                        handle: arrOptions.handle,
                        _url: arrOptions.url,
                        _id: arrOptions.id,

                        onSort: function ( objEvent ) {

                            var objXhr = new XMLHttpRequest();

                            this.options._url += '&sortableId=' + this.options._id;
                            this.options._url += '&sortableOld=' + objEvent.oldIndex;
                            this.options._url += '&sortableNew=' + objEvent.newIndex;

                            objXhr.open( 'GET', this.options._url, true );
                            objXhr.send();

                            if ( SortableEvents.hasOwnProperty( 'onSort' ) ) {

                                try {

                                    SortableEvents.onSort( objEvent, this );
                                }

                                catch ( objError ) {

                                    console.log( objError );
                                }
                            }
                        }
                    });
                }
            }
        }
    }

})();