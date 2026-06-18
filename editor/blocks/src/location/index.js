import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit.js';
import './editor.scss';
import metadata from './block.json';

registerBlockType( metadata.name, {
	...metadata,
	edit: Edit,
	save: () => null,
} );
