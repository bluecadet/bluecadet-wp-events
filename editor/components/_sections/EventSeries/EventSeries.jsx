import { __ } from '@wordpress/i18n';
import RepeatingPostPicker from '../RepeatingPostPicker/RepeatingPostPicker';

export default function EventSeries() {
  return (
    <RepeatingPostPicker
      postType="bc-events-series"
      id="event-series"
      sectionTitle={ __( 'Event Series', 'basecadet' ) }
      metaKey="series_ids"
      addButtonTitle={ __( 'Add Series', 'basecadet' ) }
    />
  )
}
