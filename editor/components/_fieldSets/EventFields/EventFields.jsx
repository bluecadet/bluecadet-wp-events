import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { setKeys, loadKeys, REST_NAMESPACE, getStore } from '../../_utils/store.js';

import Loader from '../../_sections/Loader/Loader.jsx';
import SectionToggle from '../../SectionToggle/SectionToggle.jsx';
import EventDetails from '../../_sections/EventDetails/EventDetails.jsx';
import Recurring from '../../_sections/Recurring/Recurring.jsx';
import Validation from '../../_sections/Validation/Validation.jsx';
import ChildEventDetails from '../../_sections/ChildEventDetails/ChildEventDetails.jsx';
import EventLocations from '../../_sections/EventLocations/EventLocations.jsx';
import EventSeries from '../../_sections/EventSeries/EventSeries.jsx';
import { AfterEventDetailsSlot, AfterRecurringSlot} from '../../_utils/slots.js';

import './eventFields.scss';

/**
 * The full event editing UI (dates, recurrence, validation, locations, series, and
 * the child-event view). Shared by the event-dates block and the settings modal.
 */
export default function EventFields() {
  const keys = loadKeys();
  const [ isLoading, setIsLoading ] = useState( true );
  const [ isLoadingError, setIsLoadingError ] = useState( false );
  const [ isChild, setIsChild ] = useState( false );
  const [ useLocations, setUseLocations ] = useState( false );
  const [ useSeries, setUseSeries ] = useState( false );
  const [ useRecurDesc, setUseRecurDesc ] = useState( false );
  const postID = useSelect( select => select( 'core/editor' ).getCurrentPostId() );
  const { meta, setMeta } = getStore();

  const isCondensed = useSelect( select =>
    select( preferencesStore ).get( 'bc-events/details-condensed', 'condensed' )
  );

  const isChildCondensed = useSelect( select =>
    select( preferencesStore ).get( 'bc-events/child-details-condensed', 'condensed' )
  );

  const { set } = useDispatch( preferencesStore );
  const handleToggle = ( value ) => {
    set( 'bc-events/details-condensed', 'condensed', value );
  };

  const handleChildToggle = ( value ) => {
    set( 'bc-events/child-details-condensed', 'condensed', value );
  };

  useEffect( () => {
    // Always fetch the event key map fresh. The preferences store is a single shared
    // slot written by every post type's key set, so a cached value may be stale or
    // from another post type — re-fetching guarantees getKey() resolves correctly
    // (a stale map silently breaks fields like recur_desc on load and save).
    const fetchKeys = async () => {
      try {
        const response = await apiFetch( { path: `/${ REST_NAMESPACE }/get-keys` } );
        setKeys( response );
      } catch ( error ) {
        console.error( 'Error fetching keys:', error );
        setIsLoadingError( true );
      } finally {
        setIsLoading( false );
      }
    };

    const checkIsChild = async () => {
      if ( postID ) {
        try {
          const url = addQueryArgs( `/${ REST_NAMESPACE }/is-child`, { id: postID } );
          const response = await apiFetch( { path: url } );
          setIsChild( response );
        } catch ( error ) {
          console.error( 'Error fetching is_child:', error );
        }
      }
    };

    const getSupportSettings = async () => {
      try {
        const response = await apiFetch( { path: `/${ REST_NAMESPACE }/get-support-settings` } );
        setUseLocations( response?.use_locations ?? false );
        setUseSeries( response?.use_series ?? false );
        setUseRecurDesc( response?.use_recuring_description && response?.recurring_description_helper_text ? response.recurring_description_helper_text : false );
      } catch ( error ) {
        console.error( 'Error fetching support settings:', error );
      }
    };

    fetchKeys();
    checkIsChild();
    getSupportSettings();
  }, [ postID ] );

  if ( isLoading ) {
    return <Loader />;
  }

  return (
    <div className="bc-event-dates__container">
      { isChild ? (
        <>
          <SectionToggle
            title={ __( 'Child Event Details', 'basecadet' ) }
            value={ isChildCondensed }
            onChange={ ( value ) => handleChildToggle( value ) }
            asTitle={ true }
            titleTag="h2"
          />
          { ! isChildCondensed && <ChildEventDetails parentData={ isChild } /> }
        </>
      ) : (
        <>
          <Validation />
          <SectionToggle
            title={ __( 'Event Details', 'basecadet' ) }
            value={ isCondensed }
            onChange={ ( value ) => handleToggle( value ) }
            asTitle={ true }
            titleTag="h2"
          />

          { ! isCondensed && (
            <>
              <EventDetails />
              { useLocations && <EventLocations /> }
              { useSeries && <EventSeries /> }
              <AfterEventDetailsSlot fillProps={{ keys, postID, meta, setMeta }} />
              <Recurring useRecurDesc={useRecurDesc} />
              <AfterRecurringSlot fillProps={{ keys, postID, meta, setMeta }} />
            </>
          ) }
        </>
      ) }
    </div>
  );
}
