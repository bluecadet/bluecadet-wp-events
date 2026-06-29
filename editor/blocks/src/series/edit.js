import { useBlockProps } from '@wordpress/block-editor';
import SeriesFields from '../../../components/_fieldSets/SeriesFields/SeriesFields.jsx';

export default function Edit() {
	const blockProps = useBlockProps( { className: 'bc-event-dates' } );

	return (
		<div { ...blockProps }>
			<SeriesFields />
		</div>
	);
}
