import { useState, useEffect, useRef } from '@wordpress/element'
import { Modal, TextControl, Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'

interface EditModalProps {
  fieldId: string
  label: string
  placeholder: string
  onSave: (label: string, placeholder: string) => void
  onClose: () => void
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
 * Modal component for editing field labels and placeholders
 * Features: Auto-focus, keyboard navigation (Enter/Escape), ARIA labels
 */
export default function EditModal({ fieldId, label, placeholder, onSave, onClose }: EditModalProps) {
  const [editLabel, setEditLabel] = useState(label)
  const [editPlaceholder, setEditPlaceholder] = useState(placeholder)
  const labelInputRef = useRef<HTMLInputElement>(null)

  const defaultLabel = getDefaultLabel(fieldId)

  // A11y: Focus first input on mount
  useEffect(() => {
    if (labelInputRef.current) {
      labelInputRef.current.focus()
    }
  }, [])

  // A11y: Handle keyboard shortcuts
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      // Enter to save (if not in textarea)
      if (e.key === 'Enter' && !e.shiftKey && document.activeElement?.tagName !== 'TEXTAREA') {
        e.preventDefault()
        handleSave()
      }
      // Escape to close
      if (e.key === 'Escape') {
        e.preventDefault()
        onClose()
      }
    }

    window.addEventListener('keydown', handleKeyDown)
    return () => window.removeEventListener('keydown', handleKeyDown)
  }, [editLabel, editPlaceholder])

  const handleSave = () => {
    onSave(editLabel, editPlaceholder)
    onClose()
  }

  return (
    <Modal
      title={__('Edit Field', 'mcf')}
      onRequestClose={onClose}
      className="mcf-edit-modal"
      aria-labelledby="mcf-edit-modal-title"
    >
      <div className="mcf-modal-body">
        <TextControl
          label={__('Label', 'mcf')}
          value={editLabel}
          onChange={setEditLabel}
          placeholder={defaultLabel}
          ref={labelInputRef}
          aria-describedby="mcf-label-help"
          help={__('Leave empty to use the default translated label.', 'mcf')}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
        <TextControl
          label={__('Placeholder', 'mcf')}
          value={editPlaceholder}
          onChange={setEditPlaceholder}
          placeholder={editLabel || defaultLabel}
          aria-describedby="mcf-placeholder-help"
          help={__('Optional placeholder text for the input field.', 'mcf')}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      </div>
      <div className="mcf-modal-footer">
        <Button
          variant="secondary"
          onClick={onClose}
          aria-label={__('Cancel editing', 'mcf')}
        >
          {__('Cancel', 'mcf')}
        </Button>
        <Button
          variant="primary"
          onClick={handleSave}
          aria-label={__('Save changes', 'mcf')}
        >
          {__('Apply', 'mcf')}
        </Button>
      </div>
    </Modal>
  )
}
