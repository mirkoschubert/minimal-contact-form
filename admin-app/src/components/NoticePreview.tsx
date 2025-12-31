import { Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import { CheckCircle2, AlertTriangle, Info } from 'lucide-react'

interface NoticePreviewProps {
  noticeType: 'success' | 'error' | 'warning'
  onNoticeTypeChange: (type: 'success' | 'error' | 'warning') => void
}

/**
 * Get notice message based on type
 */
const getNoticeMessage = (type: 'success' | 'error' | 'warning'): string => {
  switch (type) {
    case 'success':
      return __('Thank you for your message! We will get back to you soon.', 'mcf')
    case 'error':
      return __('There was an error submitting your form. Please check your entries and try again.', 'mcf')
    case 'warning':
      return __('Please note: This is a warning message example.', 'mcf')
  }
}

/**
 * Notice type selector with toggle buttons
 * Features: ARIA role, aria-pressed, descriptive labels
 */
function NoticeTypeSelector({ noticeType, onChange }: { noticeType: 'success' | 'error' | 'warning'; onChange: (type: 'success' | 'error' | 'warning') => void }) {
  return (
    <div
      className="mcf-notice-type-controls"
      role="group"
      aria-label={__('Notice type selector', 'mcf')}
    >
      <Button
        icon={<CheckCircle2 size={18} />}
        iconSize={18}
        label={__('Show success notice', 'mcf')}
        onClick={() => onChange('success')}
        className={`mcf-field-control mcf-notice-control ${noticeType === 'success' ? 'active' : ''}`}
        size="small"
        aria-pressed={noticeType === 'success'}
        style={{
          backgroundColor: noticeType === 'success' ? '#d1e7dd' : undefined
        }}
      />
      <Button
        icon={<AlertTriangle size={18} />}
        iconSize={18}
        label={__('Show error notice', 'mcf')}
        onClick={() => onChange('error')}
        className={`mcf-field-control mcf-notice-control ${noticeType === 'error' ? 'active' : ''}`}
        size="small"
        aria-pressed={noticeType === 'error'}
        style={{
          backgroundColor: noticeType === 'error' ? '#f8d7da' : undefined
        }}
      />
      <Button
        icon={<Info size={18} />}
        iconSize={18}
        label={__('Show warning notice', 'mcf')}
        onClick={() => onChange('warning')}
        className={`mcf-field-control mcf-notice-control ${noticeType === 'warning' ? 'active' : ''}`}
        size="small"
        aria-pressed={noticeType === 'warning'}
        style={{
          backgroundColor: noticeType === 'warning' ? '#fff3cd' : undefined
        }}
      />
    </div>
  )
}

/**
 * Notice preview component with type selector
 * Features: ARIA live region, status role, polite announcements
 */
export default function NoticePreview({ noticeType, onNoticeTypeChange }: NoticePreviewProps) {
  return (
    <div className="mcf-notice-wrapper" style={{ position: 'relative' }}>
      <NoticeTypeSelector noticeType={noticeType} onChange={onNoticeTypeChange} />
      <div
        className={`mcf-notice show ${noticeType}`}
        role="status"
        aria-live="polite"
      >
        {getNoticeMessage(noticeType)}
      </div>
    </div>
  )
}
