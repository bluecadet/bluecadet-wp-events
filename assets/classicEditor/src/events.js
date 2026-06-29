/**
 * Classic editor interactions for the Bluecadet Events metabox.
 *
 * Mirrors the behavior of the block editor React components
 * (editor/components/_formParts/*, _sections/*, SectionToggle) without React.
 *
 * The core pattern is the same as React: a single `syncVisibility()` reads the
 * current input state and shows/hides the conditional blocks, and it runs after
 * any change. Toggle buttons and add/remove controls mutate the DOM; nothing is
 * persisted here — hidden input values are kept in sync so the eventual classic
 * form submit captures them. Saving meta is handled later, on form submit.
 */

import TomSelect from 'tom-select';

const ROOT_SELECTOR = '.bc-event-dates--classic';

/* -------------------------------------------------------------------------
 * Small DOM helpers
 * ---------------------------------------------------------------------- */

function setHidden( el, isHidden ) {
  if ( ! el ) {
    return;
  }
  // Reverting to '' restores the stylesheet's display value (flex/block).
  el.style.display = isHidden ? 'none' : '';
}

function isChecked( el ) {
  return !! ( el && el.checked );
}

function selectValue( el ) {
  return el ? el.value : '';
}


/* -------------------------------------------------------------------------
 * syncVisibility — the "render". Reads state, sets visibility. Idempotent.
 * ---------------------------------------------------------------------- */

function syncVisibility( root ) {
  syncHideTime( root );
  syncRecurring( root );
  syncRecurringAltered( root );
  syncFrequency( root );
  syncOccurrenceCustomize( root );
}

// EventDetails: when "Hide Time Display" is checked, the "Hide End Time" option
// is irrelevant (mirrors `!HIDE_TIME_VALUE && <BasicCheckbox … />`).
function syncHideTime( root ) {
  const control = root.querySelector( '.js-bc-hide-time-control input[type="checkbox"]' );
  const endWrap = root.querySelector( '.js-bc-hide-end-time' );
  setHidden( endWrap, isChecked( control ) );
}

// Recurring: show the inner panel when recurring; show the remove-recurring
// warning only when it is being turned off but *was* recurring before.
function syncRecurring( root ) {
  const recurring = root.querySelector( '.bc-event-recurring' );
  if ( ! recurring ) {
    return;
  }

  const valueInput = recurring.querySelector( '.js-bc-recurring-value' );
  const isRecurring = selectValue( valueInput ) === '1';
  const wasRecurring = recurring.dataset.wasRecurring === '1';

  setHidden( recurring.querySelector( '.bc-event-recurring__inner' ), ! isRecurring );
  setHidden( recurring.querySelector( '.js-bc-remove-recurring' ), ! ( ! isRecurring && wasRecurring ) );
}

// RecurringAltered: show the "recurrence pattern changed" notice only when the
// current strategy differs from the snapshot saved on the last save
// (data-recur-was). New posts have no snapshot, so the notice never shows.
// Mirrors _sections/Recurring/RecurringAltered.jsx.
function syncRecurringAltered( root ) {
  const notice = root.querySelector( '.js-bc-recurring-notice' );
  if ( ! notice ) {
    return;
  }

  const raw = notice.dataset.recurWas;
  let stored = null;
  if ( raw ) {
    try {
      stored = JSON.parse( raw );
    } catch ( e ) {
      stored = null;
    }
  }

  // No saved snapshot (new / never-saved-recurring post) — never show.
  if ( ! stored || typeof stored !== 'object' || Object.keys( stored ).length === 0 ) {
    setHidden( notice, true );
    return;
  }

  const current = buildCurrentStrategy( root );
  const changed = serializeStrategy( current ) !== serializeStrategy( stored );
  setHidden( notice, ! changed );
}

