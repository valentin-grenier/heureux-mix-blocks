import { store, getContext } from "@wordpress/interactivity";

store("faqToggle", {
	actions: {
		toggle: () => {
			const context = getContext();
			context.isOpen = !context.isOpen;
		},
	},
});

// == Parse post content headings
const headings = document.querySelectorAll(".entry-content h2");
headings.forEach((heading) => {
	heading.setAttribute("id", cleanString(heading.textContent));
});

function cleanString(input) {
	// == 1. Convert accented characters to normal characters
	const normalized = input.normalize("NFD").replace(/[\u0300-\u036f]/g, "");

	// == 2. Replace spaces with dashes
	const spacesReplaced = normalized.replace(/\s+/g, "-");

	// == 3. Remove special characters (keep letters, numbers, dashes)
	let cleaned = spacesReplaced.replace(/[^a-zA-Z0-9\-]/g, "");

	// == 4. Replace double hyphens with single hyphen
	cleaned = cleaned.replace(/--+/g, "-");

	// == 5. Trim hyphens from start and end
	cleaned = cleaned.replace(/^-+|-+$/g, "");

	// == 6. Convert to lowercase (optional, for URLs or consistency)
	return cleaned.toLowerCase();
}
