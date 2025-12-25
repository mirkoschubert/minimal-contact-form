import { Panel, PanelBody, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFPrivacyTexts } from '../types';

interface PrivacyTextsProps {
	texts: MCFPrivacyTexts;
	onChange: (key: keyof MCFPrivacyTexts, value: string) => void;
}

export default function PrivacyTexts({ texts, onChange }: PrivacyTextsProps) {
	return (
		<Panel>
			<PanelBody title={__('Privacy Texts', 'mcf')} initialOpen={false}>
				<TextareaControl
					label={__('Opt-in Text', 'mcf')}
					value={texts?.optin_text || ''}
					onChange={(value) => onChange('optin_text', value || '')}
					help={__('Shown when GDPR mode is set to Opt-in in Field Settings', 'mcf')}
					rows={3}
					__nextHasNoMarginBottom
				/>

				<TextareaControl
					label={__('Inform Text', 'mcf')}
					value={texts?.inform_text || ''}
					onChange={(value) => onChange('inform_text', value || '')}
					help={__('Shown when GDPR mode is set to Inform in Field Settings', 'mcf')}
					rows={3}
					__nextHasNoMarginBottom
				/>
			</PanelBody>
		</Panel>
	);
}
