import { Panel, PanelBody, Notice } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import CSSEditor from './CSSEditor'

interface CustomCSSPanelProps {
  customCSS: string
  onCustomCSSChange: (css: string) => void
}

export default function CustomCSSPanel({ customCSS, onCustomCSSChange }: CustomCSSPanelProps) {
  return (
    <Panel>
      <PanelBody title={__('Custom CSS', 'mcf')} initialOpen={true}>
        <Notice status="info" isDismissible={false}>
          {__('Add custom CSS to style your contact form. Use autocomplete for MCF variables (--mcf-*) and classes (.mcf-*). Note: Use !important to override inline styles.', 'mcf')}
        </Notice>

        <div className="mcf-css-editor-wrapper">
          <CSSEditor value={customCSS} onChange={onCustomCSSChange} height="450px" />
        </div>

        <p className="description" style={{ marginTop: '8px' }}>
          {__('Target classes: .mcf-form, .mcf-field, .mcf-input, .mcf-submit-button', 'mcf')}
        </p>
      </PanelBody>
    </Panel>
  )
}
