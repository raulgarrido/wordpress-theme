import { useBlockProps } from '@wordpress/block-editor';

/**
 * @return { null } for dynamic blocks.
 * https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/creating-dynamic-blocks/
 */
export default function save() {
	useBlockProps.save();
	return null;
}
