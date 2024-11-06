import "./editor.scss";

import { useBlockProps } from "@wordpress/block-editor";
import { useSelect } from "@wordpress/data";
import { Spinner } from "@wordpress/components";

export default function Edit(props) {
	const blockProps = useBlockProps();

	// == Dynamic "question" post type fetch
	const questions = useSelect((select) => {
		return select("core").getEntityRecords("postType", "question", {
			per_page: 3,
		});
	});

	// == Debug
	console.log(questions);

	// == Conditional rendering: API has not returned data yet
	if (!questions) {
		return (
			<div {...blockProps}>
				<Spinner />
			</div>
		);
	}

	// == Conditional rendering: API returned an empty array
	if (questions.length === 0) {
		return (
			<div {...blockProps}>
				<p>No questions found.</p>
			</div>
		);
	}

	// == Conditional rendering: API returned data
	return (
		<div {...blockProps}>
			{questions.map((question) => (
				<div className="faq__item" key={question.id}>
					<span className="faq__question">{question.title.raw}</span>

					<div
						className="faq__answer"
						dangerouslySetInnerHTML={{ __html: question.content.rendered }}
					/>
				</div>
			))}
		</div>
	);
}
