import { useEffect, useState } from '@wordpress/element';

export default function CheckboxButton(
  {
    id, 
    value, 
    checked, 
    onChange, 
    label, 
    pressedLabel = null, 
    smallOnChecked = false
  }
) {
  const [isPressed, setIsPressed] = useState(value);
  const pressedLabelText = pressedLabel ? pressedLabel : label;
  const classes = "bc-events__checkbox-button bc-events__button bc-events__button--secondary";
  const checkedClasses = smallOnChecked ? "bc-events__checkbox-button bc-events__button bc-events__button--secondary bc-events__button--small" : classes;



  const togglePressed = () => {
    setIsPressed(!isPressed);
    onChange(!isPressed);
  }

  return (
    <button 
      type="button" 
      className={isPressed ? checkedClasses : classes}
      onClick={togglePressed} 
      aria-pressed={isPressed}
    >
      { isPressed ? `${pressedLabelText}` : `${label}` }
    </button>
  )
}
