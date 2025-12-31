import { useState, useEffect } from '@wordpress/element'
import { Modal, ToggleControl, Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import type { MCFFieldConfig } from '../types'
import FieldRow from './FieldRow'
import { NameModeToggle, ContactModeToggle, SubmitAlignmentToggle, PrivacyModeToggle } from './FieldToggleButtons'

interface FieldSettingsModalProps {
  fields: MCFFieldConfig
  onClose: () => void
  onFieldsChange: (fields: MCFFieldConfig) => void
}

/**
 * Advanced field configuration modal
 * Features: Keyboard navigation (Escape), ARIA labels, focus management
 */
export default function FieldSettingsModal({ fields, onClose, onFieldsChange }: FieldSettingsModalProps) {
  const [localFields, setLocalFields] = useState<MCFFieldConfig>(fields)

  // Update local state when props change
  useEffect(() => {
    setLocalFields(fields)
  }, [fields])

  // A11y: Handle keyboard shortcuts
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      // Escape to close
      if (e.key === 'Escape') {
        e.preventDefault()
        onClose()
      }
    }

    window.addEventListener('keydown', handleKeyDown)
    return () => window.removeEventListener('keydown', handleKeyDown)
  }, [onClose])

  const { field_groups, labels } = localFields

  // Helper to update a specific field group
  const updateGroup = (group: keyof typeof field_groups, updates: Partial<(typeof field_groups)[typeof group]>) => {
    setLocalFields({
      ...localFields,
      field_groups: {
        ...field_groups,
        [group]: {
          ...field_groups[group],
          ...updates
        }
      }
    })
  }

  // Helper to get label
  const getLabel = (fieldId: string) => {
    return labels[fieldId] || fieldId.replace(/-/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
  }

  const handleSave = () => {
    onFieldsChange(localFields)
    onClose()
  }

  return (
    <Modal
      title={__('Field Settings', 'mcf')}
      onRequestClose={onClose}
      className="mcf-field-settings-modal"
      aria-labelledby="mcf-field-settings-title"
    >
      <div className="mcf-modal-body">
        {/* Hide Labels Toggle */}
        <div className="mcf-hide-labels-section">
          <ToggleControl
            label={__('Hide Labels Visually', 'mcf')}
            checked={localFields.hide_labels ?? false}
            onChange={(val) => setLocalFields({ ...localFields, hide_labels: val })}
            help={__('Labels will be hidden visually but remain accessible to screen readers for better accessibility.', 'mcf')}
            aria-describedby="mcf-hide-labels-help"
            __nextHasNoMarginBottom
          />
        </div>

        {/* Company Field */}
        <FieldRow
          label={getLabel('company')}
          enabled={field_groups.company.enabled}
          onToggle={(val) => updateGroup('company', { enabled: val })}
        />

        {/* Name Fields - Dynamic based on mode */}
        {field_groups.name.mode === 'single' ? (
          <FieldRow
            label={getLabel('name')}
            enabled={field_groups.name.enabled}
            onToggle={(val) => updateGroup('name', { enabled: val })}
          >
            <NameModeToggle mode={field_groups.name.mode} onChange={(mode) => updateGroup('name', { mode })} />
          </FieldRow>
        ) : (
          <div className="mcf-field-row-grid">
            <FieldRow label={getLabel('first-name')} />
            <FieldRow
              label={getLabel('last-name')}
              enabled={field_groups.name.enabled}
              onToggle={(val) => updateGroup('name', { enabled: val })}
            >
              <NameModeToggle mode={field_groups.name.mode} onChange={(mode) => updateGroup('name', { mode })} />
            </FieldRow>
          </div>
        )}

        {/* Contact Fields - Dynamic based on mode */}
        {field_groups.contact.mode === 'email' ? (
          <FieldRow label={getLabel('email')} isStatic>
            <ContactModeToggle mode={field_groups.contact.mode} onChange={(mode) => updateGroup('contact', { mode })} />
          </FieldRow>
        ) : (
          <div className="mcf-field-row-grid">
            <FieldRow label={getLabel('email')} />
            <FieldRow label={getLabel('phone')} isStatic>
              <ContactModeToggle mode={field_groups.contact.mode} onChange={(mode) => updateGroup('contact', { mode })} />
            </FieldRow>
          </div>
        )}

        {/* Subject Field */}
        <FieldRow
          label={getLabel('subject')}
          enabled={field_groups.subject.enabled}
          onToggle={(val) => updateGroup('subject', { enabled: val })}
        />

        {/* Message Field (always enabled) */}
        <FieldRow label={getLabel('message')} isStatic />

        {/* Privacy/GDPR Field */}
        <FieldRow label={__('Privacy', 'mcf')} isStatic>
          <PrivacyModeToggle mode={field_groups.gdpr.mode} onChange={(mode) => updateGroup('gdpr', { mode })} />
        </FieldRow>

        {/* Submit Alignment */}
        <FieldRow label={getLabel('submit')} isStatic>
          <SubmitAlignmentToggle alignment={field_groups.submit.alignment} onChange={(alignment) => updateGroup('submit', { alignment })} />
        </FieldRow>
      </div>

      <div className="mcf-modal-footer">
        <Button
          variant="secondary"
          onClick={onClose}
          aria-label={__('Cancel field settings', 'mcf')}
        >
          {__('Cancel', 'mcf')}
        </Button>
        <Button
          variant="primary"
          onClick={handleSave}
          aria-label={__('Save field settings', 'mcf')}
        >
          {__('Apply', 'mcf')}
        </Button>
      </div>
    </Modal>
  )
}
