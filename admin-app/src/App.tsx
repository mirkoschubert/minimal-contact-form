import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { Button, Spinner, Notice } from '@wordpress/components';

import SettingsPanel from './components/SettingsPanel';
import ThemeSelector from './components/ThemeSelector';
import FieldManager from './components/FieldManager';
import AdvancedStyling from './components/AdvancedStyling';
import PrivacyTexts from './components/PrivacyTexts';

import type { MCFOptions, Notice as NoticeType } from './types';

export default function App() {
	const [settings, setSettings] = useState<MCFOptions | null>(null);
	const [loading, setLoading] = useState<boolean>(true);
	const [saving, setSaving] = useState<boolean>(false);
	const [notice, setNotice] = useState<NoticeType | null>(null);

	// Load settings on mount
	useEffect(() => {
		apiFetch<MCFOptions>({ path: '/mcf/v1/settings' })
			.then((data) => {
				setSettings(data);
				setLoading(false);
			})
			.catch((error: Error) => {
				console.error('Failed to load settings:', error);
				setNotice({
					type: 'error',
					message: __('Failed to load settings. Please refresh the page.', 'mcf'),
				});
				setLoading(false);
			});
	}, []);

	// Save settings
	const handleSave = () => {
		setSaving(true);
		setNotice(null);

		apiFetch({
			path: '/mcf/v1/settings',
			method: 'POST',
			data: settings,
		})
			.then(() => {
				setSaving(false);
				setNotice({
					type: 'success',
					message: __('Settings saved successfully!', 'mcf'),
				});
				// Auto-hide success message after 3 seconds
				setTimeout(() => setNotice(null), 3000);
			})
			.catch((error: Error) => {
				setSaving(false);
				setNotice({
					type: 'error',
					message: error.message || __('Failed to save settings. Please try again.', 'mcf'),
				});
			});
	};

	// Update settings helper
	const updateSettings = <K extends keyof MCFOptions>(
		category: K,
		key: keyof MCFOptions[K],
		value: MCFOptions[K][keyof MCFOptions[K]]
	) => {
		if (!settings) return;

		setSettings({
			...settings,
			[category]: {
				...(settings[category] as object),
				[key]: value,
			},
		});
	};

	if (loading) {
		return (
			<div className="mcf-admin-loading">
				<Spinner />
				<p>{__('Loading settings...', 'mcf')}</p>
			</div>
		);
	}

	if (!settings) {
		return (
			<div className="mcf-admin-error">
				<Notice status="error" isDismissible={false}>
					{__('Failed to load settings. Please refresh the page.', 'mcf')}
				</Notice>
			</div>
		);
	}

	return (
		<div className="mcf-admin-app">
			{notice && (
				<Notice
					status={notice.type}
					isDismissible={true}
					onDismiss={() => setNotice(null)}
				>
					{notice.message}
				</Notice>
			)}

			<div className="mcf-admin-layout">
				<div className="mcf-admin-sidebar">
					<SettingsPanel
						settings={settings.settings}
						onChange={(key, value) => updateSettings('settings', key, value)}
					/>

					<ThemeSelector
						currentTheme={settings.styling?.theme_preset || 'light'}
						onThemeChange={(theme) => updateSettings('styling', 'theme_preset', theme)}
					/>

					<PrivacyTexts
						texts={settings.privacy_texts}
						gdprMode={settings.settings?.gdpr_mode}
						onChange={(key, value) => updateSettings('privacy_texts', key, value)}
					/>
				</div>

				<div className="mcf-admin-main">
					<FieldManager
						fields={settings.fields}
						onFieldsChange={(fields) => setSettings({ ...settings, fields })}
					/>

					<AdvancedStyling
						styling={settings.styling}
						onStylingChange={(advanced) =>
							setSettings({
								...settings,
								styling: { ...settings.styling, advanced },
							})
						}
					/>
				</div>
			</div>

			<div className="mcf-admin-footer">
				<Button variant="primary" onClick={handleSave} isBusy={saving} disabled={saving}>
					{saving ? __('Saving...', 'mcf') : __('Save Settings', 'mcf')}
				</Button>
			</div>
		</div>
	);
}
