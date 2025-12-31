import { useMemo } from '@wordpress/element'
import { Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import { Pencil } from 'lucide-react'
import type { MCFFieldConfig, MCFPrivacyTexts } from '../types'

interface FormFieldRendererProps {
  fields: MCFFieldConfig
  privacyTexts: MCFPrivacyTexts
  onEditField: (fieldId: string) => void
}

/**
 * Helper to get default translated label
 */
const getDefaultLabel = (fieldId: string): string => {
  const defaults: Record<string, string> = {
    company: __('Company', 'mcf'),
    'first-name': __('First Name', 'mcf'),
    'last-name': __('Last Name', 'mcf'),
    name: __('Name', 'mcf'),
    phone: __('Phone', 'mcf'),
    email: __('Email', 'mcf'),
    subject: __('Subject', 'mcf'),
    message: __('Message', 'mcf'),
    submit: __('Submit', 'mcf')
  }
  return defaults[fieldId] || fieldId.replace(/-/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
}

/**
 * Helper to build privacy text with link
 */
const buildPrivacyTextWithLink = (baseText: string): string => {
  const privacyUrl = (window as any).mcfAdmin?.privacyPage?.pageUrl

  if (!privacyUrl) {
    return baseText
  }

  return `${baseText} ${__('For further information please visit our', 'mcf')} <a href="${privacyUrl}" target="_blank" rel="noopener noreferrer">${__('Privacy Policy', 'mcf')}</a>.`
}

/**
 * Form field renderer component
 * Handles field rendering logic, privacy text generation, and accessibility
 * Features: ARIA labels, aria-required, proper label/input associations
 */
export default function FormFieldRenderer({ fields, privacyTexts, onEditField }: FormFieldRendererProps) {
  const { field_groups, labels, placeholders } = fields

  // Get label for a field
  const getLabel = (fieldId: string) => {
    return labels[fieldId] || getDefaultLabel(fieldId)
  }

  // Get placeholder for a field
  const getPlaceholder = (fieldId: string) => {
    return placeholders[fieldId] || ''
  }

  // Render a preview field
  const renderField = (fieldId: string, type: string = 'text', required: boolean = false, fullWidth: boolean = false) => {
    const label = getLabel(fieldId)
    const explicitPlaceholder = getPlaceholder(fieldId)
    const isTextarea = fieldId === 'message'
    const inputId = `mcf-${fieldId}`
    const hideLabels = fields.hide_labels ?? false

    // Compute placeholder based on hide_labels setting
    const computePlaceholder = () => {
      if (hideLabels) {
        // Labels are hidden → Placeholder = Label + asterisk (if required)
        return required ? `${label} *` : label
      } else {
        // Labels are visible → Only use explicitly entered placeholder
        return explicitPlaceholder || ''
      }
    }

    const placeholder = computePlaceholder()

    return (
      <div className={`mcf-field mcf-field-${fieldId}`} key={fieldId} data-full-width={fullWidth}>
        <div className="mcf-preview-field-controls">
          <Button
            icon={<Pencil size={18} />}
            iconSize={18}
            label={__('Edit label & placeholder', 'mcf')}
            aria-label={`${__('Edit', 'mcf')} ${label}`}
            onClick={() => onEditField(fieldId)}
            className="mcf-field-control mcf-edit"
            size="small"
          />
        </div>
        <label className={`mcf-label ${hideLabels ? 'mcf-sr-only' : ''}`} htmlFor={inputId}>
          {label}
          {required && <span className="required" aria-label={__('required', 'mcf')}>*</span>}
        </label>
        {isTextarea ? (
          <textarea
            id={inputId}
            className="mcf-textarea"
            placeholder={placeholder}
            rows={4}
            readOnly
            aria-label={hideLabels ? label : undefined}
            aria-required={required}
          />
        ) : (
          <input
            id={inputId}
            type={type}
            className="mcf-input"
            placeholder={placeholder}
            readOnly
            aria-label={hideLabels ? label : undefined}
            aria-required={required}
          />
        )}
      </div>
    )
  }

  // Build the active fields array based on field_groups configuration
  const activeFields = useMemo(() => {
    const fieldsArray: JSX.Element[] = []

    // Company (full width)
    if (field_groups.company.enabled) {
      fieldsArray.push(renderField('company', 'text', false, true))
    }

    // Name (single or split)
    if (field_groups.name.enabled) {
      if (field_groups.name.mode === 'single') {
        fieldsArray.push(renderField('name', 'text', true, true))
      } else {
        fieldsArray.push(renderField('first-name', 'text', true, false))
        fieldsArray.push(renderField('last-name', 'text', true, false))
      }
    }

    // Contact (email or email-phone)
    if (field_groups.contact.enabled) {
      if (field_groups.contact.mode === 'email-phone') {
        fieldsArray.push(renderField('email', 'email', true, false))
        fieldsArray.push(renderField('phone', 'tel', false, false))
      } else {
        fieldsArray.push(renderField('email', 'email', true, true))
      }
    }

    // Subject (full width)
    if (field_groups.subject.enabled) {
      fieldsArray.push(renderField('subject', 'text', true, true))
    }

    // Message (always enabled, full width)
    fieldsArray.push(renderField('message', 'text', true, true))

    // GDPR/Privacy field
    if (field_groups.gdpr.enabled) {
      const gdprMode = field_groups.gdpr.mode

      // Get base text from privacy_texts
      let baseText = gdprMode === 'optin' ? privacyTexts.optin_text : privacyTexts.inform_text

      // Fallback to default if empty
      if (!baseText) {
        baseText = gdprMode === 'optin'
          ? __('I consent to having you process my submitted information so you can respond to my inquiry.', 'mcf')
          : __('Your submitted information will only be processed to respond to your inquiry.', 'mcf')
      }

      // Build complete text with privacy policy link if available
      const completeText = buildPrivacyTextWithLink(baseText)

      fieldsArray.push(
        <div className="mcf-field mcf-field-privacy" key="gdpr">
          {gdprMode === 'optin' ? (
            <label className="mcf-checkbox-label" htmlFor="mcf-privacy">
              <input
                type="checkbox"
                id="mcf-privacy"
                className="mcf-checkbox"
                readOnly
                aria-required="true"
              />
              <span className="mcf-checkbox-text" dangerouslySetInnerHTML={{ __html: completeText }} />
              <span className="required" aria-label={__('required', 'mcf')}>*</span>
            </label>
          ) : (
            <p
              className="mcf-privacy-text"
              dangerouslySetInnerHTML={{ __html: completeText }}
              role="note"
            />
          )}
        </div>
      )
    }

    return fieldsArray
  }, [fields, privacyTexts])

  return <>{activeFields}</>
}
