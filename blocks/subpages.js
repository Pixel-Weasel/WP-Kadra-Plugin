( function( blocks, editor, element ) {
	var el = element.createElement;

	blocks.registerBlockType( 'mcb/subpages', {
		title: 'Automatyczne wstawianie podstron', // The title of block in editor.
        icon: 'groups', 
        description: 'Blok do automatycznego uzupełniania podstron dla danej strony', // The description of block in editor.
		category: 'widgets', // The category of block in editor.
		attributes: {
            content: {
                type: 'string',
                default: 'Kompletnie nie potrzebne dane'
            },
        },
		edit: function( props ) {
            return (
                el( 'div', { className: props.className },
                    el(
                        editor.RichText,
                        {
                            tagName: 'div',
                            className: 'subpages',
                            value: props.attributes.content,
                            onChange: function( content ) {
                                props.setAttributes( { content: content } );
                            }
                        }
                    )
                )
            );
        },
		save: function( props ) {
            return (
                el( 'div', { className: props.className },
                    el( editor.RichText.Content, {
                        tagName: 'h3',
                        className: 'subpages',
                        value: props.attributes.content,
                    } )
                )
            );
        },
	} );
} )( window.wp.blocks, window.wp.editor, window.wp.element );