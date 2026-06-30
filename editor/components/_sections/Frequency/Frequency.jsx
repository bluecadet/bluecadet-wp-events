import { __ } from '@wordpress/i18n';
import { getKey, getStore } from '../../_utils/store';
import { FREQUENCY_OPTIONS, RECURRING_MONTHLY_FREQUENCY_OPTIONS, DAY_OF_WEEK_OPTIONS } from '../../../blocks/src/event-dates/data.js';
import BasicSelect from '../../_formParts/BasicSelect/BasicSelect';
import CheckboxButton from '../../_formParts/CheckboxButton/CheckboxButton.jsx';
import CheckboxFormGroup from '../../_formParts/CheckboxFormGroup/CheckboxFormGroup.js';
import BasicNumber from '../../_formParts/BasicNumber/BasicNumber.js';
import Consecutive from '../Consecutive/Consecutive.jsx';


export default function Frequency({ frequencyOptions = [] }) {

  const { keys, meta, setMeta } = getStore();

  const META_USE_FREQUENCY = getKey( 'use_frequency', keys );
  const META_FREQUENCY = getKey( 'freq', keys );
  const META_WEEKLY_DAYS = getKey( 'freq_days', keys );
  const META_MONTHLY_SCHEDULE = getKey( 'freq_mo_schedule', keys );
  const META_MONTHLY_DAY = getKey( 'freq_mo_day', keys );
  const META_MONTHLY_DATE = getKey( 'freq_mo_date', keys );
  const META_END_TYPE = getKey( 'freq_end_type', keys );
  const META_END_DATE = getKey( 'freq_end_date', keys );
  const META_END_AFTER_X = getKey( 'freq_end_after_x', keys );
  const META_START_DATE = getKey('start_date', keys );
  const META_FREQ_IS = getKey( 'freq_is', keys );
  const META_FREQ_WAS = getKey( 'freq_was', keys );

  const END_TYPE_VALUE = meta?.[ META_END_TYPE ] ?? 'on_date';
  const END_DATE_VALUE = meta?.[ META_END_DATE ] ?? '';
  const END_AFTER_X_VALUE = meta?.[ META_END_AFTER_X ] ?? 2;

  const USE_FREQUENCY = meta?.[ META_USE_FREQUENCY ] ?? false;
  const FREQUENCY_VALUE = meta?.[ META_FREQUENCY ] ?? '';
  const WEEKLY_DAYS = meta?.[ META_WEEKLY_DAYS ] ?? [];
  const MONTHLY_SCHED = meta?.[ META_MONTHLY_SCHEDULE ] ?? '';
  const MONTHLY_DAY = meta?.[ META_MONTHLY_DAY ] ?? '';
  const MONTHLY_DATE = meta?.[ META_MONTHLY_DATE ] ?? '';
  const START_DATE_VALUE = meta?.[ META_START_DATE ];

  const FREQ_OPTS_MERGED = FREQUENCY_OPTIONS( frequencyOptions );

  

  return (
    <div className="bc-event-dates__frequency">
    
      <p className='bc-event-dates__description'>{__('If your event recurs on a specific schedule, you can set the frequency here.', 'bluecadet-events')}</p>
      
      {
        USE_FREQUENCY && (
          <div className="bc-events__flex-fieldset">
            <div className="bc-event-dates__frequency-row">
              <BasicSelect
                id="recurring-frequency"
                label={ __( 'Frequency', 'basecadet' ) }
                value={ FREQUENCY_VALUE }
                options={ FREQ_OPTS_MERGED }
                onChange={ ( val ) => setMeta( { ...meta, [ META_FREQUENCY ]: val } ) }
              />
            </div>

            { FREQUENCY_VALUE === 'weekly' && (
              <div className="bc-event-dates__frequency-row">
                <CheckboxFormGroup
                  id="recurring-custom-occurrences"
                  label={ __( 'Day(s) of the week', 'basecadet' ) }
                  values={ WEEKLY_DAYS }
                  options={ DAY_OF_WEEK_OPTIONS }
                  onChange={ ( vals ) => {
                    setMeta( { ...meta, [ META_WEEKLY_DAYS ]: vals } )
                  } }
                />
              </div>
            ) }


            { FREQUENCY_VALUE === 'monthly' && (
              <>
                <div className="bc-event-dates__frequency-row">
                  <BasicSelect
                    id="recurring-monthly-schedule"
                    label={ __( 'Schedule', 'basecadet' ) }
                    value={ MONTHLY_SCHED }
                    options={ RECURRING_MONTHLY_FREQUENCY_OPTIONS }
                    onChange={ ( val ) => setMeta( { ...meta, [ META_MONTHLY_SCHEDULE ]: val } ) }
                  />
                </div>

                { MONTHLY_SCHED === 'date' ? (
                    <div className="bc-event-dates__frequency-row bc-event-dates__input-row">
                      <label className="bc-event-dates__label">
                        { __( 'Day of Month', 'basecadet' ) }
                      </label>
                      <input
                        className="bc-event-dates__input"
                        type="number"
                        value={ MONTHLY_DATE }
                        required
                        step={1}
                        min={1}
                        max={31}
                        onChange={ ( e ) => {
                          const value = e.target.value.replace(/^0+(?=\d)/, '');
                          setMeta( { ...meta, [ META_MONTHLY_DATE ]: value } );
                        } }
                      />
                    </div>
                  ) : (
                    <div className="bc-event-dates__frequency-row">
                      <BasicSelect
                        id="recurring-monthly-day"
                        label={ __( 'Weekday', 'basecadet' ) }
                        value={ MONTHLY_DAY }
                        options={ DAY_OF_WEEK_OPTIONS }
                        onChange={ ( val ) => setMeta( { ...meta, [ META_MONTHLY_DAY ]: val } ) }
                      />
                    </div>
                  )
                }
              </>
            ) }


            { FREQUENCY_VALUE === 'consecutive' && (
              <Consecutive/>
            ) }

            { FREQUENCY_VALUE !== 'consecutive' && (
              <>
                <div className="bc-event-dates__frequency-row">
                  <BasicSelect
                    id="end-type"
                    label={ __( 'Ends', 'basecadet' ) }
                    value={ END_TYPE_VALUE }
                    options={ [
                      { value: 'on_date', label: __( 'On Selected Date', 'basecadet' ) },
                      { value: 'after_x', label: __( 'After [X] Events', 'basecadet' ) },
                    ] }
                    onChange={ ( val ) => {
                      setMeta( { ...meta, [ META_END_TYPE ]: val } ) 
                    } }
                  />
                </div>

                { END_TYPE_VALUE === 'on_date' && (
                  <div className="bc-event-dates__frequency-row bc-event-dates__input-row">
                    <label className="bc-event-dates__label" htmlFor="recurring-end-date">{ __( 'At the end of day:', 'basecadet' ) }</label>
                    <input
                      className={ `bc-event-dates__input` }
                      type="date"
                      id="recurring-end-date"
                      value={ END_DATE_VALUE }
                      min={ START_DATE_VALUE || undefined }
                      onChange={ ( e ) => setMeta( { ...meta, [ META_END_DATE ]: e.target.value } ) }
                    />
                  </div>
                ) }

                { END_TYPE_VALUE === 'after_x' && (
                  <div className="bc-event-dates__frequency-row bc-event-dates__input-row">
                    <label className="bc-event-dates__label" htmlFor="recurring-end-after-x">{ __( 'After', 'basecadet' ) }</label>
                    <input
                      className={ `bc-event-dates__input` }
                      type="number"
                      id="recurring-end-after-x"
                      value={ END_AFTER_X_VALUE }
                      min={ 1 }
                      onChange={ ( e ) => setMeta( { ...meta, [ META_END_AFTER_X ]: parseInt(e.target.value, 10) } ) }
                    />
                    <span className="bc-event-dates__label bc-event-dates__recurring-end-after-x-label">{ __( 'events', 'basecadet' ) }</span>
                  </div>
                ) }
              </>
            ) }
            

          </div>
        )
      }

      

      <div className="bc-event-dates__frequency-row bc-event-dates__frequency-toggle">
        <CheckboxButton
          id="use-frequency"
          label={ __( 'Set a Frequency', 'basecadet' ) }
          pressedLabel={ __( 'Remove Frequency', 'basecadet' ) }
          value={USE_FREQUENCY}
          onChange={ ( val ) => {
            setMeta( { ...meta, [ META_USE_FREQUENCY ]: val } )
          } }
          smallOnChecked={true}
        />
      </div>
    </div>
  )
}