// Read the live recurrence strategy from the form, matching the shape of
// `recur_strategy_was` (see EventsSaveAction::recur_strategy_was).
function buildCurrentStrategy( root ) {
  const freq = root.querySelector( '.bc-event-dates__frequency' );

  const weeklyDays = [];
  if ( freq ) {
    freq.querySelectorAll( '.js-bc-frequency-weekly input[type="checkbox"]' ).forEach( ( cb ) => {
      if ( cb.checked ) {
        weeklyDays.push( cb.value );
      }
    } );
  }

  const occurences = [];
  root.querySelectorAll( '.bc-event-dates__custom-occurence' ).forEach( ( row ) => {
    occurences.push( {
      start_date: rowValue( row, 'input[name$="[start_date]"]' ),
      customize: !! ( row.querySelector( 'input[name$="[customize]"]' ) || {} ).checked,
      start_time: rowValue( row, 'input[name$="[start_time]"]' ),
      end_date: rowValue( row, 'input[name$="[end_date]"]' ),
      end_time: rowValue( row, 'input[name$="[end_time]"]' ),
    } );
  } );

  const omissions = [];
  root.querySelectorAll( '.bc-event-dates__exclusions-dates input[type="date"]' ).forEach( ( input ) => {
    omissions.push( input.value );
  } );

  const freqHidden = freq && freq.querySelector( '.bc-event-dates__frequency-toggle input[type="hidden"]' );

  return {
    use_frequency: selectValue( freqHidden ) === '1',
    frequency: freq ? selectValue( freq.querySelector( '.js-bc-freq-select select' ) ) : '',
    weekly_days: weeklyDays,
    month_schedule: freq ? selectValue( freq.querySelector( '.js-bc-freq-mo-schedule select' ) ) : '',
    month_day: freq ? selectValue( freq.querySelector( '.js-bc-frequency-monthly-day select' ) ) : '',
    month_date: freq ? selectValue( freq.querySelector( '.js-bc-frequency-monthly-date input' ) ) : '',
    end_type: freq ? selectValue( freq.querySelector( '.js-bc-freq-end-type select' ) ) : '',
    end_date: freq ? selectValue( freq.querySelector( '.js-bc-frequency-end-date input' ) ) : '',
    end_after_x: freq ? selectValue( freq.querySelector( '.js-bc-frequency-end-after-x input' ) ) : '',
    start_date_timestamp: selectValue( root.querySelector( '.js-bc-start-timestamp' ) ),
    occurences,
    omissions,
    primary_start_date: selectValue( root.querySelector( '.bc-event-dates__group--start input[type="date"]' ) ),
    primary_start_time: selectValue( root.querySelector( '.bc-event-dates__group--start input[type="time"]' ) ),
    primary_end_date: selectValue( root.querySelector( '.bc-event-dates__group--end input[type="date"]' ) ),
    primary_end_time: selectValue( root.querySelector( '.bc-event-dates__group--end input[type="time"]' ) ),
  };
}

function rowValue( row, selector ) {
  const el = row.querySelector( selector );
  return el ? el.value : '';
}

// Normalize a strategy (current OR stored) to a canonical string so the two can
// be compared without being tripped up by type/order differences (PHP casts vs
// DOM strings, checkbox order, etc.).
function serializeStrategy( s ) {
  const str = ( v ) => String( v == null ? '' : v );
  const arrStr = ( v ) => ( Array.isArray( v ) ? v.map( str ) : [] ).slice().sort();

  const occ = ( Array.isArray( s.occurences ) ? s.occurences : [] ).map( ( o ) => ( {
    start_date: str( o && o.start_date ),
    customize: !! ( o && ( o.customize === true || o.customize === '1' || o.customize === 1 ) ),
    start_time: str( o && o.start_time ),
    end_date: str( o && o.end_date ),
    end_time: str( o && o.end_time ),
  } ) );

  const canonical = {
    use_frequency: s.use_frequency === true || s.use_frequency === '1' || s.use_frequency === 1,
    frequency: str( s.frequency ),
    weekly_days: arrStr( s.weekly_days ),
    month_schedule: str( s.month_schedule ),
    month_day: str( s.month_day ),
    month_date: str( s.month_date ),
    end_type: str( s.end_type ),
    end_date: str( s.end_date ),
    end_after_x: str( s.end_after_x ),
    start_date_timestamp: str( s.start_date_timestamp ),
    occurences: occ,
    omissions: arrStr( s.omissions ),
    primary_start_date: str( s.primary_start_date ),
    primary_start_time: str( s.primary_start_time ),
    primary_end_date: str( s.primary_end_date ),
    primary_end_time: str( s.primary_end_time ),
  };

  return JSON.stringify( canonical );
}

