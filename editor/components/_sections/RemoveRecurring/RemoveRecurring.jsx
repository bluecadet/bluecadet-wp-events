import { __ } from '@wordpress/i18n';
import { getKey, getStore } from '../../_utils/store';
import BasicSelect from '../../_formParts/BasicSelect/BasicSelect.js';

export default function RemoveRecurring() {

  const { keys, meta, setMeta } = getStore();

  const META_REMOVE_RECURRING = getKey( 'remove_recurring', keys );
  const REMOVE_RECURRING = meta?.[ META_REMOVE_RECURRING ] ?? 'delete';

  return (
    <div className="bc-event__content-section">
      <div className="bc-event__remove-recurring">
        <div className="bc-event__remove-recurring-content bc-event__content-section">
          <h2 className="bc-events-title-h1">Remove Recurring Events</h2>
          <p className="bc-event-dates__description">It seems like you're trying to remove recurring settings from an event that was previously set as recurring. This action will remove all recurring dates and settings associated with this event.</p>
          <p className="bc-event-dates__description">Please choose how you would like to handle the existing recurring dates:</p>

          <ul className="bc-event__remove-recurring-list">
            <li>This action will not be implemented until after you save the event.</li>
            <li>This action cannot be reversed once implemented.</li>
          </ul>
        </div>
        
        <div className="bc-event__remove-recurring-action bc-event__content-section">
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
        </div>
      </div>
    </div>
  )
}
