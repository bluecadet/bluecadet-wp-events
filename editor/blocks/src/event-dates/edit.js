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
import ChildEventDetails from '../../../components/_sections/ChildEventDetails/ChildEventDetails.jsx';




export default function Edit() {
	const blockProps = useBlockProps( { className: 'bc-event-dates' } );
  const { meta, setMeta } = getStore();
  const [ isLoading, setIsLoading ] = useState( true );
  const [ isLoadingError, setIsLoadingError ] = useState( false );
  const [ isChild, setIsChild ] = useState( false );
  const [ parentInfo, setParentInfo ] = useState({});
  const postID = useSelect(select => select('core/editor').getCurrentPostId());

  const isCondensed = useSelect(select =>
    select(preferencesStore).get('bc-events/details-condensed', 'condensed')
  );

  const isChildCondensed = useSelect(select =>
    select(preferencesStore).get('bc-events/child-details-condensed', 'condensed')
  );

  const { set } = useDispatch(preferencesStore);
  const handleToggle = (value) => {
    set('bc-events/details-condensed', 'condensed', value);
  };

  const handleChildToggle = (value) => {
    set('bc-events/child-details-condensed', 'condensed', value);
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

    const checkIsChild = async () => {
      if ( postID ) {
        try {
          const url = addQueryArgs( `/${REST_NAMESPACE}/is-child`, {id: postID} );
          const response = await apiFetch( { path: url } );
          setIsChild( response );
        } catch ( error ) {
          console.error( 'Error fetching is_child:', error );
        }
      }
    }

    fetchKeys();
    checkIsChild();

  }, [postID] );
  



	return (
    <div { ...blockProps }>
      { isLoading ? (
        <div className="bce-loader">
          <div className="bce-sr-only">Loading...</div>
          <div className="bce-loader__icon">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.93626 0.37875L15.6063 7.04875C16.1213 7.56475 16.1213 8.41275 15.6063 8.92875L8.93626 15.5988C8.42026 16.1138 7.57226 16.1138 7.05626 15.5988L0.386251 8.92875C-0.12875 8.41275 -0.12875 7.56475 0.386251 7.04875L7.05626 0.37875C7.57626 -0.12625 8.41626 -0.12625 8.93626 0.37875Z" fill="#F0F0F0"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M15.6063 7.04875L8.93626 0.37875C8.41626 -0.12625 7.57626 -0.12625 7.05626 0.37875L0.386251 7.04875C-0.12875 7.56475 -0.12875 8.41275 0.386251 8.92875L7.05626 15.5988C7.57226 16.1138 8.42026 16.1138 8.93626 15.5988L15.6063 8.92875C16.1213 8.41275 16.1213 7.56475 15.6063 7.04875ZM15.2763 8.59875L8.60626 15.2688C8.44526 15.4308 8.22526 15.5228 7.99626 15.5228C7.76726 15.5228 7.54726 15.4308 7.38626 15.2688L0.716251 8.59875C0.554251 8.43775 0.462251 8.21775 0.462251 7.98875C0.462251 7.75975 0.554251 7.53975 0.716251 7.37875L7.38626 0.70875C7.54626 0.54375 7.76626 0.45075 7.99626 0.45075C8.22626 0.45075 8.44626 0.54375 8.60626 0.70875L15.2763 7.37875C15.4383 7.53975 15.5303 7.75975 15.5303 7.98875C15.5303 8.21775 15.4383 8.43775 15.2763 8.59875Z" fill="#0049DB"/>
              <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99622 4.25873C9.67722 4.01073 9.30322 3.84273 8.90622 3.76873C8.70522 3.72773 8.50122 3.70473 8.29622 3.69873H6.84622C6.33021 3.69873 5.90621 4.12273 5.90621 4.63873V5.43873C4.78521 5.77173 4.01221 6.80973 4.01221 7.97873C4.01221 9.14773 4.78521 10.1858 5.90621 10.5188V11.3188C5.90621 11.8348 6.33021 12.2588 6.84622 12.2588H8.24622C8.45022 12.2508 8.65422 12.2308 8.85622 12.1988C9.27122 12.1228 9.66222 11.9478 9.99622 11.6888C10.6313 11.1778 11.0003 10.4038 10.9963 9.58873C10.9823 9.00973 10.7783 8.45073 10.4163 7.99873C10.7923 7.53073 10.9973 6.94873 10.9963 6.34873C11.0003 5.53673 10.6313 4.76573 9.99622 4.25873ZM9.99622 8.12873C10.3573 8.52973 10.5563 9.04973 10.5563 9.58873C10.5733 10.6238 9.83422 11.5298 8.81622 11.7188C8.63822 11.7518 8.45722 11.7718 8.27622 11.7788H6.84622C6.58822 11.7788 6.37621 11.5668 6.37621 11.3088V10.6088C6.48322 10.6138 6.58921 10.6138 6.69622 10.6088H8.54622C8.71222 10.5848 8.86822 10.5158 8.99622 10.4088C9.25322 10.2158 9.40222 9.91073 9.39622 9.58873C9.39522 9.26873 9.24722 8.96673 8.99622 8.76873C8.87222 8.67673 8.72822 8.61573 8.57622 8.58873H6.84622C6.33021 8.58873 5.90621 9.01273 5.90621 9.52873V9.99873C5.05421 9.67873 4.48621 8.85873 4.48621 7.94873C4.48621 7.03873 5.05421 6.21873 5.90621 5.89873V6.43873C5.90621 6.95473 6.33021 7.37873 6.84622 7.37873H7.91622C8.12622 7.39673 8.33622 7.39673 8.54622 7.37873C8.71222 7.35073 8.86822 7.27773 8.99622 7.16873C9.26422 6.98173 9.42422 6.67573 9.42422 6.34873C9.42422 6.02173 9.26422 5.71573 8.99622 5.52873C8.87022 5.43773 8.72722 5.37273 8.57622 5.33873C8.45322 5.33173 8.32922 5.33173 8.20622 5.33873H6.69622C6.58921 5.33373 6.48322 5.33373 6.37621 5.33873V4.63873C6.37321 4.51573 6.42022 4.39673 6.50622 4.30873C6.59622 4.21873 6.71922 4.16873 6.84622 4.16873H8.22622C8.40622 4.15673 8.58622 4.15673 8.76622 4.16873C9.09422 4.23173 9.40322 4.37273 9.66622 4.57873C10.1963 4.99173 10.5063 5.62673 10.5063 6.29873C10.5303 6.85073 10.3483 7.39273 9.99622 7.81873L9.79622 7.99873L9.99622 8.12873ZM6.74622 10.1288C6.64022 10.1388 6.53222 10.1388 6.42621 10.1288V9.47873C6.41921 9.23473 6.60422 9.02473 6.84622 8.99873H8.15622C8.25622 8.98673 8.35622 8.98673 8.45622 8.99873C8.53722 9.00573 8.61322 9.03673 8.67622 9.08873C8.81422 9.19773 8.89522 9.36373 8.89622 9.53873C8.89922 9.71473 8.81722 9.88273 8.67622 9.98873C8.61222 10.0398 8.53622 10.0738 8.45622 10.0888C8.35622 10.0968 8.25622 10.0968 8.15622 10.0888H6.69622L6.74622 10.1288ZM6.37621 5.79873C6.48222 5.78873 6.59021 5.78873 6.69622 5.79873H8.45622C8.53622 5.81373 8.61222 5.84773 8.67622 5.89873C8.81622 6.00573 8.89822 6.17273 8.89622 6.34873C8.89922 6.52473 8.81722 6.69273 8.67622 6.79873C8.61222 6.84973 8.53622 6.88373 8.45622 6.89873C8.28022 6.91373 8.10222 6.91373 7.92622 6.89873H6.84622C6.58822 6.89873 6.37621 6.68673 6.37621 6.42873V5.79873Z" fill="#0049DB"/>
            </svg>
          </div>
        </div>
      ) : (
        <div className="bc-event-dates__container">
          { 
            isChild ? (
              <>
                <SectionToggle
                  title={ __( 'Child Event Details', 'basecadet' ) }
                  value={ isChildCondensed }
                  onChange={ (value) => handleChildToggle(value) }
                  asTitle={true}
                  titleTag="h2"
                />
                { !isChildCondensed && <ChildEventDetails parentData={isChild} /> }
              </>
            ) : (
              <>
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
              </>
            )
          }

          
        </div>
      ) }      
    </div>
	);
}