// Frequency: gate the whole options fieldset behind the "Set a Frequency"
// toggle, then show the weekly / monthly / end-type sub-fields by value.
function syncFrequency( root ) {
  const freq = root.querySelector( '.bc-event-dates__frequency' );
  if ( ! freq ) {
    return;
  }

  const useFreqInput = freq.querySelector( '.bc-event-dates__frequency-toggle input[type="hidden"]' );
  const useFrequency = selectValue( useFreqInput ) === '1';
  setHidden( freq.querySelector( '.js-bc-frequency-options' ), ! useFrequency );

  const frequency = selectValue( freq.querySelector( '.js-bc-freq-select select' ) );
  setHidden( freq.querySelector( '.js-bc-frequency-weekly' ), frequency !== 'weekly' );
  setHidden( freq.querySelector( '.js-bc-frequency-monthly' ), frequency !== 'monthly' );

  const monthlySchedule = selectValue( freq.querySelector( '.js-bc-freq-mo-schedule select' ) );
  setHidden( freq.querySelector( '.js-bc-frequency-monthly-date' ), monthlySchedule !== 'date' );
  setHidden( freq.querySelector( '.js-bc-frequency-monthly-day' ), monthlySchedule === 'date' );

  const endType = selectValue( freq.querySelector( '.js-bc-freq-end-type select' ) );
  setHidden( freq.querySelector( '.js-bc-frequency-end-date' ), endType !== 'on_date' );
  setHidden( freq.querySelector( '.js-bc-frequency-end-after-x' ), endType !== 'after_x' );
}

// CustomOccurences: each row reveals its custom time fields when "Add custom
// times" is checked.
function syncOccurrenceCustomize( root ) {
  root.querySelectorAll( '.bc-event-dates__custom-occurence' ).forEach( ( row ) => {
    const checkbox = row.querySelector( '.bc-event-dates__custom-occurence-customize input[type="checkbox"]' );
    setHidden( row.querySelector( '.js-bc-occurence-custom' ), ! isChecked( checkbox ) );
  } );
}


/* -------------------------------------------------------------------------
 * Toggle buttons (CheckboxButton / RecurringButton)
 * ---------------------------------------------------------------------- */

// Set the is_recurring state from the recurring button and keep its label /
// pressed state in sync. Used by both the recurring button and the
// "Reset Recurring Settings" button in the remove-recurring panel.
function setRecurring( root, isOn ) {
  const recurring = root.querySelector( '.bc-event-recurring' );
  if ( ! recurring ) {
    return;
  }

  const valueInput = recurring.querySelector( '.js-bc-recurring-value' );
  if ( valueInput ) {
    valueInput.value = isOn ? '1' : '';
  }

  const button = recurring.querySelector( '.bce-events-recurring-button' );
  if ( button ) {
    button.setAttribute( 'aria-pressed', isOn ? 'true' : 'false' );
    button.classList.toggle( 'is-pressed', isOn );
    button.textContent = isOn ? 'Recurring Event Settings' : 'Add Recurring Dates';
  }
}

function handleCheckboxButton( root, button ) {
  const target = button.dataset.target;
  const pressed = button.getAttribute( 'aria-pressed' ) === 'true';
  const next = ! pressed;

  button.setAttribute( 'aria-pressed', next ? 'true' : 'false' );
  button.classList.toggle( 'is-pressed', next );

  const label = next ? button.dataset.pressedLabel : button.dataset.label;
  if ( label != null ) {
    button.textContent = label;
  }

  // Keep the paired hidden input in sync so the value is submitted with the form.
  if ( target ) {
    const input = root.querySelector( `[name="${ cssEscape( target ) }"]` );
    if ( input ) {
      input.value = next ? '1' : '';
    }

    // The "Reset Recurring Settings" button re-enables the real is_recurring
    // value (its own target is a synthetic "<key>__reapply" input).
    if ( target.endsWith( '__reapply' ) ) {
      setRecurring( root, true );
    }
  }

  update( root );
}

function handleRecurringButton( root, button ) {
  const isOn = button.getAttribute( 'aria-pressed' ) !== 'true';
  setRecurring( root, isOn );
  update( root );
}


