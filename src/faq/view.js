import { store, getContext } from "@wordpress/interactivity";

store("faqToggle", {
	actions: {
		toggle: () => {
			const context = getContext();
			context.isOpen = !context.isOpen;
		},
	},
	callbacks: {
		logIsOpen: () => {
			const { isOpen } = getContext();
			// Log the value of `isOpen` each time it changes.
			console.log(`Is open: ${isOpen}`);
		},
	},
});
