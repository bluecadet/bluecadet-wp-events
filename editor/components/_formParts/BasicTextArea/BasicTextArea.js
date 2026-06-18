export default function BasicTextArea({ id, value, options, onChange, label, className = null, rows = 2, useLineBreak = false, useParagraphBreak = false }) {

  const handleValue = ( val ) => {
    let newVal = val;

    if ( useLineBreak ) {
      newVal = newVal.replaceAll( '\n', '<br>' );
    }

    if ( useParagraphBreak ) {
      newVal = newVal.replaceAll( '\n', '</p><p>' );
      newVal = `<p>${newVal}</p>`;
    }

    onChange( newVal );
  };

  const displayValue = ( val ) => {
    if ( !val ) return val;
    let display = val;

    if ( useLineBreak ) {
      display = display.replaceAll( '<br>', '\n' );
    }

    if ( useParagraphBreak ) {
      display = display.replace( /^<p>/, '' ).replace( /<\/p>$/, '' );
      display = display.replaceAll( '<\/p><p>', '\n' );
    }

    return display;
  };

  return (
    <div className={`bc-event-dates__text-group bc-event-dates__input-row ${className ? className : ''}`}>
      <label className="bc-event-dates__label bc-event-dates__text-group-label" htmlFor={id}>{label}</label>
      <textarea
        className="bc-event-dates__input bc-event-dates__text bc-event-dates__text-group-text"
        id={id}
        value={ displayValue( value ) }
        onChange={ ( e ) => handleValue( e.target.value ) }
        rows={rows ? rows : 2}
      />
    </div>
  );
}