/* -------------------------------------------------------------------------
 * Section toggles (collapse / expand)
 * ---------------------------------------------------------------------- */

function handleSectionToggle( button ) {
  const wrapper = button.closest( '.bc-events__section-toggle' );
  if ( ! wrapper ) {
    return;
  }

  const content = wrapper.nextElementSibling;
  const isOpen = wrapper.classList.contains( 'open' );
  const next = ! isOpen;

  wrapper.classList.toggle( 'open', next );
  wrapper.classList.toggle( 'closed', ! next );
  button.classList.toggle( 'open', next );
  button.classList.toggle( 'closed', ! next );

  setHidden( content, ! next );
}


/* -------------------------------------------------------------------------
 * Repeating rows (post pickers, custom occurrences, omit dates)
 * ---------------------------------------------------------------------- */

function addFromTemplate( template, destination, before ) {
  if ( ! template || ! destination ) {
    return null;
  }
  const fragment = template.content.cloneNode( true );
  const node = fragment.firstElementChild;
  if ( before ) {
    destination.insertBefore( fragment, before );
  } else {
    destination.appendChild( fragment );
  }
  return node;
}

function handleRepeatingAdd( root, button ) {
  const section = button.closest( '.bc-event-repeating-picker__section' );
  if ( ! section ) {
    return;
  }
  const template = section.querySelector( '.js-bc-repeating-template' );
  const content = section.querySelector( '.bc-event__content-section' );
  const anchor = section.querySelector( '.bc-event-repeating-picker__section-add' );
  const node = addFromTemplate( template, content, anchor );
  if ( node ) {
    initPostPickers( node );
  }
}

function handleOccurrenceAdd( root, button ) {
  const container = button.closest( '.bc-event-dates__custom-occurences' );
  if ( ! container ) {
    return;
  }
  const template = container.querySelector( '.js-bc-occurence-template' );
  const list = container.querySelector( '.js-bc-occurence-list' );
  if ( ! template || ! list ) {
    return;
  }

  // Unique, ever-incrementing index so field names never collide. The save
  // handler can array_values() the resulting sparse array.
  const index = parseInt( container.dataset.nextIndex || '0', 10 );
  container.dataset.nextIndex = String( index + 1 );

  const html = template.innerHTML.split( '__INDEX__' ).join( String( index ) );
  const temp = document.createElement( 'div' );
  temp.innerHTML = html;
  Array.from( temp.children ).forEach( ( child ) => list.appendChild( child ) );

  update( root );
}

function handleOmitAdd( root, button ) {
  const container = button.closest( '.bc-event-dates__exclusions' );
  if ( ! container ) {
    return;
  }
  const template = container.querySelector( '.js-bc-omit-template' );
  const list = container.querySelector( '.bc-event-dates__exclusions-dates' );
  addFromTemplate( template, list );
  update( root );
}

function removeClosest( button, selector, root ) {
  const row = button.closest( selector );
  if ( row ) {
    destroyPostPickers( row );
    row.remove();
    update( root );
  }
}


/* -------------------------------------------------------------------------
 * Post pickers (Tom Select — mirrors the React PostPicker async combobox)
 * ---------------------------------------------------------------------- */

function restConfig() {
  const cfg = window.bcEventsClassic || {};
  return {
    url: ( cfg.restUrl || '/wp-json/' ).replace( /\/$/, '' ),
    nonce: cfg.nonce || '',
  };
}

