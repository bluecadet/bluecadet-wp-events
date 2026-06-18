import { useCallback, useMemo, useState, useRef, useEffect } from '@wordpress/element';
import { useSelect, resolveSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { debounce } from '@wordpress/compose';
import AsyncSelect from 'react-select/async';

const PER_PAGE = 20;
const DEBOUNCE_MS = 300;

/**
 * Single-post select rendered as a popout combobox: a native-select-like toggle
 * button that opens a panel with the search input pinned at the top and the
 * results listed below it.
 *
 * - Searches published posts of `postType` as the user types (debounced).
 * - Pre-loads the most recent posts so the panel is never empty on open.
 * - Emits a bare post ID via onChange.
 *
 * @param {Object}            props
 * @param {string}            props.postType Post type slug (e.g. 'bc-events-locations').
 * @param {number|string}     props.value    Currently selected post ID.
 * @param {(id:number)=>void} props.onChange Called with the selected post ID.
 */
export default function PostPicker( { postType, value, onChange } ) {

  const [ isOpen, setIsOpen ] = useState( false );
  const containerRef = useRef( null );

  // Resolve the currently selected post so the toggle can show its title.
  // A pre-set value is a bare ID, so it must be looked up to display correctly.
  const selectedRecord = useSelect(
    ( select ) => {
      if ( ! value ) {
        return null;
      }
      return select( coreStore ).getEntityRecord( 'postType', postType, Number( value ) );
    },
    [ postType, value ]
  );

  const selectedOption = useMemo( () => {
    if ( ! value ) {
      return null;
    }
    return {
      value: Number( value ),
      label: selectedRecord?.title?.rendered || `#${ value }`,
    };
  }, [ value, selectedRecord ] );

  const toOptions = ( records ) =>
    ( records ?? [] ).map( ( record ) => ( {
      value: record.id,
      label: record.title?.rendered || `(no title) #${ record.id }`,
    } ) );

  // Fetch matching published posts. core-data resolves rest_base, auth nonce
  // and caches by query, so repeated searches are cheap.
  const fetchPosts = useCallback(
    ( search ) =>
      resolveSelect( coreStore )
        .getEntityRecords( 'postType', postType, {
          per_page: PER_PAGE,
          status: 'publish',
          orderby: search ? 'relevance' : 'date',
          order: 'desc',
          ...( search ? { search } : {} ),
        } )
        .then( toOptions ),
    [ postType ]
  );

  const loadOptions = useMemo(
    () =>
      debounce( ( inputValue, callback ) => {
        fetchPosts( inputValue ).then( callback );
      }, DEBOUNCE_MS ),
    [ fetchPosts ]
  );

  // Close the panel on outside click / Escape. The block renders inside the
  // editor iframe, so listen on the element's ownerDocument, not `document`.
  useEffect( () => {
    if ( ! isOpen ) {
      return;
    }
    const doc = containerRef.current?.ownerDocument || document;
    const onClick = ( e ) => {
      if ( containerRef.current && ! containerRef.current.contains( e.target ) ) {
        setIsOpen( false );
      }
    };
    const onKey = ( e ) => {
      if ( e.key === 'Escape' ) {
        setIsOpen( false );
      }
    };
    doc.addEventListener( 'mousedown', onClick );
    doc.addEventListener( 'keydown', onKey );
    return () => {
      doc.removeEventListener( 'mousedown', onClick );
      doc.removeEventListener( 'keydown', onKey );
    };
  }, [ isOpen ] );

  const toggleLabel = selectedOption?.label || 'Select a post…';

  return (
    <div className="bc-event-post-picker" ref={ containerRef }>
      <button
        type="button"
        className={ `bc-event-post-picker__toggle${ selectedOption ? '' : ' is-placeholder' }` }
        aria-expanded={ isOpen }
        aria-haspopup="listbox"
        onClick={ () => setIsOpen( ( open ) => ! open ) }
      >
        <span className="bc-event-post-picker__toggle-label">{ toggleLabel }</span>
        <span className="bc-event-post-picker__toggle-chevron" aria-hidden="true">
          <ArrowDownIcon />
        </span>
      </button>

      { isOpen && (
        <div className="bc-event-post-picker__popout">
          <AsyncSelect
            classNamePrefix="bc-event-post-picker"
            autoFocus
            menuIsOpen
            controlShouldRenderValue={ false }
            hideSelectedOptions={ false }
            isClearable={ false }
            backspaceRemovesValue={ false }
            tabSelectsValue={ false }
            cacheOptions
            defaultOptions
            placeholder="Search posts…"
            value={ selectedOption }
            loadOptions={ loadOptions }
            onChange={ ( option ) => {
              onChange( option ? option.value : '' );
              setIsOpen( false );
            } }
            components={ { DropdownIndicator: null, IndicatorSeparator: null } }
          />
        </div>
      ) }
    </div>
  );
}


function ArrowDownIcon() {
  return (
    <svg width="44" height="44" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M15.4226 18.2343C15.0972 17.9219 14.5695 17.9219 14.2441 18.2343C13.9186 18.5467 13.9186 19.0533 14.2441 19.3657L20.9108 25.7657C21.2362 26.0781 21.7638 26.0781 22.0893 25.7657L28.7559 19.3657C29.0814 19.0533 29.0814 18.5467 28.7559 18.2343C28.4305 17.9219 27.9029 17.9219 27.5774 18.2343L21.5 24.0687L15.4226 18.2343Z" fill="currentColor"/>
    </svg>

  )
}

