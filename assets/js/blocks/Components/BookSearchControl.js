import { Component, Fragment } from "@wordpress/element";

import {
    Button,
    Placeholder
} from '@wordpress/components';

const {__} = wp.i18n;

class BookSearchControl extends Component {
    constructor( props ) {
        super( props );

        this.state = {
            search: '',
            isSearching: false,
            searchResults: []
        }
    }

    render() {
        const results = [];

        return (
            <Placeholder
                icon="book-alt"
                label={__( 'Select a Book', 'novelist' )}
                instructions={__(
                    'Enter a book title to find the book you want to select.',
                    'novelist'
                )}
            >
                <form onSubmit={( e ) => this.handleSearchSubmit( e )}>
                    <input
                        type="search"
                        className="components-placeholder__input"
                        aria-label={__( 'Enter a book title', 'novelist' )}
                        aria-placeholder={__( 'Enter a book title', 'novelist' )}
                        onChange={( e ) => this.setState( {search: e.target.value} )}
                        value={this.state.search || ''}
                    />
                    <Button isPrimary type="submit" disabled={this.state.isSearching}>
                        {__( 'Search', 'novelist' )}
                    </Button>
                </form>

                {results.length > 0 && (
                    <Fragment>
                        <p>{__('Select a book.', 'novelist')}</p>
                    </Fragment>
                )}
            </Placeholder>
        )
    }

    handleSearchSubmit( e ) {
        e.preventDefault();

        if ( ! this.state.search ) {
            return;
        }

        this.setState( {isSearching: true} );
    }
}

export default BookSearchControl;
