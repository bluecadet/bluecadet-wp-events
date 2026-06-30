import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { getStore, getKey } from '../../../_utils/store';
import ValidationNotice from '../ValidationNotice.jsx';

export default function MissingFields({ onError, onSuccess }) {
  const { keys, meta } = getStore();
  const [ missingFields, setMissingFields ] = useState( [] );

  const IS_RECURRING = meta?.[ getKey( 'is_recurring', keys ) ];
  const USE_FREQ = meta?.[ getKey( 'use_frequency', keys ) ];
  const FREQ = meta?.[ getKey( 'freq', keys ) ] ?? 'daily';
  const WEEKLY_DAYS = meta?.[ getKey( 'freq_days', keys ) ] ?? [];
  const MONTHLY_SCHED = meta?.[ getKey( 'freq_mo_schedule', keys ) ];
  const MONTHLY_DAY = meta?.[ getKey( 'freq_mo_day', keys ) ];
  const MONTHLY_DATE = meta?.[ getKey( 'freq_mo_date', keys ) ];
  const FREQ_END_TYPE = meta?.[ getKey( 'freq_end_type', keys ) ] ?? 'on_date';
  const FREQ_END_DATE = meta?.[ getKey( 'freq_end_date', keys ) ];
  const FREQ_END_AFTER_X = meta?.[ getKey( 'freq_end_after_x', keys ) ];
  const OCCURENCES = meta?.[ getKey( 'custom_occurrences', keys ) ];
  const OMISSIONS = meta?.[ getKey( 'omissions', keys ) ];

  useEffect( () => {

    const validationChecks = {}

    if ( IS_RECURRING ) {
      if ( USE_FREQ && FREQ ) {

        if ( FREQ === 'weekly' ) {
          validationChecks.freq_days = {
            test: WEEKLY_DAYS.length > 0,
            label: __( 'Day(s) of the week', 'basecadet' ),
          }
        }

        if ( FREQ === 'monthly' ) {
          if ( MONTHLY_SCHED === 'date' ) {
            validationChecks.freq_mo_day = {
              test: MONTHLY_DAY !== '',
              label: __( 'Day of Month', 'basecadet' ),
            }
          }
        }

        if ( FREQ !== 'concurrent' ) {

          if ( FREQ_END_TYPE === 'on_date' ) {
            validationChecks.freq_end_date = {
              test: FREQ_END_DATE !== '',
              label: __( 'At the end of day', 'basecadet' ),
            }
          }

          if ( FREQ_END_TYPE === 'after_x' ) {
            validationChecks.freq_end_after_x = {
              test: FREQ_END_AFTER_X !== '',
              label: __( 'After [X] Events', 'basecadet' ),
            }
          }
        }
      }

      if ( OCCURENCES && OCCURENCES.length && OCCURENCES.some( occ => ! occ.start_date ) ) {
        validationChecks.custom_occurrences = {
          test: false,
          label: __( 'Specific Dates (empty start date values)', 'basecadet' ),
        }
      }

      if ( OMISSIONS && OMISSIONS.length && OMISSIONS.some( occ => occ === '' ) ) {
        validationChecks.omissions = {
          test: false,
          label: __( 'Exclusions (empty date values)', 'basecadet' ),
        }
      }
    }


    const dateFields = Object.keys( validationChecks ).filter( key => validationChecks[ key ].test === false ).map( key => validationChecks[ key ].label );
    
    setMissingFields(dateFields);

    if ( dateFields.length ) {
      onError( 'missing_fields' );
    } else {
      onSuccess( 'missing_fields' );
    }
  
  }, [ 
    IS_RECURRING,
    USE_FREQ,
    FREQ,
    WEEKLY_DAYS,
    MONTHLY_SCHED,
    MONTHLY_DAY,
    MONTHLY_DATE,
    FREQ_END_TYPE,
    FREQ_END_DATE,
    FREQ_END_AFTER_X,
    OCCURENCES,
    OMISSIONS
  ] );

  return (
    <>
      { missingFields && missingFields.length > 0 && (
        <ValidationNotice>
          <p><strong>Required Fields are missing values:</strong></p>
          <p>{ missingFields.join( ', ' ) }</p>
        </ValidationNotice>
      ) }
    </>
  )
}