import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import CaretIcon from '../icons/CaretIcon/CaretIcon';

export default function SectionToggle({ title, value, onChange, className = null, asTitle = false, titleTag = 'h3'}) {
  const [isCondensed, setIsCondensed] = useState( value );

  const handleToggle = () => {
    setIsCondensed( ( prev ) => {
      const newValue = !prev;
      onChange && onChange( newValue );
      return newValue;
    } );
  }

  
  return (
    <div className={`bc-events__section-toggle${ isCondensed ? ' closed' : ' open' }`}>
      {
        titleTag && titleTag === 'h2' ? (
          <h2 className="bce-sr-only">
            { title }
          </h2>
        ) : (
          <h3 className="bce-sr-only">
            { title }
          </h3>
        )
      }
      <button type="button" className={`bc-events__section-toggle-button bc-events-title-h1${ isCondensed ? ' closed' : ' open' }${className ? ` ${className}` : ''}`} onClick={ handleToggle }>
        <span className='bc-event-dates__toggle-text'>
          { title}
        </span>
        <span className='bc-event-dates__toggle-icon' aria-hidden="true">
          <CaretIcon />
        </span>
      </button>
    </div>
  )
}
