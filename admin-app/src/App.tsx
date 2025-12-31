import { useState, useEffect, lazy, Suspense } from '@wordpress/element'
import apiFetch from '@wordpress/api-fetch'
import { __ } from '@wordpress/i18n'
import { Button, Spinner, Notice } from '@wordpress/components'

import EmailPanel from './components/EmailPanel'
import ThemePanel from './components/ThemePanel'
import FormPreview from './components/FormPreview'
import PrivacyPanel from './components/PrivacyPanel'

// Lazy load CustomCSSPanel to reduce initial bundle size (~160KB saved)
const CustomCSSPanel = lazy(() => import('./components/CustomCSSPanel'))

import type { MCFOptions, Notice as NoticeType } from './types'

export default function App() {
  const [settings, setSettings] = useState<MCFOptions | null>(null)
  const [loading, setLoading] = useState<boolean>(true)
  const [saving, setSaving] = useState<boolean>(false)
  const [notice, setNotice] = useState<NoticeType | null>(null)

  // Load settings on mount
  useEffect(() => {
    apiFetch<MCFOptions>({ path: '/mcf/v1/settings' })
      .then((data) => {
        setSettings(data)
        setLoading(false)
      })
      .catch((error: Error) => {
        console.error('Failed to load settings:', error)
        setNotice({
          type: 'error',
          message: __('Failed to load settings. Please refresh the page.', 'mcf')
        })
        setLoading(false)
      })
  }, [])

  // Save settings
  const handleSave = () => {
    setSaving(true)
    setNotice(null)

    apiFetch({
      path: '/mcf/v1/settings',
      method: 'POST',
      data: settings
    })
      .then(() => {
        setSaving(false)
        setNotice({
          type: 'success',
          message: __('Settings saved successfully!', 'mcf')
        })
        // Auto-hide success message after 3 seconds
        setTimeout(() => setNotice(null), 3000)
      })
      .catch((error: Error) => {
        setSaving(false)
        setNotice({
          type: 'error',
          message: error.message || __('Failed to save settings. Please try again.', 'mcf')
        })
      })
  }

  // Reset database (DEV ONLY)
  const handleResetDatabase = () => {
    if (!confirm(__('⚠️ WARNING: This will delete ALL settings and reset to v1.0.0 defaults!\n\nAre you sure?', 'mcf'))) {
      return
    }

    setLoading(true)
    setNotice(null)

    apiFetch({
      path: '/mcf/v1/reset-database',
      method: 'POST'
    })
      .then(() => {
        setNotice({
          type: 'success',
          message: __('Database reset successful! Reloading...', 'mcf')
        })
        // Reload page after 1 second
        setTimeout(() => window.location.reload(), 1000)
      })
      .catch((error: Error) => {
        setLoading(false)
        setNotice({
          type: 'error',
          message: error.message || __('Reset failed!', 'mcf')
        })
      })
  }

  // Update settings helper
  const updateSettings = <K extends keyof MCFOptions>(category: K, key: keyof MCFOptions[K], value: MCFOptions[K][keyof MCFOptions[K]]) => {
    if (!settings) return

    setSettings({
      ...settings,
      [category]: {
        ...(settings[category] as object),
        [key]: value
      }
    })
  }

  if (loading) {
    return (
      <div className="mcf-admin-loading">
        <Spinner />
        <p>{__('Loading settings...', 'mcf')}</p>
      </div>
    )
  }

  if (!settings) {
    return (
      <div className="mcf-admin-error">
        <Notice status="error" isDismissible={false}>
          {__('Failed to load settings. Please refresh the page.', 'mcf')}
        </Notice>
      </div>
    )
  }

  return (
    <div className="mcf-admin-app">
      {notice && (
        <Notice status={notice.type} isDismissible={true} onDismiss={() => setNotice(null)}>
          {notice.message}
        </Notice>
      )}

      <div className="mcf-admin-layout">
        <div className="mcf-admin-sidebar">
          <EmailPanel settings={settings.settings} onChange={(key, value) => updateSettings('settings', key, value)} />

          <ThemePanel styling={settings.styling} onStylingChange={(key, value) => updateSettings('styling', key, value)} />

          <PrivacyPanel texts={settings.privacy_texts} antispamEnabled={settings.settings.antispam_enabled ?? true} onTextsChange={(key, value) => updateSettings('privacy_texts', key, value)} onAntispamChange={(value) => updateSettings('settings', 'antispam_enabled', value)} />
        </div>

        <div className="mcf-admin-main">
          <FormPreview
            fields={settings.fields}
            customCSS={settings.styling?.custom_css || ''}
            theme={settings.styling?.theme_preset || 'default'}
            variant={settings.styling?.variant || 'light'}
            primaryColor={settings.styling?.primary_color}
            privacyTexts={settings.privacy_texts || { optin_text: '', inform_text: '' }}
            onFieldsChange={(fields) => {
              // Create a deep copy to ensure React detects the change
              const newSettings = {
                ...settings,
                fields: {
                  ...fields,
                  field_groups: {
                    company: { ...fields.field_groups.company },
                    name: { ...fields.field_groups.name },
                    contact: { ...fields.field_groups.contact },
                    subject: { ...fields.field_groups.subject },
                    message: { ...fields.field_groups.message },
                    gdpr: { ...fields.field_groups.gdpr },
                    submit: { ...fields.field_groups.submit }
                  },
                  labels: { ...fields.labels },
                  placeholders: { ...fields.placeholders }
                }
              }

              setSettings(newSettings)
            }}
          />

          {/* Show CustomCSSPanel when theme is "custom" - lazy loaded */}
          {settings.styling?.theme_preset === 'custom' && (
            <Suspense
              fallback={
                <div style={{ padding: '1rem', textAlign: 'center' }}>
                  <Spinner />
                  <p style={{ marginTop: '0.5rem', color: '#646970', fontSize: '0.875rem' }}>{__('Loading CSS Editor...', 'mcf')}</p>
                </div>
              }
            >
              <CustomCSSPanel customCSS={settings.styling?.custom_css || ''} onCustomCSSChange={(css) => updateSettings('styling', 'custom_css', css)} />
            </Suspense>
          )}
        </div>
      </div>

      <div className="mcf-admin-footer">
        <Button variant="primary" onClick={handleSave} isBusy={saving} disabled={saving}>
          {saving ? __('Saving...', 'mcf') : __('Save Settings', 'mcf')}
        </Button>
        <Button variant="secondary" isDestructive onClick={handleResetDatabase} disabled={loading || saving} style={{ marginLeft: '10px' }}>
          {__('Reset Database (DEV)', 'mcf')}
        </Button>
      </div>
    </div>
  )
}
