import { useEffect, useState } from '@wordpress/element';

export default function RecurringButton({id, value, onChange}) {
  const [isPressed, setIsPressed] = useState(value);

  const togglePressed = () => {
    setIsPressed(!isPressed);
    onChange(!isPressed);
  }

  return (
    <button id={id} className={`bce-events-recurring-button ${isPressed ? 'is-pressed' : ''}`} onClick={togglePressed} aria-pressed={isPressed}>
      { isPressed ? 'Recurring Event Settings' : 'Add Recurring Dates' }
    </button>
  )
}
