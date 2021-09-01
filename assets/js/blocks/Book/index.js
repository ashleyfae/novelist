import Block from './Block';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

registerBlockType('novelist/book', {
    title: __('Book', 'novelist'),
    icon: 'book',
    category: 'novelist',
    supports: {
        multiple: true,
        customClassName: false
    },
    attributes: {
        id: {
            type: 'string',
            default: ''
        }
    },
    edit(props) {
        return <Block {...props} />;
    },
    save() {
        return null;
    }
})
