export default function CheckboxFormGroup({ id, values, options, onChange, label }) {

  return (
    <formgroup className="bc-event-dates__checkbox-form-group">
      <div className="bc-event-dates__label bc-event-dates__checkbox-form-group-title">{label}</div>
      <div className="bc-event-dates__checkbox-form-group-inner">
        {options.map( ( option ) => (
          <div key={`formgroup-${id}-${option.value}`} className="bc-event-dates__checkbox-form-group-option">
            <input
              className="bc-event-dates__checkbox--toggle bc-event-dates__checkbox-form-group-checkbox"
              type="checkbox"
              id={`${id}-${option.value}`}
              checked={ values.includes( option.value ) }
              onChange={ ( e ) => {
                const newValues = e.target.checked
                  ? [ ...values, option.value ]
                  : values.filter( ( val ) => val !== option.value );
                onChange( newValues );
              } }
            />
            <label className="bc-event-dates__label bc-event-dates__checkbox-form-group-label" htmlFor={`${id}-${option.value}`}>{option.label}</label>
          </div>
        ) )}
      </div>
    </formgroup>
  )
}
