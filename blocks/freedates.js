( function( blocks, editor, element ) {
	var el = element.createElement;

	blocks.registerBlockType( 'mcb/freedates', {
		title: 'Dni Wolne', // The title of block in editor.
        icon: 'clock', 
        description: 'Blok do wstawiania dni wolnych od zajęć z ustawień ZSPlugin', // The description of block in editor.
		category: 'widgets', // The category of block in editor.
		attributes: {
            content: {
                type: 'string',
                default: 'Nic nieznaczące pole'
            },
        },
		edit: function( props ) {
            return (
                el( 'div', { className: props.className },
                    el(
                        editor.RichText,
                        {
                            tagName: 'div',
                            className: 'freedates',
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
                        className: 'freedates',
                        value: props.attributes.content,
                    } )
                )
            );
        },
	} );
} )( window.wp.blocks, window.wp.editor, window.wp.element );