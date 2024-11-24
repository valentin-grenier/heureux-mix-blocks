import { store, getContext } from "@wordpress/interactivity";

store("burgerToggle", {
	actions: {
		toggle: () => {
			const context = getContext();
			context.isVisible = !context.isVisible;

			if (context.isVisible) {
				document.documentElement.style.overflow = "hidden";
			} else {
				document.documentElement.style.overflow = "auto";
			}
		},
	},
});
