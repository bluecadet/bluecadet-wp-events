import { useBlockProps } from '@wordpress/block-editor';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { setKeys, getStore, REST_NAMESPACE } from '../../../components/_utils/store.js';

import SectionToggle from '../../../components/SectionToggle/SectionToggle.jsx';
import CheckboxButton from '../../../components/_formParts/CheckboxButton/CheckboxButton.jsx';
import EventDetails from '../../../components/_sections/EventDetails/EventDetails.jsx';
import Recurring from '../../../components/_sections/Recurring/Recurring.jsx';
import Validation from '../../../components/_sections/Validation/Validation.jsx';




export default function Edit() {
	const blockProps = useBlockProps( { className: 'bc-event-dates' } );
  const { meta, setMeta } = getStore();
  const [ isLoading, setIsLoading ] = useState( true );
  const [ isLoadingError, setIsLoadingError ] = useState( false );

  const isCondensed = useSelect(select =>
    select(preferencesStore).get('bc-events/details-condensed', 'condensed')
  );

  const { set } = useDispatch(preferencesStore);
  const handleToggle = (value) => {
    set('bc-events/details-condensed', 'condensed', value);
  };
  

  useEffect( () => {
    const fetchKeys = async () => {
      try {
        const response = await apiFetch( { path: `/${REST_NAMESPACE}/get-keys` } );  
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
  



	return (
    <div { ...blockProps }>

      { isLoading ? (
        <p>Loading</p>
      ) : (
        
        <div className="bc-event-dates__container">
          <Validation />
          <SectionToggle
            title={ __( 'Event Details', 'basecadet' ) }
            value={ isCondensed }
            onChange={ (value) => handleToggle(value) }
            asTitle={true}
            titleTag="h2"
          />
          
          { !isCondensed && (
            <>
              <EventDetails />
              <Recurring />
            </>
          )}
        </div>

      ) }


      
    </div>
	);
}
