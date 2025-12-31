/**
 * TypeScript type definitions for Minimal Contact Form
 */

export interface SMTPConfig {
  enabled: boolean
  host: string
  port: number
  username: string
  password: string
  encryption: 'tls' | 'ssl' | 'none'
  from_name: string
  from_email: string
}

export interface MCFSettings {
  recipient_user_id?: number // Legacy (optional, kept for backwards compat)
  sender_name?: string // New flexible email field
  sender_email?: string // New flexible email field
  reply_to?: string // New flexible email field
  antispam_enabled: boolean
  mail_service: 'wp_mail' | 'php_mail' | 'smtp'
  smtp_config: SMTPConfig
}

export interface MCFFieldConfig {
  field_groups: {
    company: { enabled: boolean }
    name: { mode: 'single' | 'split'; enabled: boolean }
    contact: { mode: 'email' | 'email-phone'; enabled: boolean }
    subject: { enabled: boolean }
    message: { enabled: boolean }
    gdpr: { enabled: boolean; mode: 'inform' | 'optin' }
    submit: { alignment: 'left' | 'right' }
  }
  labels: Record<string, string>
  placeholders: Record<string, string>
  hide_labels: boolean
}

export interface CustomField {
  id: string
  type: 'text' | 'email' | 'tel' | 'textarea'
  label: string
  placeholder: string
  required: boolean
  validation?: string
}

export interface MCFPrivacyTexts {
  optin_text: string
  inform_text: string
}

export interface MCFStyling {
  theme_preset: 'default' | 'modern' | 'minimal' | 'custom'
  variant: 'light' | 'dark'
  primary_color?: string
  custom_css?: string
}

export interface MCFOptions {
  version: string
  settings: MCFSettings
  fields: MCFFieldConfig
  privacy_texts: MCFPrivacyTexts
  styling: MCFStyling
}

export interface ThemePreset {
  label: string
  colors: Record<string, string>
}

export interface User {
  id: number
  name: string
  email: string
}

export interface Notice {
  type: 'success' | 'error' | 'warning' | 'info'
  message: string
}

declare global {
  interface Window {
    mcfAdmin: {
      apiUrl: string
      nonce: string
      pluginUrl: string
      previewCSS: {
        base: string
        themes: {
          default: string
          modern: string
          minimal: string
          custom?: string
        }
      }
      privacyPage: {
        exists: boolean
        id: number | false
        url: string
        pageUrl: string | false
      }
    }
  }
}
