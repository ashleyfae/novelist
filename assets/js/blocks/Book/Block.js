import BookPreview from "../Components/BookPreview";
import BookSearchControl from "../Components/BookSearchControl";

const { Component } = wp.element;

class Block extends Component {
    constructor(props) {
        super( props );

        this.state = {
            bookId: null
        };
    }

    render() {
        if ( this.state.bookId ) {
            return (
                <BookPreview id={ this.state.bookId } />
            )
        }
        return (
            <BookSearchControl />
        )
    }
}

export default Block;
