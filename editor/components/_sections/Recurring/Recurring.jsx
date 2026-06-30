import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as preferencesStore } from '@wordpress/preferences';
import { store as editorStore } from '@wordpress/editor';
import { getKey, getStore } from '../../_utils/store.js';
import Frequency from '../Frequency/Frequency.jsx';
import CustomOccurences from '../CustomOccurences/CustomOccurences.jsx';
import OmitDates from '../OmitDates/OmitDates.jsx';
import SectionToggle from '../../SectionToggle/SectionToggle.jsx';
import RecurringButton from '../../_formParts/RecurringButton/RecurringButton.jsx';
import RemoveRecurring from '../RemoveRecurring/RemoveRecurring.jsx';
import RecurringAltered from './RecurringAltered.jsx';
import BasicText from '../../_formParts/BasicText/BasicText.js';

export default function Recurring({ useRecurDesc = false, frequencyOptions = {} }) {
  const { keys, meta, setMeta } = getStore();
  const { set } = useDispatch(preferencesStore);

  const META_IS_RECURRING = getKey( 'is_recurring', keys );
  const META_IS_RECURRING_WAS = getKey( 'is_recurring_was', keys );
  const META_RECUR_DESC = getKey( 'recur_desc', keys );

  const IS_RECURRING = meta?.[ META_IS_RECURRING ] ?? false;
  const IS_RECURRING_WAS = meta?.[ META_IS_RECURRING_WAS ] ?? IS_RECURRING;

  const USE_FREQUENCY = meta?.[ getKey( 'use_frequency', keys ) ] ?? false;
  const FREQUENCY_VALUE = meta?.[ getKey( 'freq', keys ) ] ?? '';
 
  const isFreqCondensed = useSelect(select =>
    select(preferencesStore).get('bc-events/frequency-condensed', 'condensed')
  );

  const isDescCondensed = useSelect(select =>
    select(preferencesStore).get('bc-events/recur-desc-condensed', 'condensed')
  );

  const isCustomOccurrencesCondensed = useSelect(select =>
    select(preferencesStore).get('bc-events/custom-occurrences-condensed', 'condensed')
  );

  const isOmitDatesCondensed = useSelect(select =>
    select(preferencesStore).get('bc-events/omit-dates-condensed', 'condensed')
  );

  const toggleCondensed = (key, value) => {
    set(`bc-events/${key}-condensed`, 'condensed', value);
  }

  const updateIsRecurring = ( val ) => {
    setMeta( { ...meta, [ META_IS_RECURRING ]: val } );
  }

  const LOCK_KEY = 'event-dates-remove-recurring-warning';
  const { lockPostSaving, unlockPostSaving } = useDispatch( editorStore );
  const [ userAllowDelete, setUserAllowDelete ] = useState(false);

  useEffect( () => {
    if ( !IS_RECURRING && ( IS_RECURRING !== IS_RECURRING_WAS ) && !userAllowDelete ) {
      lockPostSaving( LOCK_KEY );
    } else {
      unlockPostSaving( LOCK_KEY );
    }
  }, [ IS_RECURRING, IS_RECURRING_WAS, userAllowDelete ] );

  return (
    <div className="bc-event-recurring">
      <div className="bc-event__content-section">
        <RecurringButton
          id="is-recurring"
          value={ meta?.[ META_IS_RECURRING ] ?? false }
          onChange={ ( val ) => updateIsRecurring(val) }
        />
      </div>
      {
        IS_RECURRING && (
          <div className="bc-event-recurring__inner">
            <RecurringAltered />

            { useRecurDesc && (
              <div className="bc-event-recurring__section">              
                <div className="bc-event__content-section">
                  <BasicText 
                    id="recur-desc" 
                    value={meta?.[META_RECUR_DESC]} 
                    onChange={(val) => setMeta({ ...meta, [META_RECUR_DESC]: val })} 
                    label={__('Recurring Description', 'basecadet')} 
                    helperText={useRecurDesc}
                  />
                </div>
              </div>
            )}

            {
              Object.keys(frequencyOptions).length > 0 && (
                <div className="bc-event-recurring__section">
                  <SectionToggle
                    title={ __( 'Frequency Options', 'basecadet' ) }
                    value={ isFreqCondensed }
                    onChange={ (value) => toggleCondensed('frequency', value) }
                    asTitle={false}
                    titleTag='h3'
                  />
                  { !isFreqCondensed && (
                    <div className="bc-event__content-section">
                      <Frequency frequencyOptions={frequencyOptions} />
                    </div>
                  ) }
                </div>
              )
            }
            
            {
              (!USE_FREQUENCY || (USE_FREQUENCY && FREQUENCY_VALUE !== 'concurrent')) && (
                <>
                  <div className="bc-event-recurring__section">
                    <SectionToggle
                      title={ __( 'Specific Dates', 'basecadet' ) }
                      value={ isCustomOccurrencesCondensed }
                      onChange={ (value) => toggleCondensed('custom-occurrences', value) }
                      asTitle={false}
                    />
                    { !isCustomOccurrencesCondensed && (
                      <div className="bc-event__content-section">
                        <CustomOccurences />
                      </div>
                    )}
                  </div>

                  <div className="bc-event-recurring__section">
                    <SectionToggle
                      title={ __( 'Exclusions', 'basecadet' ) }
                      value={ isOmitDatesCondensed }
                      onChange={ (value) => toggleCondensed('omit-dates', value) }
                      asTitle={false}
                    />
                    { !isOmitDatesCondensed && (
                      <div className="bc-event__content-section">
                        <OmitDates />
                      </div>
                    )}
                  </div>
                </>
              )
            }

            
          </div>
        )
      }

      {
        !IS_RECURRING && IS_RECURRING_WAS && (
          <RemoveRecurring 
            onRecurringChange={ (val) => updateIsRecurring(val) }
            onDeleteChange={ (val) => setUserAllowDelete(val) } 
            userAllowDelete={userAllowDelete}   
          />
        )
      }
    </div>
  )
}
