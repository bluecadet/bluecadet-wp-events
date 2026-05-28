export default function BasicSelect({ id, value, options, onChange, label, className = null }) {

  return (
    <div className={`bc-event-dates__select-group bc-event-dates__input-row ${className ? className : ''}`}>
      <label className="bc-event-dates__label bc-event-dates__select-group-label" htmlFor={id}>{label}</label>
      <select
        className="bc-event-dates__input bc-event-dates__select bc-event-dates__select-group-select"
        id={id}
        value={value}
        onChange={ ( e ) => onChange( e.target.value ) }
      >
        {options.map( ( option ) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ) )}
      </select>
    </div>
  );
}
