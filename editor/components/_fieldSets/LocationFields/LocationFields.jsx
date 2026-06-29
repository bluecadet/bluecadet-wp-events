import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { getKey, setKeys, loadKeys, getStore, REST_NAMESPACE } from '../../_utils/store.js';

import SectionToggle from '../../SectionToggle/SectionToggle.jsx';
import Loader from '../../_sections/Loader/Loader.jsx';
import BasicText from '../../_formParts/BasicText/BasicText.js';
import BasicTextArea from '../../_formParts/BasicTextArea/BasicTextArea.js';

import './locationFields.scss';

/**
 * Event location meta fields. Shared by the location block and the settings modal.
 */
export default function LocationFields() {
  const { keys, meta, setMeta } = getStore();
  const [ isLoading, setIsLoading ] = useState( ! keys );
  const [ isLoadingError, setIsLoadingError ] = useState( false );

  const isCondensed = useSelect( select =>
    select( preferencesStore ).get( 'bc-events/location-details-condensed', 'condensed' )
  );

  const { set } = useDispatch( preferencesStore );
  const handleToggle = ( value ) => {
    set( 'bc-events/location-details-condensed', 'condensed', value );
  };

  useEffect( () => {
    const fetchKeys = async () => {
      if ( loadKeys() ) {
        return;
      }
      try {
        const response = await apiFetch( { path: `/${ REST_NAMESPACE }/get-locations-keys` } );
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

  if ( isLoading ) {
    return <Loader />;
  }

  const META_ADDRESS = meta?.[ getKey( 'address', keys ) ] ?? '';
  const META_DESCRIPTION = meta?.[ getKey( 'description', keys ) ] ?? '';
  const META_WEBSITE = meta?.[ getKey( 'website', keys ) ] ?? '';

  return (
    <div className="bc-event-dates__container bc-event-dates__location">
      <SectionToggle
        title={ __( 'Event Location Details', 'basecadet' ) }
        value={ isCondensed }
        onChange={ ( value ) => handleToggle( value ) }
        asTitle={ true }
        titleTag="h2"
      />

      { ! isCondensed && (
        <div className="bc-event__content-section">
          <div className="bc-events__flex-fieldset">
            <BasicTextArea
              id="location-address"
              label={ __( 'Location Address', 'basecadet' ) }
              value={ META_ADDRESS }
              onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'address', keys ) ]: val } ) }
              rows={ 4 }
              useLineBreak={ true }
            />
            <BasicTextArea
              id="location-description"
              label={ __( 'Location Description', 'basecadet' ) }
              value={ META_DESCRIPTION }
              onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'description', keys ) ]: val } ) }
              rows={ 4 }
              useLineBreak={ true }
            />
            <BasicText
              id="location-website"
              label={ __( 'Location Website', 'basecadet' ) }
              value={ META_WEBSITE }
              onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'website', keys ) ]: val } ) }
            />
          </div>
        </div>
      ) }
    </div>
  );
}
