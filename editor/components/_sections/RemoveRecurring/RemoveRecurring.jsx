import { __ } from '@wordpress/i18n';
import { getKey, getStore } from '../../_utils/store';
import BasicSelect from '../../_formParts/BasicSelect/BasicSelect.js';
import BasicCheckbox from '../../_formParts/BasicCheckbox/BasicCheckbox.js';
import CheckboxButton from '../../_formParts/CheckboxButton/CheckboxButton.jsx';

export default function RemoveRecurring({onRecurringChange, onDeleteChange, userAllowDelete}) {

  const { keys, meta, setMeta } = getStore();

  const META_REMOVE_RECURRING = getKey( 'remove_recurring', keys );
  const REMOVE_RECURRING = meta?.[ META_REMOVE_RECURRING ] ?? 'delete';
  const META_IS_RECURRING = getKey( 'is_recurring', keys );
  const IS_RECURRING = meta?.[ META_IS_RECURRING ] ?? false;


  return (
    <div className="bc-event__content-section">
      <div className="bc-event__remove-recurring">
        <div className="bc-event__remove-recurring-content bc-event__content-section">
          <h2 className="bc-events-title-h1">Remove Recurring Events</h2>
          <p className="bc-event-dates__description">It seems like you're trying to remove recurring settings from an event that was previously set as recurring. This action will not take place until you save the post.</p>
          
          <CheckboxButton
            id="use-recurring-reapply"
            label={ __( 'Reset Recurring Settings', 'basecadet' ) }
            value={IS_RECURRING}
            onChange={ ( val ) => setMeta( { ...meta, [ META_IS_RECURRING ]: val } ) }
            smallButton={true}
          />

        </div>

          
        
        {/* <div className="bc-event__remove-recurring-action bc-event__content-section">
          <BasicSelect
            id="remove-recurring"
            label="Action:"
            value={ REMOVE_RECURRING }
            onChange={ ( val ) => setMeta( { ...meta, [ META_REMOVE_RECURRING ]: val } ) }
            options={[
              { value: 'delete', label: 'Delete all recurring dates' },
              { value: 'to_posts', label: 'Convert child events to top level events' },
            ]}
          />
          <div className="bc-event__remove-recurring-action-helper">
            {
              REMOVE_RECURRING === 'to_posts' ? (
                <p className="bc-event-dates__description">All recurring events created from the current settings will be converted to their own posts and no longer associated with a recurring relationship.</p>
              ) : (
                <p className="bc-event-dates__description">All recurring dates will be permanently deleted. This is irreversible.</p>
              )
            }

          </div>
        </div> */}
        <div className="bc-event__remove-recurring-action bc-event__content-section">
          <div className="bc-event__remove-recurring-desc">
            <p className="bc-event__remove-recurring-desc-title">This action cannot be undone.</p>
            
            <p>Once saved:</p>

            <ul className="bc-event__remove-recurring-list">
              <li>All child recurring events will be deleted</li>
              <li>All existing recurring settings will be reset to default settings</li>
            </ul>

            <p className="bc-event__important">To continue, confirm by checking below:</p>
          </div>

          <BasicCheckbox
            id="user-allow-delete"
            label={ __( 'I understand that this action cannot be undone and I want to proceed.', 'basecadet' ) }
            checked={ userAllowDelete }
            onChange={ ( val ) => {
              onDeleteChange(val) 
              setMeta( { ...meta, [ META_REMOVE_RECURRING ]: 'delete' } )
            } }
          />
        </div>

      </div>
    </div>
  )
}
