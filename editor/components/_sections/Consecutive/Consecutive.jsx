import { __ } from '@wordpress/i18n';
import { getKey, getStore } from '../../_utils/store.js';
import BasicNumber from '../../_formParts/BasicNumber/BasicNumber.js';
import { useEffect, useState } from '@wordpress/element';

export default function Consecutive() {

  const { keys, meta, setMeta } = getStore();

  const META_FREQ_CON_OFFSET = getKey( 'freq_consecutive_buffer', keys );
  const META_FREQ_CON_COUNT = getKey( 'freq_consecutive_count', keys );
  
  const FREQ_CON_OFFSET = meta?.[ META_FREQ_CON_OFFSET ] ?? 0;
  const FREQ_CON_COUNT = meta?.[ META_FREQ_CON_COUNT ] ?? 2;
  const START_DATE = meta?.[ getKey( 'start_date', keys ) ];
  const START_TIME = meta?.[ getKey( 'start_time', keys ) ];
  const END_DATE = meta?.[ getKey( 'end_date', keys ) ];
  const END_TIME = meta?.[ getKey( 'end_time', keys ) ];

  const [ duration, setDuration ] = useState( 0 );
  const [ durationTotal, setDurationTotal ] = useState( '0' );
  const [ lastStart, setLastStart] = useState( '' );
  const [ lastEnd, setLastEnd ] = useState( '' );
  const [ occurenceList, setOccurenceList ] = useState( [] );

  const minutesToNaturalTime = (minutes) => {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;

    if (hours === 0 && mins === 0) return '0 minutes';

    const hourPart = hours > 0 ? `${hours} ${hours === 1 ? 'hour' : 'hours'}` : '';
    const minPart = mins > 0 ? `${mins} ${mins === 1 ? 'minute' : 'minutes'}` : '';

    return [hourPart, minPart].filter(Boolean).join(' and ');
  }

  const formatDate = (date) => {
    return date.toLocaleString('en-US', {
      month: 'long',
      day: 'numeric',
      year: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true,
    }).replace(',', '').replace(', ', ' at ');
  }

  const getAllOccurrences = (startDate, endDate, count, offsetMinutes) => {
    const durationMs = endDate - startDate;
    const offsetMs = offsetMinutes * 60 * 1000;
    const intervalMs = durationMs + offsetMs;

    return Array.from({ length: count }, (_, i) => {
      const occurrenceStart = new Date(startDate.getTime() + intervalMs * i);
      const occurrenceEnd = new Date(occurrenceStart.getTime() + durationMs);

      return {
        start: formatDate(occurrenceStart),
        end: formatDate(occurrenceEnd),
      };
    });
  }

  useEffect( () => {
    if ( START_DATE && START_TIME && END_DATE && END_TIME ) {
      const startDateTime = new Date( `${START_DATE}T${START_TIME}` );
      const endDateTime = new Date( `${END_DATE}T${END_TIME}` );
      const durationInMs = endDateTime - startDateTime;
      const offsetInMs = FREQ_CON_OFFSET * 60 * 1000;
      
      const durationInMinutes = Math.floor( durationInMs / ( 1000 * 60 ) );
      const durationTotal = durationInMinutes + FREQ_CON_OFFSET;
      
      // const lastStartDate = new Date( startDateTime.getTime() + ( durationTotal * ( FREQ_CON_COUNT - 1 ) * 60 * 1000 ) );
      // const lastEndDate = new Date( lastStartDate.getTime() + ( durationInMinutes * 60 * 1000 ) );

      // setLastStart( formatDate( lastStartDate ) );
      // setLastEnd( formatDate( lastEndDate ) );

      setDuration( minutesToNaturalTime( durationInMinutes ) );
      setDurationTotal( minutesToNaturalTime( durationTotal ) );
      
      setOccurenceList( getAllOccurrences( startDateTime, endDateTime, FREQ_CON_COUNT, FREQ_CON_OFFSET ) );
    }
  }, [START_DATE, START_TIME, END_DATE, END_TIME, FREQ_CON_OFFSET, FREQ_CON_COUNT] );

  return (
    <div className="bc-event-dates__frequency-row bc-event-dates__frequency-row--consecutive">
      <div class="bc-events__flex-fieldset">
        <BasicNumber
          id="recurring-consecutive-offset"
          label={ __( 'Offset (minutes)', 'basecadet' ) }
          value={ meta?.[ META_FREQ_CON_OFFSET ] ?? 0 }
          min={ 0 }
          onChange={ ( val ) => setMeta( { ...meta, [ META_FREQ_CON_OFFSET ]: parseInt(val, 10) } ) }
          helperText={__('How long after the event ends does the next begin?', 'basecadet')}
        />
        <BasicNumber
          id="recurring-consecutive-count"
          label={ __( 'Count', 'basecadet' ) }
          value={ meta?.[ META_FREQ_CON_COUNT ] ?? 2 }
          min={ 2 }
          onChange={ ( val ) => setMeta( { ...meta, [ META_FREQ_CON_COUNT ]: parseInt(val, 10) } ) }
          helperText={__('How many occurrences should there be? (minimum 2)', 'basecadet')}
        />
        <div className="bc-event-dates__consecutive-duration">
          <p className="bc-event-dates__description">This event has a duration of <strong>{ duration }</strong>. With an offset of { FREQ_CON_OFFSET } minutes, there will be an occurrence every <strong>{ durationTotal }</strong>:</p>
          {
            occurenceList.length > 0 && (
              <>
                <ul className="bc-event-dates__consecutive-list">
                  { occurenceList.map( ( occurrence, index ) => (
                    <li key={ `occurrence-list-${index}` } className="bc-event-dates__description">
                      <p><strong>Start:</strong> { occurrence.start }</p>
                      <p><strong>End:</strong> { occurrence.end }</p>
                    </li>
                  ) ) }
                </ul>
              </>
            )
          }

          <div className="bc-event-dates__consecutive-duration-notice bc-events__recurring-notice-inner">
            <p className="bc-event-dates__description"><strong>Specific Dates and Exclusions cannot be added when the Frequency is set to consecutive.</strong></p>
          </div>
          
        </div>
      </div>
    </div>
  )
}
