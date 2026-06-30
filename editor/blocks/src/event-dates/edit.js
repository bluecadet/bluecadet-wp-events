import { useBlockProps } from '@wordpress/block-editor';
import EventFields from '../../../components/_fieldSets/EventFields/EventFields.jsx';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit() {

	return (
		<div { ...useBlockProps() }>
			<div className="bc-event-dates">
				<EventFields />
			</div>
			<ServerSideRender
				block="bc-events/event-dates"
				attributes={ {} }
				EmptyResponsePlaceholder={ () => null }
			/>
		</div>
	);
}
