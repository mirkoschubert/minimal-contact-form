import { Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import { Eye, EyeOff } from 'lucide-react'

interface FieldRowProps {
  label: string
  enabled?: boolean
  onToggle?: (enabled: boolean) => void
  children?: React.ReactNode
  isStatic?: boolean
}

/**
 * Reusable field row component for field settings modal
 * Features: ARIA role group, aria-label with context, aria-pressed state
 */
export default function FieldRow({ label, enabled, onToggle, children, isStatic }: FieldRowProps) {
  const showToggleButton = !isStatic && onToggle
  const toggleLabel = enabled
    ? __('Hide field', 'mcf')
    : __('Show field', 'mcf')

  return (
    <div className="mcf-field-row">
      <div className="mcf-field-row-content">
        <div className="mcf-field-row-header">
          <span className="mcf-field-row-label">{label}</span>
          {(children || onToggle || isStatic) && (
            <div
              className="mcf-field-row-controls"
              role="group"
              aria-label={`${label} ${__('controls', 'mcf')}`}
            >
              {children}
              {showToggleButton && (
                <Button
                  icon={enabled ? <Eye size={18} /> : <EyeOff size={18} />}
                  iconSize={18}
                  label={toggleLabel}
                  aria-label={`${toggleLabel} ${label}`}
                  aria-pressed={enabled}
                  onClick={() => onToggle(!enabled)}
                  size="small"
                />
              )}
              {isStatic && (
                <Button
                  icon={<Eye size={18} />}
                  iconSize={18}
                  label={__('Always enabled', 'mcf')}
                  aria-label={`${label} ${__('is always enabled', 'mcf')}`}
                  size="small"
                  disabled
                />
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
