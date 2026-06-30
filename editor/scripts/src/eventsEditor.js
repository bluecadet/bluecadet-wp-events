import { AfterEventDetailsFill, AfterRecurringFill } from '../../components/_utils/slots.js';

// Expose slots for editor extensions to use without having to import the slots file directly 
window.BluecadetEvents = window.BluecadetEvents || {};
window.BluecadetEvents.AfterEventDetailsFill = AfterEventDetailsFill;
window.BluecadetEvents.AfterRecurringFill = AfterRecurringFill;


/**
 * Keeping this code for now...it may come back at some point...
 */
// import { useEffect, useRef } from '@wordpress/element';
// import { useSelect, dispatch, select } from '@wordpress/data';
// import { store as blockEditorStore } from '@wordpress/block-editor';
// import { registerPlugin } from '@wordpress/plugins';

// function EventDatesEnforcer() {
// 	const isDragging = useSelect( ( sel ) =>
// 		sel( blockEditorStore ).isDraggingBlocks()
// 	);

// 	const wasDragging = useRef( false );

// 	useEffect( () => {
// 		const dragJustEnded = wasDragging.current && ! isDragging;
// 		wasDragging.current = isDragging;

// 		if ( ! dragJustEnded ) return;

// 		const blocks = select( blockEditorStore ).getBlocks();
// 		const idx    = blocks.findIndex( ( b ) => b.name === 'bc-events/event-dates' );

// 		if ( idx > 0 ) {
// 			dispatch( blockEditorStore ).moveBlocksToPosition(
// 				[ blocks[ idx ].clientId ], '', '', 0
// 			);
// 		}
// 	}, [ isDragging ] );

// 	return null;
// }

// Disabled: the always-available "Event Settings" panel (eventsPluginPanel.js) now
// guarantees the meta is editable regardless of the block's presence/position, so the
// block is freely movable/removable and no longer needs pinning to position 0.
// Re-enable this line to restore the enforced placement.
// registerPlugin( 'bc-events-enforcer', { render: EventDatesEnforcer } );
