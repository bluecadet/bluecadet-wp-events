export default function BasicText({ id, value, options, onChange, label, className = null, helperText = null }) {

  return (
    <div className={`bc-event-dates__text-group bc-event-dates__input-row ${className ? className : ''}`}>
      <label className="bc-event-dates__label bc-event-dates__text-group-label" htmlFor={id}>{label}</label>
      <input
        type="text"
        className="bc-event-dates__input bc-event-dates__text bc-event-dates__text-group-text"
        id={id}
        value={value}
        onChange={ ( e ) => onChange( e.target.value ) }
      />
      {helperText && <p className="bc-event-dates__helper-text">{helperText}</p>}
    </div>
  );
}
