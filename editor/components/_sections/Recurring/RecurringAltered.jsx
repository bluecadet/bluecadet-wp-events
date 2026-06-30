import { __ } from '@wordpress/i18n';
import { getKey, getStore } from '../../_utils/store.js';
import { FREQUENCY_OPTIONS, RECURRING_MONTHLY_FREQUENCY_OPTIONS, DAY_OF_WEEK_OPTIONS } from '../../../blocks/src/event-dates/data.js';
import BasicSelect from '../../_formParts/BasicSelect/BasicSelect.js';
import CheckboxButton from '../../_formParts/CheckboxButton/CheckboxButton.jsx';
import CheckboxFormGroup from '../../_formParts/CheckboxFormGroup/CheckboxFormGroup.js';
import { useEffect, useState } from '@wordpress/element';
import { isEqual } from 'lodash';
import lodash from 'lodash';


export default function RecurringAltered() {

  const { keys, meta, setMeta } = getStore();

  const [ hasRecurDiff, setHasRecurDiff ] = useState( false );
  
  const USE_FREQUENCY = meta?.[ getKey( 'use_frequency', keys ) ];
  const START_DATE = meta?.[ getKey( 'start_date', keys ) ];
  const START_TIME = meta?.[ getKey( 'start_time', keys ) ];
  const END_DATE = meta?.[ getKey( 'end_date', keys ) ];
  const END_TIME = meta?.[ getKey( 'end_time', keys ) ];
  const FREQUENCY_VALUE = meta?.[ getKey( 'freq', keys ) ];
  const WEEKLY_DAYS = meta?.[ getKey( 'freq_days', keys ) ];
  const MONTHLY_SCHED = meta?.[ getKey( 'freq_mo_schedule', keys ) ];
  const MONTHLY_DAY = meta?.[ getKey( 'freq_mo_day', keys ) ];
  const MONTHLY_DATE = meta?.[ getKey( 'freq_mo_date', keys ) ];
  const END_TYPE_VALUE = meta?.[ getKey( 'freq_end_type', keys ) ];
  const END_DATE_VALUE = meta?.[ getKey( 'freq_end_date', keys ) ];
  const END_AFTER_X_VALUE = meta?.[ getKey( 'freq_end_after_x', keys ) ];
  const START_TIMESTAMP = meta?.[ getKey('start_timestamp', keys ) ];
  const OCCURENCES = meta?.[ getKey('custom_occurrences', keys ) ];
  const OMISSIONS = meta?.[ getKey('omissions', keys ) ];

  const RECUR_WAS = meta?.[ getKey( 'recur_strategy_was', keys ) ];

  useEffect( () => {

    if ( !RECUR_WAS || RECUR_WAS.length === 0 ) {
      setHasRecurDiff(false);
    } else {
      const currentRecur = {
        use_frequency: USE_FREQUENCY,
        frequency: FREQUENCY_VALUE,
        weekly_days: WEEKLY_DAYS,
        month_schedule: MONTHLY_SCHED,
        month_day: MONTHLY_DAY,
        month_date: MONTHLY_DATE,
        end_type: END_TYPE_VALUE,
        end_date: END_DATE_VALUE,
        end_after_x: parseInt(END_AFTER_X_VALUE, 10),
        start_date_timestamp: `${START_TIMESTAMP}`,
        occurences: OCCURENCES,
        omissions: OMISSIONS,
        primary_start_date: START_DATE,
        primary_start_time: START_TIME,
        primary_end_date: END_DATE,
        primary_end_time: END_TIME,
      }

      const diffs = lodash.filter(
        lodash.keys(currentRecur), 
        key => !lodash.isEqual(currentRecur[key], RECUR_WAS[key])
      );

      setHasRecurDiff( diffs.length > 0 );
    }

  }, [
    USE_FREQUENCY,
    FREQUENCY_VALUE,
    WEEKLY_DAYS,
    MONTHLY_SCHED,
    MONTHLY_DAY,
    MONTHLY_DATE,
    END_TYPE_VALUE,
    END_DATE_VALUE,
    END_AFTER_X_VALUE,
    START_TIMESTAMP,
    RECUR_WAS,
    OCCURENCES,
    OMISSIONS,
    START_DATE,
    START_TIME,
    END_DATE,
    END_TIME,
  ]);


  return (
    <>
      {
        hasRecurDiff && (
          <div className="bc-events__recurring-notice">
            <div className="bc-events__recurring-notice-inner">
              <p className="bc-event-dates__description"><strong>The recurring event pattern has changed.</strong></p>
              <p className="bc-event-dates__description">Existing child events with dates that are not in the current recurring pattern will be deleted. This may include customized child events. Proceed with caution.</p>
            </div>
          </div>
        )
      }
    </>
  )
}