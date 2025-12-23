import { Panel, PanelBody, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFPrivacyTexts } from '../types';

interface PrivacyTextsProps {
	texts: MCFPrivacyTexts;
	gdprMode: 'inform' | 'optin' | undefined;
	onChange: (key: keyof MCFPrivacyTexts, value: string) => void;
}

export default function PrivacyTexts({ texts, gdprMode, onChange }: PrivacyTextsProps) {
	return (
		<Panel>
			<PanelBody title={__('Privacy Texts', 'mcf')} initialOpen={false}>
				<TextareaControl
					label={__('Opt-in Text', 'mcf')}
					value={texts?.optin_text || ''}
					onChange={(value) => onChange('optin_text', value || '')}
					help={__('Shown when GDPR mode is set to Opt-in', 'mcf')}
					rows={3}
					__nextHasNoMarginBottom
				/>

				<TextareaControl
					label={__('Inform Text', 'mcf')}
					value={texts?.inform_text || ''}
					onChange={(value) => onChange('inform_text', value || '')}
					help={__('Shown when GDPR mode is set to Inform', 'mcf')}
					rows={3}
					__nextHasNoMarginBottom
				/>

				<div className="mcf-privacy-preview">
					<p>
						<strong>{__('Current mode:', 'mcf')}</strong>{' '}
						{gdprMode === 'optin' ? __('Opt-in', 'mcf') : __('Inform', 'mcf')}
					</p>
					<p>
						<em>
							{gdprMode === 'optin'
								? __('Showing opt-in checkbox', 'mcf')
								: __('Showing informational text', 'mcf')}
						</em>
					</p>
				</div>
			</PanelBody>
		</Panel>
	);
}
