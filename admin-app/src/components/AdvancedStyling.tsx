import { Panel, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFStyling } from '../types';

interface AdvancedStylingProps {
	styling: MCFStyling;
	onStylingChange: (advanced: Record<string, string>) => void;
}

export default function AdvancedStyling({ styling, onStylingChange }: AdvancedStylingProps) {
	// Only show when Custom theme is selected
	if (styling.theme_preset !== 'custom') {
		return null;
	}

	// TODO: Implement full styling controls in Phase 4
	return (
		<Panel>
			<PanelBody title={__('Advanced Styling', 'mcf')} initialOpen={false}>
				<p>{__('Advanced styling controls will be implemented in Phase 4', 'mcf')}</p>
				<p>
					{__(
						'All 23+ color pickers, sliders, and styling options will be available here',
						'mcf'
					)}
				</p>
			</PanelBody>
		</Panel>
	);
}
