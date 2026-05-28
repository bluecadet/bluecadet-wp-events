import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { getStore, getKey } from '../../../_utils/store';
import ValidationNotice from '../ValidationNotice.jsx';

export default function MissingRecurringStrategy({ onError, onSuccess }) {

  const { keys, meta } = getStore();
  const [ hasError, setHasError ] = useState( false );

  const IS_RECURRING = meta?.[ getKey( 'is_recurring', keys ) ] ?? false;
  const USE_FREQUENCY = meta?.[ getKey( 'use_frequency', keys ) ] ?? false;
  const OCCURENCES = meta?.[ getKey( 'custom_occurrences', keys ) ];

  useEffect( () => {
    if ( IS_RECURRING && !USE_FREQUENCY && !OCCURENCES.length ) {
      setHasError( true );
      onError( 'missing_recurring_strategy' );
    } else {
      setHasError( false );
      onSuccess( 'missing_recurring_strategy' );
    }
  }, [ IS_RECURRING, USE_FREQUENCY, OCCURENCES ] );

  return (
    <>
      { hasError && (
        <ValidationNotice>
          <p><strong>Missing Recurring Strategy:</strong></p>
          <p>A recurring event must have a frequency or specific dates defined.</p>
        </ValidationNotice>
      )}
    </>
  )
}