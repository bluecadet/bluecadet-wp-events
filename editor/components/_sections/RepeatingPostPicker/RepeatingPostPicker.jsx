import { useState, useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { store as editorStore } from '@wordpress/editor';
import { getKey, getStore } from '../../_utils/store.js';
import PostPicker from '../../_formParts/PostPicker/PostPicker.jsx';
import SectionToggle from '../../SectionToggle/SectionToggle.jsx';

export default function RepeatingPostPicker({
  id,
  sectionTitle,
  addButtonTitle = 'Add',
  removeButtonText = 'Remove',
  metaKey,
  postType
}) {

  const { keys, meta, setMeta } = getStore();
  const { set } = useDispatch(preferencesStore);

  const isCondensed = useSelect(select =>
    select(preferencesStore).get(`bc-events/${id}-condensed`, 'condensed')
  );

  const toggleCondensed = (value) => {
    set(`bc-events/${id}-condensed`, 'condensed', value);
  }

  // Resolve the namespaced meta key (e.g. 'location_ids' -> 'bc_events_location_ids').
  // Until the key map has loaded from the REST endpoint, META is null and we no-op.
  const META = keys ? getKey( metaKey, keys ) : null;

  const values = META ? meta?.[ META ] ?? [] : [];

  const addRow = () => {
    const newValues = [ ...values, '' ];
    onChange( newValues );
  }

  const removeRow = ( index ) => {
    const newValues = values.filter( ( _, i ) => i !== index );
    onChange( newValues );
  }

  const onChange = ( newValues ) => {
    if ( ! META ) {
      return;
    }
    setMeta( { ...meta, [ META ]: newValues } );
  }

  return (
    <div className="bc-event-repeating-picker__section">
      <SectionToggle
        title={ sectionTitle }
        value={ isCondensed }
        onChange={ (value) => toggleCondensed(value) }
        asTitle={false}
        titleTag='h3'
      />
      { !isCondensed && (
        <div className="bc-event__content-section">
          { values.map( ( postID, index ) => (
            <div className="bc-event-repeating-picker__selection" key={`${id}-row-${index}`}>
              <div className="bc-event-dates__input-row">
                <PostPicker postType={ postType } value={ postID } onChange={ ( newPostID ) => {
                  const newValues = [ ...values ];
                  newValues[ index ] = newPostID;
                  onChange( newValues );
                }} />
              </div>
              <div className="bc-event-repeating-picker__section-remove">
                <button type="button" className="bc-events__button bc-events__button--small bc-events__button--warning" onClick={ () => removeRow( index ) }>{ removeButtonText }</button>
              </div>
            </div>
          ) ) }
          <div className="bc-event-repeating-picker__section-add">
            <button type="button" className="bc-events__button bc-events__button--secondary" onClick={ addRow }>{ addButtonTitle }</button>
          </div>
        </div>
      ) }
    </div>
  )
}
