import { useBlockProps } from '@wordpress/block-editor';
import { Notice } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { store as editorStore } from '@wordpress/editor';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import DateRow from '../DateRow/DateRow';
import { getStore, REST_NAMESPACE } from '../../_utils/store';




export default function StartEndDate({
  startDateKey = null,
  startTimeKey = null,
  startTimestampKey = null,
  endDateKey = null,
  endTimeKey = null,
  endTimestampKey = null,
  idPrefix = "bc-event-dates"
}) {

  const { meta, setMeta } = getStore();

  const startDate = meta?.[ startDateKey ] ?? '';
	const startTime = meta?.[ startTimeKey ] ?? '';
	const endDate   = meta?.[ endDateKey ]   ?? '';
	const endTime   = meta?.[ endTimeKey ]   ?? '';

  const getTimestamp = async (date, time) => {
    try {
      const params = { date, time };
      const url = addQueryArgs( `/${REST_NAMESPACE}/to-timestamp`, params );
      const data = await apiFetch( { path: url } );
      return data.timestamp;
    } catch ( error ) {
      console.error( error );
    }
  };


  const handleBlur = async () => {
    const updates = {};

    const [ startTs, endTs ] = await Promise.all( [
      startDate  && startTime  ? getTimestamp( startDate, startTime ) : null,
      endDate && endTime ? getTimestamp( endDate, endTime ) : null,
    ] );

    if ( startTs != null ) updates[ startTimestampKey ] = startTs;
    if ( endTs   != null ) updates[ endTimestampKey ]   = endTs;

    setMeta( { ...meta, ...updates } );
  }


  return (
    <>
      <div className="bc-event-dates__group bc-event-dates__group--start">
        <DateRow
          dateID="bce-start-date"
          dateValue={ startDate }
          dateLabel={ __( 'Start Date', 'basecadet' ) }
          onDateChange={ ( val ) => setMeta( { ...meta, [ startDateKey ]: val } ) }
          onDateBlur={ () => handleBlur() }
          timeID="bce-start-time"
          timeValue={ startTime }
          timeLabel={ __( 'Start Time', 'basecadet' ) }
          onTimeChange={ ( val ) => setMeta( { ...meta, [ startTimeKey ]: val } ) }
          onTimeBlur={ () => handleBlur() }
          required
        />
      </div>
      <div className="bc-event-dates__group bc-event-dates__group--end">
          <DateRow
            dateID="bce-end-date"
            dateValue={ endDate }
            dateLabel={ __( 'End Date', 'basecadet' ) }
            dateMin={ startDate || undefined }
            onDateChange={ ( val ) => setMeta( { ...meta, [ endDateKey ]: val } ) }
            onDateBlur={ () => handleBlur() }
            timeID="bce-end-time"
            timeValue={ endTime }
            timeLabel={ __( 'End Time', 'basecadet' ) }
            timeMin={ endDate === startDate && startTime ? startTime : undefined }
            onTimeChange={ ( val ) => setMeta( { ...meta, [ endTimeKey ]: val } ) }
            onTimeBlur={ () => handleBlur() }
            required
          />
      </div>
    </>
  )
}
