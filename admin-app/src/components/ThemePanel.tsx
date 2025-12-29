import { useState } from '@wordpress/element';
import { Panel, PanelBody, RadioControl, ColorPicker, ColorIndicator, Button, Popover } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFStyling } from '../types';

interface ThemePanelProps {
	styling: MCFStyling;
	onStylingChange: (key: keyof MCFStyling, value: any) => void;
}

export default function ThemePanel({ styling, onStylingChange }: ThemePanelProps) {
	const [isColorPickerOpen, setIsColorPickerOpen] = useState(false);
	const themePreset = styling.theme_preset || 'default';
	const variant = styling.variant || 'light';
	// Always read from styling prop to ensure we have latest value
	const primaryColor = styling.primary_color || undefined;

	return (
		<Panel>
			<PanelBody title={__('Theme Settings', 'mcf')} initialOpen={true}>
				{/* Theme Preset */}
				<div className="mcf-panel-group">
					<h4>{__('Theme Preset', 'mcf')}</h4>
					<RadioControl
						selected={themePreset}
						options={[
							{
								label: __('Default (Classic)', 'mcf'),
								value: 'default',
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
								label: __('Custom', 'mcf'),
								value: 'custom',
							},
						]}
						onChange={(value) => onStylingChange('theme_preset', value as MCFStyling['theme_preset'])}
					/>
					<p className="description">
						{__('Default theme matches v0.10.0 styling. Modern has rounded corners, Minimal is flat.', 'mcf')}
					</p>
				</div>

				{/* Variant (Light/Dark) */}
				<div className="mcf-panel-group">
					<h4>{__('Color Variant', 'mcf')}</h4>
					<RadioControl
						selected={variant}
						options={[
							{
								label: __('Light', 'mcf'),
								value: 'light',
							},
							{
								label: __('Dark', 'mcf'),
								value: 'dark',
							},
						]}
						onChange={(value) => onStylingChange('variant', value as 'light' | 'dark')}
					/>
					<p className="description">
						{__('Choose between light and dark color schemes.', 'mcf')}
					</p>
				</div>

				{/* Primary Color Override */}
				<div className="mcf-panel-group">
					<h4>{__('Primary Color Override', 'mcf')}</h4>
					<p className="description" style={{ marginBottom: '10px' }}>
						{__('Optional: Override the default button and accent color.', 'mcf')}
					</p>

					<div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
						<Button
							onClick={() => setIsColorPickerOpen(!isColorPickerOpen)}
							style={{ position: 'relative' }}
						>
							<div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
								<ColorIndicator colorValue={primaryColor || '#222222'} />
								<span>{primaryColor ? __('Change Color', 'mcf') : __('Choose Color', 'mcf')}</span>
							</div>
						</Button>

						{primaryColor && (
							<Button
								variant="secondary"
								size='small'
								onClick={() => {
									onStylingChange('primary_color', undefined);
									setIsColorPickerOpen(false);
								}}
							>
								{__('Reset', 'mcf')}
							</Button>
						)}
					</div>

					{isColorPickerOpen && (
						<Popover
							position="bottom left"
							onClose={() => setIsColorPickerOpen(false)}
						>
							<div style={{ padding: '10px' }}>
								<ColorPicker
									color={primaryColor || '#222222'}
									onChangeComplete={(color) => {
										onStylingChange('primary_color', color.hex);
									}}
									enableAlpha={false}
								/>
							</div>
						</Popover>
					)}

					{primaryColor && (
						<p className="description" style={{ marginTop: '8px' }}>
							{__('Current:', 'mcf')} <code>{primaryColor}</code>
						</p>
					)}
				</div>
			</PanelBody>
		</Panel>
	);
}
