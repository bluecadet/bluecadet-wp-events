import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { useEffect, useState } from '@wordpress/element';
import { getKey, getStore } from '../../_utils/store';
import BasicCheckbox from '../../_formParts/BasicCheckbox/BasicCheckbox.js';
import { getSettings } from '@wordpress/date';

export default function ChildEventDetails({parentData}) {

  const { keys, meta, setMeta } = getStore();
  const { parent_url, parent_id, parent_title } = parentData;

  const DENY_OVERRIDE = meta?.[getKey( 'child_deny_override', keys )] ?? false;
  const START_DATE = meta?.[getKey( 'start_date', keys )];
  const END_DATE = meta?.[getKey( 'end_date', keys )];
  const START_TIME = meta?.[getKey( 'start_time', keys )];
  const END_TIME = meta?.[getKey( 'end_time', keys )];

  const convertTo12Hour = (time) => {
    if (!time) return null;
    const [hour, minute] = time.split(':');
    const hourNum = parseInt(hour);
    const ampm = hourNum >= 12 ? 'PM' : 'AM';
    const hour12 = hourNum % 12 || 12; // Convert to 12-hour format
    return `${hour12}:${minute} ${ampm}`;
  };

  const convertYMD = (date) => {
    if (!date) return null;
    const [year, month, day] = date.split('-');
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return `${months[parseInt(month, 10) - 1]} ${day}, ${year}`;
  }

  const startDate = convertYMD(START_DATE);
  const startTime = START_TIME ? convertTo12Hour(START_TIME) : null;
  const endDate = convertYMD(END_DATE);
  const endTime = END_TIME ? convertTo12Hour(END_TIME) : null;  


  return (
    <div className="bc-event-details bc-event__content-section">
      <div className="bc-events__flex-fieldset">
        <div className="bc-event__child-content">
          <p className="bc-event__child-content-title"><strong>This is a child event of <a href={parent_url} target="_blank" rel="noopener noreferrer">{parent_title}</a></strong></p> 
        </div>
        <div className="bc-event__child-overview">
          <p className="bc-event-dates__description bc-events__child-overview-row">
            Event Start: <span>{ startDate ? startDate : 'N/A' } { startTime ? `at ${startTime}` : '' }</span>
          </p>     
          <p className="bc-event-dates__description bc-events__child-overview-row">
            Event End: <span>{ endDate ? endDate : 'N/A' } { endTime ? `at ${endTime}` : '' }</span>
          </p>     
        </div>
        <div className="bc-event__child-content">
          <p className="bc-event-dates__description">By default, content from {parent_title} will overwrite all content changes made in the editor for this event. You can override this behavior by checking the option below.</p>
          <div className="bc-events__flex-fieldset">
            <BasicCheckbox
              id="bce-deny-content-overrides"
              label={ __( 'Override content for this event', 'basecadet' ) }
              checked={ DENY_OVERRIDE }
              onChange={ ( val ) => setMeta( { ...meta, [ getKey( 'child_deny_override', keys ) ]: val } ) }
            />
          </div>
          <div className="bc-event__child-content-notes">
            <p className="bc-event-dates__description"><strong>Note that by checking this option:</strong></p>
            <ul>
              <li className="bc-event-dates__description">Content will not longer sync from the parent. Any changes from the parent will need to be added manually.</li>
              <li className="bc-event-dates__description">This will only effect content from the editor. Taxonomies and other meta values will still be overwritten by parent data.</li>
              <li className="bc-event-dates__description">If unchecked, content will not be synced until the <strong>parent</strong> event is saved.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  )
}
