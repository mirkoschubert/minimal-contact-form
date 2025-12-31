import { InspectorControls } from '@wordpress/block-editor'
import { PanelBody, SelectControl, Button, ColorPicker, ColorIndicator, Popover, __experimentalVStack as VStack } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import { useState } from '@wordpress/element'
import type { BlockAttributes } from '../types'

interface BlockInspectorControlsProps {
  attributes: BlockAttributes
  setAttributes: (attrs: Partial<BlockAttributes>) => void
}

export default function BlockInspectorControls({ attributes, setAttributes }: BlockInspectorControlsProps) {
  const [isColorPickerOpen, setIsColorPickerOpen] = useState(false)

  const hasCustomSettings = !!(attributes.theme || attributes.variant || attributes.primaryColor || attributes.privacyMode)

  return (
    <InspectorControls>
      <PanelBody title={__('Theme Settings', 'mcf')} initialOpen={true}>
        <VStack spacing={4}>
          <SelectControl
            label={__('Theme', 'mcf')}
            value={attributes.theme as '' | 'default' | 'modern' | 'minimal'}
            options={[
              { label: __('Use Global Setting', 'mcf'), value: '' },
              { label: __('Default', 'mcf'), value: 'default' },
              { label: __('Modern', 'mcf'), value: 'modern' },
              { label: __('Minimal', 'mcf'), value: 'minimal' }
            ]}
            onChange={(theme) => setAttributes({ theme: theme as string })}
            help={__('Override the global theme for this block', 'mcf')}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />

          <SelectControl
            label={__('Color Scheme', 'mcf')}
            value={attributes.variant as '' | 'light' | 'dark'}
            options={[
              { label: __('Use Global Setting', 'mcf'), value: '' },
              { label: __('Light', 'mcf'), value: 'light' },
              { label: __('Dark', 'mcf'), value: 'dark' }
            ]}
            onChange={(variant) => setAttributes({ variant: variant as string })}
            __next40pxDefaultSize
            __nextHasNoMarginBottom
          />

          <div>
            <div
              className="components-base-control__label"
              style={{
                display: 'block',
                marginBottom: '8px'
              }}
            >
              {__('Primary Color', 'mcf')}
            </div>
            <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
              <Button onClick={() => setIsColorPickerOpen(!isColorPickerOpen)}>
                <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                  <ColorIndicator colorValue={attributes.primaryColor || '#222222'} />
                  <span>{attributes.primaryColor ? __('Change Color', 'mcf') : __('Choose Color', 'mcf')}</span>
                </div>
              </Button>

              {attributes.primaryColor && (
                <Button
                  variant="secondary"
                  size="small"
                  onClick={() => {
                    setAttributes({ primaryColor: '' })
                    setIsColorPickerOpen(false)
                  }}
                >
                  {__('Reset', 'mcf')}
                </Button>
              )}
            </div>

            {isColorPickerOpen && (
              <Popover position="bottom left" onClose={() => setIsColorPickerOpen(false)}>
                <div style={{ padding: '10px' }}>
                  <ColorPicker
                    color={attributes.primaryColor || '#222222'}
                    onChangeComplete={(color) => {
                      const hexColor = typeof color === 'string' ? color : color.hex
                      setAttributes({ primaryColor: hexColor })
                    }}
                    enableAlpha={false}
                  />
                </div>
              </Popover>
            )}
          </div>
        </VStack>
      </PanelBody>

      <PanelBody title={__('Privacy Settings', 'mcf')}>
        <SelectControl
          label={__('Privacy Mode', 'mcf')}
          value={attributes.privacyMode as '' | 'inform' | 'optin'}
          options={[
            { label: __('Use Global Setting', 'mcf'), value: '' },
            { label: __('Inform Only', 'mcf'), value: 'inform' },
            { label: __('Opt-in Required', 'mcf'), value: 'optin' }
          ]}
          onChange={(value) => setAttributes({ privacyMode: value as string })}
          help={__('Choose how privacy information is displayed to users', 'mcf')}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      </PanelBody>

      {hasCustomSettings && (
        <PanelBody title={__('Reset', 'mcf')} initialOpen={false}>
          <p style={{ marginTop: 0 }}>{__('Reset all block-specific settings to use global plugin settings.', 'mcf')}</p>
          <Button
            variant="secondary"
            isDestructive
            onClick={() =>
              setAttributes({
                theme: '',
                variant: '',
                primaryColor: '',
                privacyMode: ''
              })
            }
          >
            {__('Reset to Global Settings', 'mcf')}
          </Button>
        </PanelBody>
      )}
    </InspectorControls>
  )
}
