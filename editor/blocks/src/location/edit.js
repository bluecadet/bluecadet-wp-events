import { useBlockProps } from '@wordpress/block-editor';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { getKey, setKeys, getStore, REST_NAMESPACE } from '../../../components/_utils/store.js';

import SectionToggle from '../../../components/SectionToggle/SectionToggle.jsx';
import Loader from '../../../components/_sections/Loader/Loader.jsx';
import BasicText from '../../../components/_formParts/BasicText/BasicText.js';
import BasicTextArea from '../../../components/_formParts/BasicTextArea/BasicTextArea.js';


export default function Edit() {
	const blockProps = useBlockProps( { className: 'bc-event-dates' } );
	const [ isLoading, setIsLoading ] = useState( true );
	const [ isLoadingError, setIsLoadingError ] = useState( false );
	const { keys, meta, setMeta } = getStore();

	const isCondensed = useSelect(select =>
		select(preferencesStore).get('bc-events/location-details-condensed', 'condensed')
	);

	const { set } = useDispatch(preferencesStore);
	
	const handleToggle = (value) => {
		set('bc-events/location-details-condensed', 'condensed', value);
	};
	

	useEffect( () => {
		const fetchKeys = async () => {
			try {
				const response = await apiFetch( { path: `/${REST_NAMESPACE}/get-locations-keys` } );  
				setKeys( response );
			} catch ( error ) {
				console.error( 'Error fetching keys:', error );
				setIsLoadingError( true );
			} finally {
				setIsLoading( false );
			}
		};

		fetchKeys();

	}, [] );

	const META_ADDRESS = meta?.[ getKey( 'address', keys ) ] ?? '';
	const META_DESCRIPTION = meta?.[ getKey( 'description', keys ) ] ?? '';
	const META_WEBSITE = meta?.[ getKey( 'website', keys ) ] ?? '';
	
	return (
		<div { ...blockProps }>
			{ isLoading ? (
				<Loader />
			) : (
				<div className="bc-event-dates__container bc-event-dates__location">
					{/* <Validation /> */}
					<SectionToggle
						title={ __( 'Event Location Details', 'basecadet' ) }
						value={ isCondensed }
						onChange={ (value) => handleToggle(value) }
						asTitle={true}
						titleTag="h2"
					/>
					
					{ !isCondensed && (
						<div className="bc-event__content-section">
							<div className="bc-events__flex-fieldset">
								<BasicTextArea
									id="location-address"
									label={ __( 'Location Address', 'basecadet' ) }
									value={ META_ADDRESS }
									onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'address', keys ) ]: val } ) }
									rows={ 4 }
									useLineBreak={true}
								/>
								<BasicTextArea
									id="location-description"
									label={ __( 'Location Description', 'basecadet' ) }
									value={ META_DESCRIPTION }
									onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'description', keys ) ]: val } ) }
									rows={ 4 }
									useLineBreak={true}
								/>
								<BasicText
									id="location-website"
									label={ __( 'Location Website', 'basecadet' ) }
									value={ META_WEBSITE }
									onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'website', keys ) ]: val } ) }
								/>
							</div>
				
							{/* <div class="bc-event-dates__divider"></div> */}
						</div>
					)}
				</div>
			) }      
		</div>
	);
}
