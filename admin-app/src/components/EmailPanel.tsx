import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
	Panel,
	PanelBody,
	TextControl,
	RadioControl,
	Notice
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFSettings, User } from '../types';

interface EmailPanelProps {
	settings: MCFSettings;
	onChange: (key: keyof MCFSettings, value: MCFSettings[keyof MCFSettings]) => void;
}

export default function EmailPanel({ settings, onChange }: EmailPanelProps) {
	const [users, setUsers] = useState<User[]>([]);

	// Load users for migration fallback
	useEffect(() => {
		apiFetch<User[]>({ path: '/mcf/v1/users' })
			.then((data) => setUsers(data))
			.catch((error) => console.error('Failed to load users:', error));
	}, []);

	// Auto-migrate from recipient_user_id if needed (frontend fallback)
	useEffect(() => {
		if (settings.recipient_user_id && !settings.sender_email && users.length > 0) {
			const user = users.find(u => u.id === settings.recipient_user_id);
			if (user) {
				onChange('sender_name', user.name);
				onChange('sender_email', user.email);
			}
		}
	}, [settings.recipient_user_id, settings.sender_email, users]);

	const mailService = settings.mail_service || 'wp_mail';
	const showSMTP = mailService === 'smtp';

	return (
		<Panel>
			<PanelBody title={__('Email Settings', 'mcf')} initialOpen={true}>
				<div className="mcf-panel-group">
					<TextControl
						label={__('Sender Name', 'mcf')}
						value={settings.sender_name || ''}
						onChange={(value) => onChange('sender_name', value)}
						help={__('Name shown in the "From" field of emails', 'mcf')}
						placeholder={__('Your Name or Company', 'mcf')}
						autoComplete="off"
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<TextControl
						label={__('Sender Email', 'mcf')}
						type="email"
						value={settings.sender_email || ''}
						onChange={(value) => onChange('sender_email', value)}
						help={__('Email address used in the "From" field', 'mcf')}
						placeholder="noreply@example.com"
						autoComplete="off"
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>

					<TextControl
						label={__('Reply-To (Optional)', 'mcf')}
						type="email"
						value={settings.reply_to || ''}
						onChange={(value) => onChange('reply_to', value)}
						help={__('If set, replies will go to this address instead of sender email', 'mcf')}
						placeholder="support@example.com"
						autoComplete="off"
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</div>

				<div className="mcf-panel-group">
					<RadioControl
						label={__('Mail Service', 'mcf')}
						selected={mailService}
						options={[
							{
								label: __('WordPress PHPMailer (Recommended)', 'mcf'),
								value: 'wp_mail',
							},
							{
								label: __('SMTP', 'mcf'),
								value: 'smtp',
							},
							{
								label: __('PHP mail() - Deprecated', 'mcf'),
								value: 'php_mail',
							},
						]}
						onChange={(value) => onChange('mail_service', value as MCFSettings['mail_service'])}
						help={__('Choose how emails are sent', 'mcf')}
					/>

					{mailService === 'php_mail' && (
						<Notice status="warning" isDismissible={false}>
							{__('PHP mail() is deprecated and may be unreliable. Consider using WordPress PHPMailer or SMTP instead.', 'mcf')}
						</Notice>
					)}
				</div>

				{showSMTP && (
					<div className="mcf-panel-group">
						<TextControl
							label={__('SMTP Host', 'mcf')}
							value={settings.smtp_config?.host || ''}
							onChange={(value) => onChange('smtp_config', { ...settings.smtp_config, host: value })}
							placeholder="smtp.gmail.com"
							autoComplete="off"
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>

						<TextControl
							label={__('SMTP Port', 'mcf')}
							type="number"
							value={settings.smtp_config?.port?.toString() || '587'}
							onChange={(value) => onChange('smtp_config', { ...settings.smtp_config, port: parseInt(value) })}
							help={__('Common: 587 (TLS), 465 (SSL), 25', 'mcf')}
							autoComplete="off"
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>

						<TextControl
							label={__('SMTP Username', 'mcf')}
							value={settings.smtp_config?.username || ''}
							onChange={(value) => onChange('smtp_config', { ...settings.smtp_config, username: value })}
							placeholder="your-email@gmail.com"
							autoComplete="off"
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>

						<TextControl
							label={__('SMTP Password', 'mcf')}
							type="password"
							value={settings.smtp_config?.password || ''}
							onChange={(value) => onChange('smtp_config', { ...settings.smtp_config, password: value })}
							help={__('Use app-specific password for services like Gmail', 'mcf')}
							autoComplete="new-password"
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>

						<RadioControl
							label={__('Encryption', 'mcf')}
							selected={settings.smtp_config?.encryption || 'tls'}
							options={[
								{ label: 'TLS (Recommended)', value: 'tls' },
								{ label: 'SSL', value: 'ssl' },
								{ label: 'None', value: 'none' },
							]}
							onChange={(value) => onChange('smtp_config', {
								...settings.smtp_config,
								encryption: value as 'tls' | 'ssl' | 'none'
							})}
						/>
					</div>
				)}
			</PanelBody>
		</Panel>
	);
}
