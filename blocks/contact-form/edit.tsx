import { useBlockProps } from '@wordpress/block-editor';
import { useState, useEffect } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

import BlockInspectorControls from './components/BlockInspectorControls';
import BlockPreview from './components/BlockPreview';
import type { MCFOptions, BlockAttributes } from './types';

interface EditProps {
  attributes: BlockAttributes;
  setAttributes: (attrs: Partial<BlockAttributes>) => void;
  clientId: string;
}

/**
 * Merge block attributes with global settings
 */
function mergeSettings(global: MCFOptions, attrs: BlockAttributes): MCFOptions {
  return {
    ...global,
    styling: {
      theme_preset: (attrs.theme || global.styling.theme_preset) as 'default' | 'modern' | 'minimal' | 'custom',
      variant: (attrs.variant || global.styling.variant) as 'light' | 'dark',
      primary_color: attrs.primaryColor || global.styling.primary_color,
      custom_css: attrs.customCSS || global.styling.custom_css,
    },
    fields: {
      ...global.fields,
      field_groups: {
        ...global.fields.field_groups,
        gdpr: {
          ...global.fields.field_groups.gdpr,
          mode: (attrs.privacyMode || global.fields.field_groups.gdpr.mode) as 'inform' | 'optin',
        },
      },
    },
  };
}

export default function Edit({ attributes, setAttributes, clientId }: EditProps) {
  const [globalSettings, setGlobalSettings] = useState<MCFOptions | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const blockProps = useBlockProps({
    className: 'mcf-block-editor',
  });

  // Load global settings on mount
  useEffect(() => {
    apiFetch<MCFOptions>({ path: '/mcf/v1/settings' })
      .then(setGlobalSettings)
      .catch((err: unknown) => {
        console.error('Failed to load MCF settings:', err);
        setError(__('Failed to load settings. Please check your WordPress REST API.', 'mcf'));
      })
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div {...blockProps}>
        <div style={{ textAlign: 'center', padding: '40px' }}>
          <Spinner />
          <p>{__('Loading contact form settings...', 'mcf')}</p>
        </div>
      </div>
    );
  }

  if (error || !globalSettings) {
    return (
      <div {...blockProps}>
        <div style={{
          padding: '20px',
          background: '#f8d7da',
          border: '1px solid #f5c2c7',
          borderRadius: '4px',
          color: '#842029'
        }}>
          <p><strong>{__('Error:', 'mcf')}</strong> {error || __('Settings could not be loaded.', 'mcf')}</p>
        </div>
      </div>
    );
  }

  // Merge block attributes with global settings
  const effectiveSettings = mergeSettings(globalSettings, attributes);

  return (
    <>
      <BlockInspectorControls
        attributes={attributes}
        setAttributes={setAttributes}
      />
      <div {...blockProps}>
        <BlockPreview
          settings={effectiveSettings}
          clientId={clientId}
        />
      </div>
    </>
  );
}
