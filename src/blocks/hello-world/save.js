import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * @return { WPElement } Element to render.
 */
export default function save() {
	return (
		<p {...useBlockProps.save()}>
			{__(
				'Gutenberg Block – Frontend View!',
				'hello-world'
			)}
		</p>
	);
}
