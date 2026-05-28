import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { useEffect, useState } from '@wordpress/element';
import { getKey, getStore } from '../../_utils/store';
import StartEndDate from '../../_formParts/StartEndDate/StartEndDate.jsx';
import BasicCheckbox from '../../_formParts/BasicCheckbox/BasicCheckbox.js';

export default function EventDetails() {
  
  const { keys, meta, setMeta } = getStore();

  const META_START_DATE         = getKey( 'start_date', keys );
  const META_START_TIME         = getKey( 'start_time', keys );
  const META_START_TIMESTAMP    = getKey( 'start_timestamp', keys );
  const META_END_DATE           = getKey( 'end_date', keys );
  const META_END_TIME           = getKey( 'end_time', keys );
  const META_END_TIMESTAMP      = getKey( 'end_timestamp', keys );
  const META_HIDE_TIME          = getKey( 'hide_time_display', keys );
  const META_HIDE_END_TIME      = getKey( 'hide_end_time_display', keys );

  const HIDE_TIME_VALUE         = meta?.[ META_HIDE_TIME ] ?? false;
  const HIDE_END_TIME_VALUE     = meta?.[ META_HIDE_END_TIME ] ?? false;

  return (
    <div className="bc-event-details bc-event__content-section">
      
      <div className="bc-events__flex-fieldset">
        <StartEndDate
          startDateKey={ META_START_DATE }
          startTimeKey={ META_START_TIME }
          startTimestampKey={ META_START_TIMESTAMP }
          endDateKey={ META_END_DATE }
          endTimeKey={ META_END_TIME }
          endTimestampKey={ META_END_TIMESTAMP }
          idPrefix="bc-event-dates"
        />
      </div>

      <div class="bc-event-dates__divider"></div>

      {/* Options */}
      <div className="bc-events__flex-fieldset">
        <BasicCheckbox
          id="hide-time"
          label={ __( 'Hide Time Display', 'basecadet' ) }
          checked={ HIDE_TIME_VALUE }
          onChange={ ( val ) => setMeta( { ...meta, [ META_HIDE_TIME ]: val } ) }
        />
        {
          !HIDE_TIME_VALUE && (
            <BasicCheckbox
              id="hide-end-time"
              label={ __( 'Hide End Time Display', 'basecadet' ) }
              checked={ HIDE_END_TIME_VALUE }
              onChange={ ( val ) => setMeta( { ...meta, [ META_HIDE_END_TIME ]: val } ) }
            />
          )
        }
      </div>
    </div>
  )
}