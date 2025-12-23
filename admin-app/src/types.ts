/**
 * TypeScript type definitions for Minimal Contact Form
 */

export interface MCFSettings {
	recipient_user_id: number;
	gdpr_mode: 'inform' | 'optin';
	antispam_enabled: boolean;
	mail_service: 'wp_mail' | 'php_mail';
}

export interface MCFFieldConfig {
	order: string[];
	enabled: Record<string, boolean>;
	labels: Record<string, string>;
	placeholders: Record<string, string>;
	custom_fields: CustomField[];
}

export interface CustomField {
	id: string;
	type: 'text' | 'email' | 'tel' | 'textarea';
	label: string;
	placeholder: string;
	required: boolean;
	validation?: string;
}

export interface MCFPrivacyTexts {
	optin_text: string;
	inform_text: string;
}

export interface MCFStyling {
	theme_preset: 'light' | 'dark' | 'modern' | 'minimal' | 'custom';
	advanced: Record<string, string>;
}

export interface MCFOptions {
	version: string;
	settings: MCFSettings;
	fields: MCFFieldConfig;
	privacy_texts: MCFPrivacyTexts;
	styling: MCFStyling;
}

export interface ThemePreset {
	label: string;
	colors: Record<string, string>;
}

export interface User {
	id: number;
	name: string;
	email: string;
}

export interface Notice {
	type: 'success' | 'error' | 'warning' | 'info';
	message: string;
}

declare global {
	interface Window {
		mcfAdmin: {
			apiUrl: string;
			nonce: string;
			pluginUrl: string;
		};
	}
}
