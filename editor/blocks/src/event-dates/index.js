import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit.js';
import './editor.scss';
import metadata from './block.json';
import BlockIcon from '../../../components/BlockIcon/BlockIcon.jsx';


registerBlockType( metadata.name, {
	...metadata,
	icon: BlockIcon,
	edit: Edit,
	save: () => null,
} );
