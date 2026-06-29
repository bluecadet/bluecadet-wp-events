import { useBlockProps } from '@wordpress/block-editor';
import LocationFields from '../../../components/_fieldSets/LocationFields/LocationFields.jsx';

export default function Edit() {
	const blockProps = useBlockProps( { className: 'bc-event-dates' } );

	return (
		<div { ...blockProps }>
			<LocationFields />
		</div>
	);
}
