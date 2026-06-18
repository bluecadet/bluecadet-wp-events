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
		select(preferencesStore).get('bc-events/series-details-condensed', 'condensed')
	);

	const { set } = useDispatch(preferencesStore);
	
	const handleToggle = (value) => {
		set('bc-events/series-details-condensed', 'condensed', value);
	};
	

	useEffect( () => {
		const fetchKeys = async () => {
			try {
				const response = await apiFetch( { path: `/${REST_NAMESPACE}/get-series-keys` } );  
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

	const META_DISPLAY_NAME = meta?.[ getKey( 'display_name', keys ) ] ?? '';
	const META_PLURAL_NAME = meta?.[ getKey( 'plural_name', keys ) ] ?? '';
	const META_SINGULAR_NAME = meta?.[ getKey( 'singular_name', keys ) ] ?? '';
	const META_DESCRIPTION = meta?.[ getKey( 'description', keys ) ] ?? '';

	console.log(keys);
	
	return (
		<div { ...blockProps }>
			{ isLoading ? (
				<Loader />
			) : (
				<div className="bc-event-dates__container bc-event-dates__series">
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
								<BasicText
									id="series-display-name"
									label={ __( 'Series Display Name', 'basecadet' ) }
									value={ META_DISPLAY_NAME }
									onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'display_name', keys ) ]: val } ) }
								/>
								<BasicText
									id="series-plural-name"
									label={ __( 'Series Plural Name', 'basecadet' ) }
									value={ META_PLURAL_NAME }
									onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'plural_name', keys ) ]: val } ) }
								/>
								<BasicText
									id="series-singular-name"
									label={ __( 'Series Singular Name', 'basecadet' ) }
									value={ META_SINGULAR_NAME }
									onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'singular_name', keys ) ]: val } ) }
								/>
								<BasicTextArea
									id="series-description"
									label={ __( 'Series Description', 'basecadet' ) }
									value={ META_DESCRIPTION }
									onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'description', keys ) ]: val } ) }
									rows={ 4 }
									useLineBreak={true}
								/>
							</div>
						</div>
					)}
				</div>
			) }      
		</div>
	);
}
