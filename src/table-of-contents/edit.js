import { useBlockProps, InspectorControls } from "@wordpress/block-editor";

import "./editor.scss";

export default function Edit(props) {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<p>
				<strong>Table of contents</strong>
				<br />
				Headings will be dynamically listed here.
			</p>
		</div>
	);
}
