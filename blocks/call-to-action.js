( function( blocks, editor, element ) {
	var el = element.createElement;

	blocks.registerBlockType( 'mcb/call-to-action', {
		title: 'Kadra', // The title of block in editor.
        icon: 'groups', 
        description: 'Blok do automatycznego wstawiania kierownictwa szkoły oraz wszystkich nauczycieli wraz z nauczanymi przedmiotami', // The description of block in editor.
		category: 'widgets', // The category of block in editor.
		attributes: {
            content: {
                type: 'string',
                default: 'Kadra pedagogiczna 2020 / 2021'
            },
        },
		edit: function( props ) {
            return (
                el( 'div', { className: props.className },
                    el(
                        editor.RichText,
                        {
                            tagName: 'div',
                            className: 'mcb-call-to-action-content',
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
                        className: 'mcb-call-to-action-content',
                        value: props.attributes.content,
                    } )
                )
            );
        },
	} );
} )( window.wp.blocks, window.wp.editor, window.wp.element );