// Enhance every native post-picker <select> within `scope` into a searchable
// combobox that loads published posts from the REST API as the user types.
// The currently-selected option is already in the <select> (rendered server
// side), so the saved value/title show without an extra request.
function initPostPickers( scope ) {
  scope.querySelectorAll( '.js-bc-post-picker' ).forEach( ( select ) => {
    if ( select.tomselect ) {
      return; // already enhanced
    }

    const postType = select.dataset.postType;
    if ( ! postType ) {
      return;
    }

    const placeholder = select.dataset.placeholder || 'Select a post…';
    const { url, nonce } = restConfig();
    const endpoint = `${ url }/wp/v2/${ postType }`;

    /* eslint-disable no-new */
    new TomSelect( select, {
      valueField: 'id',
      labelField: 'title',
      searchField: 'title',
      maxItems: 1,
      create: false,
      placeholder,
      preload: 'focus',
      loadThrottle: 300,
      // Search field lives at the top of the dropdown (like the React popout),
      // not inside the closed control.
      plugins: [ 'dropdown_input' ],
      load( query, callback ) {
        const params = new URLSearchParams( {
          per_page: '20',
          status: 'publish',
          orderby: query ? 'relevance' : 'date',
          order: 'desc',
          _fields: 'id,title',
        } );
        if ( query ) {
          params.set( 'search', query );
        }

        fetch( `${ endpoint }?${ params.toString() }`, {
          headers: nonce ? { 'X-WP-Nonce': nonce } : {},
          credentials: 'same-origin',
        } )
          .then( ( res ) => ( res.ok ? res.json() : [] ) )
          .then( ( items ) => {
            callback( ( items || [] ).map( ( item ) => ( {
              id: item.id,
              title: ( item.title && item.title.rendered ) || `#${ item.id }`,
            } ) ) );
          } )
          .catch( () => callback() );
      },
      render: {
        option( data, escape ) {
          return `<div class="option">${ escape( data.title ) }</div>`;
        },
        item( data, escape ) {
          return `<div class="item">${ escape( data.title ) }</div>`;
        },
        no_results( data, escape ) {
          return '<div class="no-results">No posts found</div>';
        },
      },
    } );
    /* eslint-enable no-new */
  } );
}

function destroyPostPickers( scope ) {
  scope.querySelectorAll( '.js-bc-post-picker' ).forEach( ( select ) => {
    if ( select.tomselect ) {
      select.tomselect.destroy();
    }
  } );
}


/* -------------------------------------------------------------------------
 * Validation (mirrors _sections/Validation/* — locks saving on error)
 * ---------------------------------------------------------------------- */

const VALIDATION_ICON = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.390362 22.7683C0.867185 23.5396 1.69314 24 2.59966 24H21.4003C22.3071 24 23.1331 23.5396 23.6096 22.7681C24.0865 21.9966 24.1289 21.0518 23.7234 20.2405L14.3232 1.43604C13.8803 0.550293 12.9901 0 11.9999 0C11.0099 0.00024415 10.1197 0.550293 9.67705 1.43604L0.276589 20.2405C-0.128942 21.0518 -0.0864602 21.9968 0.390362 22.7683ZM1.17115 20.6877L10.5716 1.8833C10.848 1.33032 11.3819 1 11.9999 1C12.6181 1 13.1523 1.33008 13.4286 1.8833L22.8288 20.6877C23.0784 21.1868 23.0522 21.7678 22.759 22.2424C22.4658 22.7168 21.958 23 21.4003 23H2.59966C2.04227 23 1.5342 22.7168 1.24122 22.2424C0.947754 21.7681 0.92163 21.1868 1.17115 20.6877Z" fill="currentColor"/><path d="M11.1586 16H12.8414C13.0956 16 13.3079 15.816 13.3307 15.5759L13.9981 8.50733C14.0103 8.37703 13.9642 8.24787 13.8712 8.15145C13.7781 8.05503 13.6466 8 13.5087 8H10.4913C10.3534 8 10.2219 8.05503 10.1288 8.15145C10.0358 8.24787 9.98971 8.37703 10.0019 8.50733L10.6693 15.5759C10.6921 15.816 10.9044 16 11.1586 16ZM12.9716 8.93144L12.3921 15.0686H11.6079L11.0284 8.93144H12.9716Z" fill="currentColor"/><path d="M12.0001 17C10.8971 17 10 17.8971 10 19.0001C10 20.1029 10.8971 21 12.0001 21C13.1029 21 14 20.1029 14 19.0001C14 17.8971 13.1029 17 12.0001 17ZM12.0001 19.9063C11.5003 19.9063 11.0937 19.4997 11.0937 19.0001C11.0937 18.5003 11.5003 18.0937 12.0001 18.0937C12.4997 18.0937 12.9063 18.5003 12.9063 19.0001C12.9063 19.4997 12.4997 19.9063 12.0001 19.9063Z" fill="currentColor"/></svg>';

let validationLocked = false;

