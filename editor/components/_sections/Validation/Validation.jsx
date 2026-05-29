import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { getStore, getKey } from '../../_utils/store';
import { useDispatch } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';

import MissingFields from './Validators/MissingFields.jsx';
import TimestampError from './Validators/TimestampError.jsx';
import MissingRecurringStrategy from './Validators/MissingRecurringStrategy.jsx';

const LOCK_KEY = 'event-dates-required-fields';

export default function Validation() {

  const { keys, meta } = getStore();
  const { lockPostSaving, unlockPostSaving } = useDispatch( editorStore );
  const [ lockedBy, setLockedBy ] = useState( [] );
  const [ hasError, setHasError ] = useState( false );

  const handleLockPost = (key) => {
    setLockedBy( (prev) => prev.includes( key ) ? prev : [ ...prev, key ] );
  }

  const handleUnlockPost = (key) => {
    setLockedBy( (prev) => prev.filter( (k) => k !== key ) );
  }

  useEffect( () => {
    if ( lockedBy.length > 0 ) {
      setHasError( true );
      lockPostSaving( LOCK_KEY );
    } else {
      setHasError( false );
      unlockPostSaving( LOCK_KEY );
    }
  }, [ lockedBy ] );

  useEffect( () => {
    return () => unlockPostSaving( LOCK_KEY );
  }, [] );

  return (
    <div className={`bc-event-dates__validation ${ hasError ? 'has-error' : '' }`}>
      <TimestampError
        onError={ handleLockPost }
        onSuccess={ handleUnlockPost }
      />
      <MissingFields 
        onError={ handleLockPost }
        onSuccess={ handleUnlockPost }
      />
      <MissingRecurringStrategy
        onError={ handleLockPost }
        onSuccess={ handleUnlockPost }
       />
    </div>
  )

}