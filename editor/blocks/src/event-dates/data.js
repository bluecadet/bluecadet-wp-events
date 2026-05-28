import { __ } from '@wordpress/i18n';

export const FREQUENCY_OPTIONS = [
  { value: 'daily', label: __( 'Daily', 'basecadet' ) },
  { value: 'weekly', label: __( 'Weekly', 'basecadet' ) },
  { value: 'monthly', label: __( 'Monthly', 'basecadet' ) },
  // { value: 'yearly', label: __( 'Yearly', 'basecadet' ) },
];


export const DAY_OF_WEEK_OPTIONS = [
  { value: 'sunday', label: __( 'Sunday', 'basecadet' ) },
  { value: 'monday', label: __( 'Monday', 'basecadet' ) },
  { value: 'tuesday', label: __( 'Tuesday', 'basecadet' ) },
  { value: 'wednesday', label: __( 'Wednesday', 'basecadet' ) },
  { value: 'thursday', label: __( 'Thursday', 'basecadet' ) },
  { value: 'friday', label: __( 'Friday', 'basecadet' ) },
  { value: 'saturday', label: __( 'Saturday', 'basecadet' ) },
];


export const RECURRING_MONTHLY_FREQUENCY_OPTIONS = [
  { value: 'first', label: __( 'Every First', 'basecadet' ) },
  { value: 'second', label: __( 'Every Second', 'basecadet' ) },
  { value: 'third', label: __( 'Every Third', 'basecadet' ) },
  { value: 'fourth', label: __( 'Every Fourth', 'basecadet' ) },
  { value: 'last', label: __( 'Every Last', 'basecadet' ) },
  { value: 'every_other', label: __( 'Every Other', 'basecadet' ) },
  { value: 'date', label: __( 'On a Specific Date', 'basecadet' ) },
];