function escapeHtml( str ) {
  const div = document.createElement( 'div' );
  div.textContent = String( str == null ? '' : str );
  return div.innerHTML;
}

function isRecurring( root ) {
  const el = root.querySelector( '.js-bc-recurring-value' );
  return !! el && el.value === '1';
}

// Join labels into a natural list: ['a'] -> 'a', ['a','b'] -> 'a and b',
// ['a','b','c'] -> 'a, b and c'.
function joinList( items ) {
  if ( items.length <= 1 ) {
    return items.join( '' );
  }
  return `${ items.slice( 0, -1 ).join( ', ' ) } and ${ items[ items.length - 1 ] }`;
}

// TimestampError: based on _sections/Validation/Validators/TimestampError.jsx.
// First require the start/end date + time fields to be filled (reported by name
// when empty), then — once all four are present — compare the computed
// timestamps for the "same"/"out of order" cases.
function validateTimestamp( root ) {
  const fields = [
    [ 'Start Date', '.bc-event-dates__group--start input[type="date"]' ],
    [ 'Start Time', '.bc-event-dates__group--start input[type="time"]' ],
    [ 'End Date', '.bc-event-dates__group--end input[type="date"]' ],
    [ 'End Time', '.bc-event-dates__group--end input[type="time"]' ],
  ];

  const missing = fields
    .filter( ( [ , sel ] ) => selectValue( root.querySelector( sel ) ) === '' )
    .map( ( [ label ] ) => label );

  if ( missing.length ) {
    return `${ joinList( missing ) } ${ missing.length > 1 ? 'are' : 'is' } required.`;
  }

  // All four fields are present — compare the timestamps. Empty/0 timestamps are
  // treated as falsy (still resolving), so we don't false-flag while they load.
  const s = Number( selectValue( root.querySelector( '.js-bc-start-timestamp' ) ) || 0 );
  const e = Number( selectValue( root.querySelector( '.js-bc-end-timestamp' ) ) || 0 );

  if ( s && e && s === e ) {
    return 'Start and End dates and times cannot be the same.';
  }
  if ( s && e && s > e ) {
    return 'Start Date and Time must be before End Date and Time.';
  }
  return null;
}

// MissingFields: only relevant when recurring. Mirrors MissingFields.jsx,
// including its existing quirks (e.g. the monthly "date" schedule check).
function validateMissingFields( root ) {
  const labels = [];
  if ( ! isRecurring( root ) ) {
    return labels;
  }

  const s = buildCurrentStrategy( root );
  const freq = s.frequency || 'daily';

  if ( s.use_frequency && freq ) {
    if ( freq === 'weekly' && ! ( s.weekly_days.length > 0 ) ) {
      labels.push( 'Day(s) of the week' );
    }

    if ( freq === 'monthly' && s.month_schedule === 'date' && ! ( s.month_day !== '' ) ) {
      labels.push( 'Day of Month' );
    }

    const endType = s.end_type || 'on_date';
    if ( endType === 'on_date' && ! ( s.end_date !== '' ) ) {
      labels.push( 'At the end of day' );
    }
    if ( endType === 'after_x' && ! ( s.end_after_x !== '' ) ) {
      labels.push( 'After [X] Events' );
    }
  }

  if ( s.occurences.length && s.occurences.some( ( occ ) => ! occ.start_date ) ) {
    labels.push( 'Specific Dates (empty start date values)' );
  }
  if ( s.omissions.length && s.omissions.some( ( date ) => date === '' ) ) {
    labels.push( 'Exclusions (empty date values)' );
  }

  return labels;
}

// MissingRecurringStrategy: recurring with neither a frequency nor specific
// dates. Mirrors MissingRecurringStrategy.jsx.
function validateMissingStrategy( root ) {
  if ( ! isRecurring( root ) ) {
    return false;
  }
  const s = buildCurrentStrategy( root );
  return ! s.use_frequency && s.occurences.length === 0;
}

function noticeHtml( innerHtml ) {
  return (
    '<div class="bce-events-validation-notice bce-events-validation-notice--error">' +
      '<div class="bce-events-validation-notice__inner">' +
        `<div class="bce-events-validation-notice__icon" aria-hidden="true">${ VALIDATION_ICON }</div>` +
        `<div class="bce-events-validation-notice__content">${ innerHtml }</div>` +
      '</div>' +
    '</div>'
  );
}

