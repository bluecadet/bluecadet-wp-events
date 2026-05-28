export default function BasicCheckbox({ id, checked, onChange, label, flip = false, asToggle = true }) {

  return (
    <div className={`bc-event-dates__checkbox-group bc-event-dates__input-row${flip ? ' bc-event-dates__checkbox-group--flip' : ''}`}>
      { flip && (
        <label className={`bc-event-dates__label ${asToggle ? 'bc-event-dates__checkbox--toggle-label' : 'bc-event-dates__checkbox-label'}`} htmlFor={id}>{label}</label>
      )}
      <input
        className={`${asToggle ? 'bc-event-dates__checkbox--toggle' : 'bc-event-dates__checkbox'}`}
        type="checkbox"
        id={id}
        checked={checked}
        onChange={ ( e ) => onChange( e.target.checked ) }
      />
      { !flip && (
        <label className={`bc-event-dates__label ${asToggle ? 'bc-event-dates__checkbox--toggle-label' : 'bc-event-dates__checkbox-label'}`} htmlFor={id}>{label}</label>
      ) }
    </div>
  );
}
