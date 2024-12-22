import "./editor.scss";

import { __ } from "@wordpress/i18n";
import { useBlockProps, InspectorControls } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import {
	Spinner,
	SelectControl,
	Panel,
	PanelBody,
} from "@wordpress/components";

export default function Edit(props) {
	const blockProps = useBlockProps();
	const { attributes, setAttributes } = props;
	const { taxonomy } = attributes;
	let selectOptions = [];

	// == Taxonomy "question-taxonomy" fetch
	const questionTaxonomy = useSelect((select) => {
		return select("core").getEntityRecords("taxonomy", "question-taxonomy", {
			per_page: -1,
		});
	});

	// == Dynamic "question" post type fetch with taxonomy filter
	const questions = useSelect(
		(select) => {
			if (taxonomy === "all") {
				// If no taxonomy is selected, fetch all questions
				return select("core").getEntityRecords("postType", "question", {
					per_page: -1,
					status: "publish",
				});
			}
			// Fetch questions filtered by the selected taxonomy
			return select("core").getEntityRecords("postType", "question", {
				per_page: -1,
				status: "publish",
				["question-taxonomy"]: taxonomy, // Filter by taxonomy
			});
		},
		[questionTaxonomy, taxonomy],
	);

	// == Populate options for SearchControl
	if (questionTaxonomy) {
		selectOptions.push({ value: "all", label: "All" });
		questionTaxonomy.forEach((taxonomy) => {
			selectOptions.push({ value: taxonomy.id, label: taxonomy.name });
		});
	} else {
		selectOptions.push({ value: 0, label: "Loading..." });
	}

	// == Handle taxonomy selection change
	const onTaxonomyChange = (selectedTaxonomy) => {
		setAttributes({
			taxonomy: selectedTaxonomy !== 0 ? selectedTaxonomy : null,
		});
	};

	// == Create InspectorControls
	const inspectorControls = (
		<InspectorControls>
			<Panel>
				<PanelBody title="FAQ Settings">
					<SelectControl
						value={taxonomy || 0}
						options={selectOptions}
						label={__("Taxonomy filters", "heureux-mix")}
						onChange={onTaxonomyChange}
					/>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);

	// == Conditional rendering: API has not returned data yet
	if (!questions) {
		return (
			<div {...blockProps}>
				{inspectorControls}

				<Spinner />
			</div>
		);
	}

	// == Conditional rendering: API returned an empty array
	if (questions.length === 0) {
		return (
			<div {...blockProps}>
				{inspectorControls}

				<p>No questions found.</p>
			</div>
		);
	}

	// == Conditional rendering: API returned data
	return (
		<div {...blockProps}>
			{inspectorControls}

			{questions.map((question) => (
				<div className="faq__item" key={question.id}>
					<span className="faq__question">{question.title.raw}</span>

					<div
						className="faq__answer"
						dangerouslySetInnerHTML={{
							__html: question.content.rendered,
						}}
					/>
				</div>
			))}
		</div>
	);
}