// Run all validators, render the notices, and lock/unlock saving. Idempotent —
// safe to call after any change.
function runValidation( root ) {
  const notices = [];

  const ts = validateTimestamp( root );
  if ( ts ) {
    notices.push( noticeHtml( escapeHtml( ts ) ) );
  }

  const missing = validateMissingFields( root );
  if ( missing.length ) {
    notices.push( noticeHtml(
      '<p><strong>Required Fields are missing values:</strong></p>' +
      `<p>${ escapeHtml( missing.join( ', ' ) ) }</p>`
    ) );
  }

  if ( validateMissingStrategy( root ) ) {
    notices.push( noticeHtml(
      '<p><strong>Missing Recurring Strategy:</strong></p>' +
      '<p>A recurring event must have a frequency or specific dates defined.</p>'
    ) );
  }

  const container = root.querySelector( '.js-bc-validation' );
  if ( container ) {
    container.innerHTML = notices.join( '' );
    container.classList.toggle( 'has-error', notices.length > 0 );
  }

  setSaveLock( notices.length > 0 );
}

// Lock saving the way the block editor's lockPostSaving does — by disabling the
// classic editor's Publish/Update and Save Draft buttons.
function setSaveLock( locked ) {
  validationLocked = locked;
  [ '#publish', '#save-post' ].forEach( ( sel ) => {
    const btn = document.querySelector( sel );
    if ( ! btn ) {
      return;
    }
    btn.disabled = locked;
    btn.classList.toggle( 'disabled', locked );
    if ( locked ) {
      btn.setAttribute( 'aria-disabled', 'true' );
    } else {
      btn.removeAttribute( 'aria-disabled' );
    }
  } );
}

// Block the keyboard/Enter submit path too (the disabled buttons cover clicks).
function initSubmitGuard() {
  const form = document.querySelector( 'form#post' );
  if ( ! form || form.dataset.bcGuard ) {
    return;
  }
  form.dataset.bcGuard = '1';
  form.addEventListener( 'submit', ( e ) => {
    if ( validationLocked ) {
      e.preventDefault();
      const notice = document.querySelector( '.js-bc-validation' );
      if ( notice ) {
        notice.scrollIntoView( { behavior: 'smooth', block: 'center' } );
      }
    }
  } );
}


/* -------------------------------------------------------------------------
 * Timestamps (mirror StartEndDate's blur → /to-timestamp REST conversion)
 * ---------------------------------------------------------------------- */

function restNamespace() {
  return ( window.bcEventsClassic && window.bcEventsClassic.namespace ) || 'bc-events/v1';
}

function toTimestamp( date, time ) {
  const { url, nonce } = restConfig();
  const params = new URLSearchParams( { date, time } );
  return fetch( `${ url }/${ restNamespace() }/to-timestamp?${ params.toString() }`, {
    headers: nonce ? { 'X-WP-Nonce': nonce } : {},
    credentials: 'same-origin',
  } )
    .then( ( res ) => ( res.ok ? res.json() : null ) )
    .then( ( data ) => ( data && data.timestamp != null ? data.timestamp : null ) )
    .catch( () => null );
}

function isPrimaryDateTime( el ) {
  if ( ! el || ! el.closest ) {
    return false;
  }
  const group = el.closest( '.bc-event-dates__group--start, .bc-event-dates__group--end' );
  return !! group && ( el.type === 'date' || el.type === 'time' );
}

// Recompute the start/end timestamp hidden inputs from the date/time fields,
// then re-validate. Like React, a timestamp is only set when both its date and
// time are present (an incomplete pair leaves the previous value untouched).
function recomputeTimestamps( root ) {
  const sd = selectValue( root.querySelector( '.bc-event-dates__group--start input[type="date"]' ) );
  const st = selectValue( root.querySelector( '.bc-event-dates__group--start input[type="time"]' ) );
  const ed = selectValue( root.querySelector( '.bc-event-dates__group--end input[type="date"]' ) );
  const et = selectValue( root.querySelector( '.bc-event-dates__group--end input[type="time"]' ) );

  Promise.all( [
    ( sd && st ) ? toTimestamp( sd, st ) : Promise.resolve( null ),
    ( ed && et ) ? toTimestamp( ed, et ) : Promise.resolve( null ),
  ] ).then( ( [ sts, ets ] ) => {
    const sEl = root.querySelector( '.js-bc-start-timestamp' );
    const eEl = root.querySelector( '.js-bc-end-timestamp' );
    if ( sEl && sts != null ) {
      sEl.value = String( sts );
    }
    if ( eEl && ets != null ) {
      eEl.value = String( ets );
    }
    runValidation( root );
  } );
}

