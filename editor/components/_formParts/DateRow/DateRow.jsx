import React from 'react';

export default function DateRow({
  dateID,
  dateValue,
  dateLabel,
  dateMin = null,
  onDateChange = null,
  onDateBlur = null,
  timeID,
  timeValue,
  timeLabel,
  timeMin = null,
  onTimeChange = null,
  onTimeBlur = null,
  required = false
}) {


  return (
    <div className={`bc-event-dates__date-row ${required ? 'bc-event-dates__date-row--required' : ''}`}>
      <div className='bc-event-dates__date-row-date'>
        <label className='bc-event-dates__label' htmlFor={dateID}>{dateLabel}{required ? (<span className="req">*</span>) : ''}</label>
        <input
          className='bc-event-dates__input'
          type='date'
          id={dateID}
          value={dateValue}
          onChange={(e) => onDateChange && onDateChange(e.target.value)}
          onBlur={(e) => onDateBlur && onDateBlur(e.target.value)}
          min={dateMin}
          {...(required ? { required: true } : {})}
        />
      </div>
      <div className='bc-event-dates__date-row-time'>
        <label className='bc-event-dates__label' htmlFor={timeID}>{timeLabel}{required ? (<span className="req">*</span>) : ''}</label>
        <input
          className='bc-event-dates__input'
          type='time'
          id={timeID}
          value={timeValue}
          min={timeMin}
          onChange={(e) => onTimeChange && onTimeChange(e.target.value)}
          onBlur={(e) => onTimeBlur && onTimeBlur(e.target.value)}
          {...(required ? { required: true } : {})}
        />
      </div>
    </div>
  )
}
