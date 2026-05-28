import { useEffect, useRef } from '@wordpress/element';
import { useSelect, dispatch, select } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { registerPlugin } from '@wordpress/plugins';

function EventDatesEnforcer() {
	const isDragging = useSelect( ( sel ) =>
		sel( blockEditorStore ).isDraggingBlocks()
	);

	const wasDragging = useRef( false );

	useEffect( () => {
		const dragJustEnded = wasDragging.current && ! isDragging;
		wasDragging.current = isDragging;

		if ( ! dragJustEnded ) return;

		const blocks = select( blockEditorStore ).getBlocks();
		const idx    = blocks.findIndex( ( b ) => b.name === 'bc-events/event-dates' );

		if ( idx > 0 ) {
			dispatch( blockEditorStore ).moveBlocksToPosition(
				[ blocks[ idx ].clientId ], '', '', 0
			);
		}
	}, [ isDragging ] );

	return null;
}

registerPlugin( 'bc-events-enforcer', { render: EventDatesEnforcer } );
