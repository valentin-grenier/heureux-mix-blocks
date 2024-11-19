import "./editor.scss";

import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import burgerIcon from "./assets/burger-icon.svg";
import {
	PanelBody,
	Panel,
	TextControl,
	Spinner,
	SelectControl,
} from "@wordpress/components";

export default function Edit(props) {
	const blockProps = useBlockProps();
	const { buttonText, menuId } = props.attributes;

	const menus = useSelect((select) => {
		return select("core").getEntityRecords("postType", "wp_navigation");
	}, []);

	const onChangeButtonText = (newButtonText) => {
		props.setAttributes({ buttonText: newButtonText });
	};

	const onChangeMenu = (newMenu) => {
		props.setAttributes({ menuId: newMenu });
	};

	// == filter menu where name = "navigation"
	if (menus) {
		return (
			<>
				<InspectorControls>
					<PanelBody title={__("Settings", "heureux-mix")}>
						<Panel>
							<SelectControl
								label={__("Menu", "heureux-mix")}
								value={menuId}
								options={menus.map((menu) => ({
									label: menu.title.raw,
									value: menu.id,
								}))}
								onChange={onChangeMenu}
							/>
						</Panel>
						<Panel>
							<TextControl
								label={__("Button Text", "heureux-mix")}
								value={buttonText}
								onChange={onChangeButtonText}
							/>
						</Panel>
					</PanelBody>
				</InspectorControls>

				<div {...blockProps}>
					<button className="wp-block-heureux-mix-custom-navigation__button">
						{buttonText}
						<img src={burgerIcon} alt="burger icon" />
					</button>

					<div className="wp-block-heureux-mix-custom-navigation__content">
						<div className="wp-block-heureux-mix-custom-navigation__menu"></div>
					</div>
				</div>
			</>
		);
	} else {
		return <Spinner />;
	}
}
