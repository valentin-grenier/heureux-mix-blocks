import { store, getContext } from '@wordpress/interactivity';

store( 'faqToggle', {
	actions: {
		toggle: () => {
			const context = getContext();
			context.isOpen = ! context.isOpen;
		},
	},
} );
