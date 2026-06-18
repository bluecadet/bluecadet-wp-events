import { __ } from '@wordpress/i18n';
import RepeatingPostPicker from '../RepeatingPostPicker/RepeatingPostPicker';

export default function EventLocations() {
  return (
    <RepeatingPostPicker
      postType="bc-events-locations"
      id="event-locations"
      sectionTitle={ __( 'Event Locations', 'basecadet' ) }
      metaKey="location_ids"
      addButtonTitle={ __( 'Add Location', 'basecadet' ) }
    />
  )
}
