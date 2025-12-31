import { useState } from '@wordpress/element'
import { Panel, PanelBody, Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import { Settings } from 'lucide-react'
import type { MCFFieldConfig, MCFPrivacyTexts } from '../types'
import { useCSSManagement } from '../hooks/useCSSManagement'
import EditModal from './EditModal'
import FieldSettingsModal from './FieldSettingsModal'
import NoticePreview from './NoticePreview'
import FormFieldRenderer from './FormFieldRenderer'

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

interface FormPreviewProps {
  fields: MCFFieldConfig
  customCSS: string
  theme: string
  variant: string
  primaryColor?: string
  privacyTexts: MCFPrivacyTexts
  onFieldsChange: (fields: MCFFieldConfig) => void
}

/**
 * Main form preview component (refactored from 605 lines to ~100 lines)
 * Features: ARIA role, descriptive labels, modular architecture
 */
export default function FormPreview({
  fields,
  customCSS,
  theme,
  variant,
  primaryColor,
  privacyTexts,
  onFieldsChange
}: FormPreviewProps) {
  const [editingField, setEditingField] = useState<string | null>(null)
  const [isFieldSettingsOpen, setIsFieldSettingsOpen] = useState(false)
  const [noticeType, setNoticeType] = useState<'success' | 'error' | 'warning'>('success')

  // Use custom hook for CSS management
  useCSSManagement({ theme, customCSS, primaryColor })

  // Loading state
  if (!fields?.field_groups) {
    return (
      <Panel>
        <PanelBody title={__('Form Fields', 'mcf')} initialOpen={true}>
          <p>{__('Loading fields...', 'mcf')}</p>
        </PanelBody>
      </Panel>
    )
  }

  const { field_groups, labels, placeholders } = fields

  // Helper to get submit button label
  const getLabel = (fieldId: string) => {
    const defaults: Record<string, string> = {
      submit: __('Submit', 'mcf')
    }
    return labels[fieldId] || defaults[fieldId] || fieldId
  }

  return (
    <>
      <Panel>
        <PanelBody title={__('Form Preview', 'mcf')} initialOpen={true}>
          <div className="mcf-preview-header">
            <p className="description">
              {__('Click the edit icon on any field to customize its label and placeholder.', 'mcf')}
            </p>
            <Button
              variant="secondary"
              onClick={() => setIsFieldSettingsOpen(true)}
              className="mcf-edit-fields-btn"
              aria-label={__('Open field settings', 'mcf')}
            >
              {__('Edit Fields', 'mcf')}
              <Settings size={16} />
            </Button>
          </div>

          <div className="mcf-form-preview">
            {/* Form with theme variant data attribute */}
            <div
              id="mcf-preview"
              className="mcf-form mcf-contact-form"
              data-theme-variant={variant}
              role="form"
              aria-label={__('Contact form preview', 'mcf')}
            >
              {/* Notice Preview with Controls */}
              <NoticePreview
                noticeType={noticeType}
                onNoticeTypeChange={setNoticeType}
              />

              {/* Form Fields */}
              <div className="mcf-grid">
                <FormFieldRenderer
                  fields={fields}
                  privacyTexts={privacyTexts}
                  onEditField={setEditingField}
                />
              </div>

              {/* Submit button */}
              <div className={`mcf-field mcf-field-submit mcf-submit-${field_groups.submit.alignment}`}>
                <button
                  type="button"
                  className="mcf-submit-button"
                  disabled
                  aria-label={__('Submit button (disabled in preview)', 'mcf')}
                >
                  {getLabel('submit')}
                </button>
              </div>
            </div>
          </div>
        </PanelBody>
      </Panel>

      {/* Edit Modal */}
      {editingField && (
        <EditModal
          fieldId={editingField}
          label={labels[editingField] || ''}
          placeholder={placeholders[editingField] || ''}
          onSave={(label, placeholder) => {
            // Only save if different from default (don't save defaults to DB)
            const defaultLabel = getDefaultLabel(editingField)
            const newLabels = { ...labels }
            const newPlaceholders = { ...placeholders }

            // If label is empty or equals default, remove it from DB (use default)
            if (!label || label === defaultLabel) {
              delete newLabels[editingField]
            } else {
              newLabels[editingField] = label
            }

            // If placeholder is empty, remove it from DB
            if (!placeholder) {
              delete newPlaceholders[editingField]
            } else {
              newPlaceholders[editingField] = placeholder
            }

            onFieldsChange({
              ...fields,
              labels: newLabels,
              placeholders: newPlaceholders
            })
          }}
          onClose={() => setEditingField(null)}
        />
      )}

      {/* Field Settings Modal */}
      {isFieldSettingsOpen && (
        <FieldSettingsModal
          fields={fields}
          onClose={() => setIsFieldSettingsOpen(false)}
          onFieldsChange={onFieldsChange}
        />
      )}
    </>
  )
}
