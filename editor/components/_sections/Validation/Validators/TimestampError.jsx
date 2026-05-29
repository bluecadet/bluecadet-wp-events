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
    if ( START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE && START_TIMESTAMP_VALUE === END_TIMESTAMP_VALUE ) {
      setErrorMessage( 'Start and End dates and times cannot be the same.' );
      onError( 'timestamp_error' );
    } else if ( START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE && START_TIMESTAMP_VALUE > END_TIMESTAMP_VALUE ) {
      setErrorMessage( 'Start Date and Time must be before End Date and Time.' );
      onError( 'timestamp_error' );
    } else if ( 
      (!START_TIMESTAMP_VALUE && !END_TIMESTAMP_VALUE) || 
      (START_TIMESTAMP_VALUE && !END_TIMESTAMP_VALUE) ||
      (!START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE) ||
      (START_TIMESTAMP_VALUE === 0 && END_TIMESTAMP_VALUE === 0) ||
      (START_TIMESTAMP_VALUE === 0 && END_TIMESTAMP_VALUE) ||
      (START_TIMESTAMP_VALUE && END_TIMESTAMP_VALUE === 0)
    ) {
      setErrorMessage( 'Start and end dates and times must be valid.' );
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