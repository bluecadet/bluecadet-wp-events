import { useEffect, useState } from '@wordpress/element';
import { getStore, getKey } from '../../../_utils/store';
import { Notice } from '@wordpress/components';
import ValidationNotice from '../ValidationNotice.jsx';

export default function TimestampError({ onError, onSuccess }) {

  const { keys, meta, setMeta } = getStore();
  const [ errorMessage, setErrorMessage ] = useState( false );

  const START_TIMESTAMP_VALUE = meta?.[ getKey( 'start_timestamp', keys ) ] ?? '';
  const END_TIMESTAMP_VALUE   = meta?.[ getKey( 'end_timestamp', keys ) ] ?? '';

  useEffect( () => {
    console.log( 'Validating timestamps...', { START_TIMESTAMP_VALUE, END_TIMESTAMP_VALUE } );
    if ( START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE && START_TIMESTAMP_VALUE > END_TIMESTAMP_VALUE ) {
      setErrorMessage( 'End date and time cannot be before start date and time.' );
      onError( 'timestamp_error' );
    } else {
      setErrorMessage( false );
      onSuccess( 'timestamp_error' );
    }
  }, [ START_TIMESTAMP_VALUE, END_TIMESTAMP_VALUE ] );

  return (
    <>
      { errorMessage && (
        <ValidationNotice>
          { errorMessage }
        </ValidationNotice>
      )}
    </>
  )
}