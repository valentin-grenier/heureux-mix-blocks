import { store, getContext } from "@wordpress/interactivity";

store("burgerToggle", {
	actions: {
		toggle: () => {
			const context = getContext();
			context.isVisible = !context.isVisible;
		},
	},
});
