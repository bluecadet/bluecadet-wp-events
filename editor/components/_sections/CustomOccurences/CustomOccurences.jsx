import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { getKey, getStore } from '../../_utils/store.js';
import BasicCheckbox from '../../_formParts/BasicCheckbox/BasicCheckbox.js';


export default function CustomOccurences() {

  const { keys, meta, setMeta } = getStore();
  const id = 'custom-values'; 

  const META_KEY = getKey( 'custom_occurrences', keys );
  const values = meta?.[ META_KEY ] ?? [];

  const addOccurence = () => {
    const newValues = [ ...values, { start_date: '', customize: false, start_time: '', end_date: '', end_time: '' } ];
    setMeta( { ...meta, [ META_KEY ]: newValues } );
  }

  const removeOccurence = ( index ) => {
    const newValues = values.filter( ( _, i ) => i !== index );
    setMeta( { ...meta, [ META_KEY ]: newValues } );
  }

  const onChange = ( newValues ) => {
    setMeta( { ...meta, [ META_KEY ]: newValues } );
  }


  return (
    <div className="bc-event-dates__custom-occurences">
      <p className='bc-event-dates__description'>{__('If your event occurs on days or times that do not fit a regular schedule, you can add the individual dates here. Individual dates can be added in addition to setting a frequency, or on their own.', 'bluecadet-events')}</p>
      <p className='bc-event-dates__description'>{__('Start Date is the only required field for each set, but you can configure end date and times as needed.', 'bluecadet-events')}</p>
      {values.map( ( occurence, index ) => (
        <OccurenceRow
          key={`${id}-occurence-${index}`}
          occurence={occurence}
          index={index}
          values={values}
          onChange={onChange}
          removeOccurence={removeOccurence}
          id={id}
        />
      ))}

      <div className="bc-event-dates__custom-occurence-add">
        <button type="button" className="bc-events__button bc-events__button--secondary" onClick={ addOccurence }>{ __( 'Add Specific Date', 'basecadet' ) }</button>
      </div>
    </div>
  )
}



function OccurenceRow( { occurence, index, values, onChange, removeOccurence, id } ) {

  const [ showCustomize, setShowCustomize ] = useState( occurence.customize || false );


  return (
    <div className="bc-event-dates__custom-occurence" key={`${id}-occurence-${index}`}>
      {/* <p className='bc-event-dates__label bc-event-dates__custom-occurence-title'>Custom Occurrence {index + 1}</p> */}
      <div className="bc-event-dates__custom-occurence-inputs">
        <div className="bc-event-dates__custom-occurence-dates">
          <div className="bc-events__flex-fieldset">
            <div className="bc-event-dates__date-row-date">
              <label htmlFor={`${id}-start-${index}`} className='bc-event-dates__label'>Start Date</label>
              <input
                className={ `bc-event-dates__input` }
                type="date"
                id={`${id}-start-${index}`}
                value={ occurence.start_date }
                onChange={ ( e ) => {
                  onChange( values.map( ( item, i ) => i === index ? { ...item, start_date: e.target.value } : item ) );
                } }
              />
            </div>


            <div className="bc-event-dates__custom-occurence-customize">
              <BasicCheckbox
                id={`${id}-customize-${index}`}
                label="Add custom times"
                checked={ showCustomize }
                onChange={ ( val ) => {
                  setShowCustomize( val );
                  onChange( values.map( ( item, i ) => i === index ? { ...item, customize: val } : item ) );
                } }
              />
            </div>

            { showCustomize && (
              <>
                
                <div className="bc-event-dates__date-row-date">
                  <label htmlFor={`${id}-start-time-${index}`} className='bc-event-dates__label'>Start Time</label>
                  <input
                    className={ `bc-event-dates__input` }
                    type="time"
                    id={`${id}-start-time-${index}`}
                    value={ occurence.start_time }
                    onChange={ ( e ) => {
                      onChange( values.map( ( item, i ) => i === index ? { ...item, start_time: e.target.value } : item ) );
                    } }
                  />
                </div>
                
                <div className="bc-event-dates__date-row-date">
                  <label htmlFor={`${id}-end-${index}`} className='bc-event-dates__label'>End Date</label>
                  <input
                    className={ `bc-event-dates__input` }
                    type="date"
                    id={`${id}-end-${index}`}
                    value={ occurence.end_date }
                    onChange={ ( e ) => {
                      onChange( values.map( ( item, i ) => i === index ? { ...item, end_date: e.target.value } : item ) );
                    } }
                  />
                </div>

                <div className="bc-event-dates__date-row-date">
                  <label htmlFor={`${id}-end-time-${index}`} className='bc-event-dates__label'>End Time</label>
                  <input
                    className={ `bc-event-dates__input` }
                    type="time"
                    id={`${id}-end-time-${index}`}
                    value={ occurence.end_time }
                    onChange={ ( e ) => {
                      onChange( values.map( ( item, i ) => i === index ? { ...item, end_time: e.target.value } : item ) );
                    } }
                  />
                </div>
              </>

            )}
          </div>
        </div>
        <div className="bc-event-dates__custom-occurence-remove">
          <button type="button" className="bc-event-dates__remove-occurence bc-events__button bc-events__button--small bc-events__button--warning" onClick={ () => removeOccurence( index ) }>{ __( 'Remove', 'basecadet' ) }</button>
        </div>
      </div>
    </div>
  )
}
