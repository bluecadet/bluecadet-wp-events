import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { store as editorStore } from '@wordpress/editor';
import { Button, Modal } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

import { StagingContext } from '../../components/_utils/store.js';
import EventFields from '../../components/_fieldSets/EventFields/EventFields.jsx';
import LocationFields from '../../components/_fieldSets/LocationFields/LocationFields.jsx';
import SeriesFields from '../../components/_fieldSets/SeriesFields/SeriesFields.jsx';

import './eventsPluginPanel.scss';

/**
 * Each post type maps to the field set its modal renders. Adding a post type here
 * is all it takes to give it an always-available settings panel.
 */
const PANEL_MAP = {
	'bc-events':           { Fields: EventFields,    title: __( 'Event Settings', 'basecadet' ) },
	'bc-events-locations': { Fields: LocationFields, title: __( 'Location Settings', 'basecadet' ) },
	'bc-events-series':    { Fields: SeriesFields,   title: __( 'Series Settings', 'basecadet' ) },
};

/**
 * The modal stages edits in a local buffer seeded from the post's current meta.
 * Save commits the buffer to the post's pending meta (persisted when the post is
 * saved/updated); Cancel discards it. The StagingContext makes every field
 * component read/write the buffer instead of live meta, with no changes to them.
 */
function SettingsModal( { title, Fields, postType, onClose } ) {
	const [ entityMeta, setEntityMeta ] = useEntityProp( 'postType', postType, 'meta' );
	const [ buffer, setBuffer ] = useState( () => ( { ...entityMeta } ) );

	const save = () => {
		setEntityMeta( buffer );
		onClose();
	};

	return (
		<Modal title={ title } size="large" onRequestClose={ onClose } className="bc-events-settings-modal">
			<StagingContext.Provider value={ { meta: buffer, setMeta: setBuffer } }>
				{ /* The `bc-event-dates` wrapper scopes the --bce-* CSS variables that
				     base.scss defines on it; the block gets it via useBlockProps, the
				     modal must add it explicitly or borders/colors fall back to defaults. */ }
				<div className="bc-event-dates">
					<Fields />
				</div>
			</StagingContext.Provider>

			<div className="bc-events-settings-modal__actions">
				<Button variant="tertiary" onClick={ onClose }>
					{ __( 'Cancel', 'basecadet' ) }
				</Button>
				<Button variant="primary" onClick={ save }>
					{ __( 'Save', 'basecadet' ) }
				</Button>
			</div>
		</Modal>
	);
}

function EventSettingsPanel() {
	const postType = useSelect( ( select ) => select( editorStore ).getCurrentPostType(), [] );
	const [ isOpen, setOpen ] = useState( false );

	const config = PANEL_MAP[ postType ];
	if ( ! config ) {
		return null;
	}

  let buttonTitle = __( 'Edit Event Settings', 'basecadet' );

  if ( postType === 'bc-events-locations' ) {
    buttonTitle = __( 'Edit Location Settings', 'basecadet' );
  } else if ( postType === 'bc-events-series' ) {
    buttonTitle = __( 'Edit Series Settings', 'basecadet' );
  }

	const { Fields, title } = config;

	return (
		<PluginDocumentSettingPanel name="bc-event-settings" title={ title }>
			<Button variant="primary" onClick={ () => setOpen( true ) } className="bc-events-settings-panel__button">
				{ buttonTitle }
			</Button>

			{ isOpen && (
				<SettingsModal
					title={ title }
					Fields={ Fields }
					postType={ postType }
					onClose={ () => setOpen( false ) }
				/>
			) }
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'bc-events-settings-panel', { render: EventSettingsPanel } );
