export default function BasicNumber({ 
  id, 
  value, 
  label, 
  onChange, 
  min = null, 
  max = null, 
  step = 1, 
  className = null, 
  helperText = null 
}) {
  return (
    <div className={`bc-event-dates__text-group bc-event-dates__input-row ${className ? className : ''}`}>
      <label className="bc-event-dates__label bc-event-dates__text-group-label" htmlFor={id}>{label}</label>
      <input
        type="number"
        min={min ?? 0}
        step={step ?? 1}
        max={max ?? 999999}
        className="bc-event-dates__input bc-event-dates__number bc-event-dates__text-group-text"
        id={id}
        value={value}
        onChange={ ( e ) => onChange( e.target.value ) }
      />
      {helperText && <p className="bc-event-dates__helper-text">{helperText}</p>}
    </div>
  );
}
