import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { getKey, getStore } from '../../_utils/store.js';

export default function OmitDates() {

  const { keys, meta, setMeta } = getStore();
  const idPrefix = 'bc-event-omit-dates';

  const META_KEY = getKey( 'omissions', keys );
  const values = meta?.[ META_KEY ] ?? [];

  const addDate = () => {
    const newValues = [ ...values, '' ];
    onChange( newValues );
  }

  const removeDate = ( index ) => {
    const newValues = values.filter( ( _, i ) => i !== index );
    onChange( newValues );
  }

  const onChange = ( newValues ) => {
    setMeta( { ...meta, [ META_KEY ]: newValues } );
  }

  return (
    <div className="bc-event-dates__exclusions">
      <p className='bc-event-dates__description'>{__('If your event has specific dates that should be excluded from a schedule, you can add them here.', 'bluecadet-events')}</p>
      <div className="bc-event-dates__exclusions-dates">
        { values.map( ( omitDate, index ) => (
          <div className="bc-event-dates__exclusion" key={`${idPrefix}-exclusion-${index}`}>
            <div className="bc-event-dates__input-row">
              <label htmlFor={`${idPrefix}-date-${index}`} className='u-sr-only'>Omit Date {index + 1}</label>
              <input
                className={ `bc-event-dates__input` }
                type="date"
                id={`${idPrefix}-date-${index}`}
                value={ omitDate }
                onChange={ ( e ) => {
                  const newValues = [ ...values ];
                  newValues[ index ] = e.target.value;
                  onChange( newValues );
                }}
              />
            </div>
            
            <button type="button" className="bc-events__button bc-events__button--small bc-events__button--warning" onClick={ () => removeDate( index ) }>{ __( 'Remove', 'basecadet' ) }</button>
          </div>
        ) ) }
      </div>
      <button type="button" className="bc-events__button bc-events__button--secondary" onClick={ addDate }>{ __( 'Add Exclusion', 'basecadet' ) }</button>
    </div>
  )
}