// syncVisibility + runValidation — the full re-render after a state change.
function update( root ) {
  syncVisibility( root );
  runValidation( root );
}


/* -------------------------------------------------------------------------
 * Wiring
 * ---------------------------------------------------------------------- */

// Minimal CSS.escape fallback for attribute selectors built from meta keys.
function cssEscape( value ) {
  if ( window.CSS && typeof window.CSS.escape === 'function' ) {
    return window.CSS.escape( value );
  }
  return String( value ).replace( /["\\\]\[]/g, '\\$&' );
}

function initRoot( root ) {
  // Seed the occurrence index counter from the rows already rendered.
  root.querySelectorAll( '.bc-event-dates__custom-occurences' ).forEach( ( container ) => {
    const list = container.querySelector( '.js-bc-occurence-list' );
    const count = list ? list.querySelectorAll( '.bc-event-dates__custom-occurence' ).length : 0;
    container.dataset.nextIndex = String( count );
  } );

  root.addEventListener( 'click', ( e ) => {
    const t = e.target;

    const sectionToggle = t.closest( '.bc-events__section-toggle-button' );
    if ( sectionToggle ) {
      handleSectionToggle( sectionToggle );
      return;
    }

    const recurringButton = t.closest( '.bce-events-recurring-button' );
    if ( recurringButton ) {
      handleRecurringButton( root, recurringButton );
      return;
    }

    const checkboxButton = t.closest( '.bc-events__checkbox-button' );
    if ( checkboxButton ) {
      handleCheckboxButton( root, checkboxButton );
      return;
    }

    if ( t.closest( '.js-bc-repeating-add' ) ) {
      handleRepeatingAdd( root, t.closest( '.js-bc-repeating-add' ) );
      return;
    }
    if ( t.closest( '.js-bc-repeating-remove' ) ) {
      removeClosest( t.closest( '.js-bc-repeating-remove' ), '.bc-event-repeating-picker__selection', root );
      return;
    }

    if ( t.closest( '.js-bc-occurence-add' ) ) {
      handleOccurrenceAdd( root, t.closest( '.js-bc-occurence-add' ) );
      return;
    }
    if ( t.closest( '.js-bc-occurence-remove' ) ) {
      removeClosest( t.closest( '.js-bc-occurence-remove' ), '.bc-event-dates__custom-occurence', root );
      return;
    }

    if ( t.closest( '.js-bc-omit-add' ) ) {
      handleOmitAdd( root, t.closest( '.js-bc-omit-add' ) );
      return;
    }
    if ( t.closest( '.js-bc-omit-remove' ) ) {
      removeClosest( t.closest( '.js-bc-omit-remove' ), '.bc-event-dates__exclusion', root );
      return;
    }
  } );

  // Re-render conditional visibility + validation whenever a control changes.
  // Editing a start/end date or time first recomputes the timestamps (via REST)
  // so the timestamp validator runs against fresh values.
  root.addEventListener( 'change', ( e ) => {
    syncVisibility( root );
    if ( isPrimaryDateTime( e.target ) ) {
      recomputeTimestamps( root );
    } else {
      runValidation( root );
    }
  } );

  // Enhance the post pickers already on the page.
  initPostPickers( root );

  // Block saving while invalid (covers Enter-key submit).
  initSubmitGuard();

  // Initial render so the DOM + save lock reflect the saved state on load.
  update( root );
}

function init() {
  document.querySelectorAll( ROOT_SELECTOR ).forEach( initRoot );
}

if ( document.readyState === 'loading' ) {
  document.addEventListener( 'DOMContentLoaded', init );
} else {
  init();
}
