import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { Panel, PanelBody, SelectControl, ToggleControl, RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFSettings, User } from '../types';

interface SettingsPanelProps {
	settings: MCFSettings;
	onChange: (key: keyof MCFSettings, value: MCFSettings[keyof MCFSettings]) => void;
}

export default function SettingsPanel({ settings, onChange }: SettingsPanelProps) {
	const [users, setUsers] = useState<User[]>([]);

	useEffect(() => {
		apiFetch<User[]>({ path: '/mcf/v1/users' })
			.then((data) => setUsers(data))
			.catch((error) => console.error('Failed to load users:', error));
	}, []);

	const userOptions = users.map((user) => ({
		label: `${user.name} (${user.email})`,
		value: user.id.toString(),
	}));

	return (
		<Panel>
			<PanelBody title={__('General Settings', 'mcf')} initialOpen={true}>
				<SelectControl
					label={__('Recipient', 'mcf')}
					value={settings.recipient_user_id?.toString() || '1'}
					options={userOptions}
					onChange={(value) => onChange('recipient_user_id', parseInt(value, 10))}
					help={__('User who receives contact form submissions', 'mcf')}
					__next40pxDefaultSize
					__nextHasNoMarginBottom
				/>

				<RadioControl
					label={__('GDPR Mode', 'mcf')}
					selected={settings.gdpr_mode || 'inform'}
					options={[
						{
							label: __('Inform', 'mcf'),
							value: 'inform',
						},
						{
							label: __('Opt-in', 'mcf'),
							value: 'optin',
						},
					]}
					onChange={(value) => onChange('gdpr_mode', value as 'inform' | 'optin')}
					help={__(
						'Inform: Shows informational text. Opt-in: Requires checkbox consent.',
						'mcf'
					)}
				/>

				<ToggleControl
					label={__('Enable Antispam', 'mcf')}
					checked={settings.antispam_enabled ?? true}
					onChange={(value) => onChange('antispam_enabled', value)}
					help={__('Uses honeypot field to prevent spam submissions', 'mcf')}
					__nextHasNoMarginBottom
				/>

				<RadioControl
					label={__('Mail Service', 'mcf')}
					selected={settings.mail_service || 'wp_mail'}
					options={[
						{
							label: __('WordPress PHPMailer (Recommended)', 'mcf'),
							value: 'wp_mail',
						},
						{
							label: __('PHP mail() function', 'mcf'),
							value: 'php_mail',
						},
					]}
					onChange={(value) => onChange('mail_service', value as 'wp_mail' | 'php_mail')}
					help={__(
						'PHPMailer is more reliable. Use PHP mail() only if PHPMailer fails.',
						'mcf'
					)}
				/>
			</PanelBody>
		</Panel>
	);
}
