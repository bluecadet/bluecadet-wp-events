import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { getKey, setKeys, getStore, REST_NAMESPACE } from '../../_utils/store.js';

import SectionToggle from '../../SectionToggle/SectionToggle.jsx';
import Loader from '../../_sections/Loader/Loader.jsx';
import BasicText from '../../_formParts/BasicText/BasicText.js';
import BasicTextArea from '../../_formParts/BasicTextArea/BasicTextArea.js';

import './seriesFields.scss';

/**
 * Event series meta fields. Shared by the series block and the settings modal.
 */
export default function SeriesFields() {
  const { keys, meta, setMeta } = getStore();
  const [ isLoading, setIsLoading ] = useState( true );
  const [ isLoadingError, setIsLoadingError ] = useState( false );

  const isCondensed = useSelect( select =>
    select( preferencesStore ).get( 'bc-events/series-details-condensed', 'condensed' )
  );

  const { set } = useDispatch( preferencesStore );
  const handleToggle = ( value ) => {
    set( 'bc-events/series-details-condensed', 'condensed', value );
  };

  useEffect( () => {
    // Always fetch fresh: the preferences key slot is shared across post types,
    // so a cached map may be stale or belong to a different post type.
    const fetchKeys = async () => {
      try {
        const response = await apiFetch( { path: `/${ REST_NAMESPACE }/get-series-keys` } );
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

  const META_DISPLAY_NAME = meta?.[ getKey( 'display_name', keys ) ] ?? '';
  const META_PLURAL_NAME = meta?.[ getKey( 'plural_name', keys ) ] ?? '';
  const META_SINGULAR_NAME = meta?.[ getKey( 'singular_name', keys ) ] ?? '';
  const META_DESCRIPTION = meta?.[ getKey( 'description', keys ) ] ?? '';

  return (
    <div className="bc-event-dates__container bc-event-dates__series">
      <SectionToggle
        title={ __( 'Event Series Details', 'basecadet' ) }
        value={ isCondensed }
        onChange={ ( value ) => handleToggle( value ) }
        asTitle={ true }
        titleTag="h2"
      />

      { ! isCondensed && (
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
              useLineBreak={ true }
            />
          </div>
        </div>
      ) }
    </div>
  );
}
