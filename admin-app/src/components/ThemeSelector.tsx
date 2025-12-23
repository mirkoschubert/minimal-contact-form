import { Panel, PanelBody, RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

interface ThemeSelectorProps {
	currentTheme: string;
	onThemeChange: (theme: 'light' | 'dark' | 'modern' | 'minimal' | 'custom') => void;
}

export default function ThemeSelector({ currentTheme, onThemeChange }: ThemeSelectorProps) {
	return (
		<Panel>
			<PanelBody title={__('Theme Preset', 'mcf')} initialOpen={true}>
				<RadioControl
					selected={currentTheme}
					options={[
						{
							label: __('Light (Default)', 'mcf'),
							value: 'light',
						},
						{
							label: __('Dark', 'mcf'),
							value: 'dark',
						},
						{
							label: __('Modern', 'mcf'),
							value: 'modern',
						},
						{
							label: __('Minimal', 'mcf'),
							value: 'minimal',
						},
						{
							label: __('Custom (Advanced)', 'mcf'),
							value: 'custom',
						},
					]}
					onChange={(value) =>
						onThemeChange(value as 'light' | 'dark' | 'modern' | 'minimal' | 'custom')
					}
				/>
				<p className="description">
					{__('Choose a preset theme or select Custom to access all styling options.', 'mcf')}
				</p>
			</PanelBody>
		</Panel>
	);
}
