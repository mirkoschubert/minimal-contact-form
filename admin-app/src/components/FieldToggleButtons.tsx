import { Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import { RotateCw, AlignLeft, AlignRight, Info, CheckCircle2 } from 'lucide-react'

/**
 * Toggle button for name field mode (single/split)
 * Features: ARIA labels, aria-pressed state
 */
interface NameModeToggleProps {
  mode: 'single' | 'split'
  onChange: (mode: 'single' | 'split') => void
}

export function NameModeToggle({ mode, onChange }: NameModeToggleProps) {
  const toggleMode = () => {
    onChange(mode === 'single' ? 'split' : 'single')
  }

  const label = mode === 'single'
    ? __('Switch to split name fields', 'mcf')
    : __('Switch to single name field', 'mcf')

  return (
    <Button
      icon={<RotateCw size={18} />}
      iconSize={18}
      label={label}
      aria-label={label}
      aria-pressed={mode === 'split'}
      onClick={toggleMode}
      size="small"
    />
  )
}

/**
 * Toggle button for contact field mode (email/email-phone)
 * Features: ARIA labels, aria-pressed state
 */
interface ContactModeToggleProps {
  mode: 'email' | 'email-phone'
  onChange: (mode: 'email' | 'email-phone') => void
}

export function ContactModeToggle({ mode, onChange }: ContactModeToggleProps) {
  const toggleMode = () => {
    onChange(mode === 'email' ? 'email-phone' : 'email')
  }

  const label = mode === 'email'
    ? __('Add phone field', 'mcf')
    : __('Remove phone field', 'mcf')

  return (
    <Button
      icon={<RotateCw size={18} />}
      iconSize={18}
      label={label}
      aria-label={label}
      aria-pressed={mode === 'email-phone'}
      onClick={toggleMode}
      size="small"
    />
  )
}

/**
 * Toggle button for submit button alignment (left/right)
 * Features: ARIA labels, aria-pressed state, dynamic icon
 */
interface SubmitAlignmentToggleProps {
  alignment: 'left' | 'right'
  onChange: (alignment: 'left' | 'right') => void
}

export function SubmitAlignmentToggle({ alignment, onChange }: SubmitAlignmentToggleProps) {
  const toggleAlignment = () => {
    onChange(alignment === 'left' ? 'right' : 'left')
  }

  const label = alignment === 'left'
    ? __('Align right', 'mcf')
    : __('Align left', 'mcf')

  return (
    <Button
      icon={alignment === 'left' ? <AlignLeft size={18} /> : <AlignRight size={18} />}
      iconSize={18}
      label={label}
      aria-label={label}
      aria-pressed={alignment === 'right'}
      onClick={toggleAlignment}
      size="small"
    />
  )
}

/**
 * Toggle button for privacy field mode (inform/optin)
 * Features: ARIA labels, aria-pressed state, dynamic icon
 */
interface PrivacyModeToggleProps {
  mode: 'inform' | 'optin'
  onChange: (mode: 'inform' | 'optin') => void
}

export function PrivacyModeToggle({ mode, onChange }: PrivacyModeToggleProps) {
  const toggleMode = () => {
    onChange(mode === 'inform' ? 'optin' : 'inform')
  }

  const label = mode === 'inform'
    ? __('Switch to Opt-in', 'mcf')
    : __('Switch to Inform', 'mcf')

  return (
    <Button
      icon={mode === 'inform' ? <Info size={18} /> : <CheckCircle2 size={18} />}
      iconSize={18}
      label={label}
      aria-label={label}
      aria-pressed={mode === 'optin'}
      onClick={toggleMode}
      size="small"
    />
  )
}
