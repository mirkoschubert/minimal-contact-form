import { Panel, PanelBody, TextareaControl, ToggleControl, Notice } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import type { MCFPrivacyTexts } from '../types'

interface PrivacyPanelProps {
  texts: MCFPrivacyTexts
  antispamEnabled: boolean
  onTextsChange: (key: keyof MCFPrivacyTexts, value: string) => void
  onAntispamChange: (enabled: boolean) => void
}

export default function PrivacyPanel({ texts, antispamEnabled, onTextsChange, onAntispamChange }: PrivacyPanelProps) {
  const privacyPage = window.mcfAdmin?.privacyPage

  return (
    <Panel>
      <PanelBody title={__('Privacy & Security', 'mcf')} initialOpen={true}>
        <div className="mcf-panel-group">
          {/* Privacy Page Notice */}
          {!privacyPage?.exists && (
            <Notice status="warning" isDismissible={false}>
              <p>
                <strong>{__('No Privacy Page Set', 'mcf')}</strong>
              </p>
              <p>{__('WordPress does not have a privacy page configured. This is required for GDPR compliance.', 'mcf')}</p>
              <p>
                <a href={privacyPage?.url} target="_blank" rel="noopener noreferrer">
                  {__('Configure Privacy Page →', 'mcf')}
                </a>
              </p>
            </Notice>
          )}

          {privacyPage?.exists && (
            <p className="description" style={{ marginTop: 0 }}>
              {__('Your WordPress privacy page is set up.', 'mcf')}{' '}
              {privacyPage.pageUrl && (
                <a href={privacyPage.pageUrl} target="_blank" rel="noopener noreferrer">
                  {__('View Privacy Page →', 'mcf')}
                </a>
              )}
            </p>
          )}

          {/* Antispam Toggle (moved from SettingsPanel) */}
          <ToggleControl label={__('Enable Honeypot Antispam', 'mcf')} checked={antispamEnabled} onChange={onAntispamChange} help={__('Uses an invisible honeypot field to prevent spam submissions without CAPTCHA.', 'mcf')} __nextHasNoMarginBottom />
        </div>

        <div className="mcf-panel-group">
          {/* Privacy Texts */}
          <TextareaControl
            label={__('Opt-in Text', 'mcf')}
            value={texts?.optin_text || ''}
            onChange={(value) => onTextsChange('optin_text', value || '')}
            placeholder={__('I consent to having you process my submitted information so you can respond to my inquiry.', 'mcf')}
            help={__('Shown when GDPR mode is set to Opt-in. Leave empty to use default translatable text. A link to your Privacy Policy will be automatically appended if configured.', 'mcf')}
            rows={3}
            // @ts-ignore - autoComplete is valid but not in types
            autoComplete="off"
            __nextHasNoMarginBottom
          />

          <TextareaControl
            label={__('Inform Text', 'mcf')}
            value={texts?.inform_text || ''}
            onChange={(value) => onTextsChange('inform_text', value || '')}
            placeholder={__('Your submitted information will only be processed to respond to your inquiry.', 'mcf')}
            help={__('Shown when GDPR mode is set to Inform. Leave empty to use default translatable text. A link to your Privacy Policy will be automatically appended if configured.', 'mcf')}
            rows={3}
            // @ts-ignore - autoComplete is valid but not in types
            autoComplete="off"
            __nextHasNoMarginBottom
          />
        </div>
      </PanelBody>
    </Panel>
  )
}
