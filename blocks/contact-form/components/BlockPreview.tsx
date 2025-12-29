import { useMemo, useEffect } from '@wordpress/element';
import type { MCFOptions } from '../types';

interface BlockPreviewProps {
  settings: MCFOptions;
  clientId: string;
}

// Declare global mcfBlockEditor
declare global {
  interface Window {
    mcfBlockEditor?: {
      previewCSS: {
        themes: {
          default: string;
          modern: string;
          minimal: string;
        };
        custom: string;
      };
      globalTheme: string;
    };
  }
}

// No need for local getDefaultLabel - labels come from global settings via REST API

/**
 * Helper functions for color calculations
 */
const adjustBrightness = (hex: string, steps: number): string => {
  const cleanHex = hex.replace('#', '');
  let r = parseInt(cleanHex.substring(0, 2), 16);
  let g = parseInt(cleanHex.substring(2, 4), 16);
  let b = parseInt(cleanHex.substring(4, 6), 16);

  r = Math.max(0, Math.min(255, r + steps));
  g = Math.max(0, Math.min(255, g + steps));
  b = Math.max(0, Math.min(255, b + steps));

  return '#' + [r, g, b].map((x) => x.toString(16).padStart(2, '0')).join('');
};

const getContrastColor = (hex: string): string => {
  const cleanHex = hex.replace('#', '');
  let r = parseInt(cleanHex.substring(0, 2), 16) / 255;
  let g = parseInt(cleanHex.substring(2, 4), 16) / 255;
  let b = parseInt(cleanHex.substring(4, 6), 16) / 255;

  // Calculate relative luminance (WCAG 2.0)
  r = r <= 0.03928 ? r / 12.92 : Math.pow((r + 0.055) / 1.055, 2.4);
  g = g <= 0.03928 ? g / 12.92 : Math.pow((g + 0.055) / 1.055, 2.4);
  b = b <= 0.03928 ? b / 12.92 : Math.pow((b + 0.055) / 1.055, 2.4);

  const luminance = 0.2126 * r + 0.7152 * g + 0.0722 * b;

  return luminance > 0.5 ? '#000000' : '#ffffff';
};

export default function BlockPreview({ settings, clientId }: BlockPreviewProps) {
  const { field_groups, labels, placeholders, hide_labels } = settings.fields;
  const { theme_preset, variant, primary_color } = settings.styling;
  const { optin_text, inform_text } = settings.privacy_texts;

  // Generate primary color CSS
  const primaryColorCSS = primary_color
    ? (() => {
        const hoverColor = adjustBrightness(primary_color, -20);
        const textColor = getContrastColor(primary_color);
        const textHoverColor = getContrastColor(hoverColor);

        return `
.mcf-form[data-theme-variant="light"],
.mcf-form[data-theme-variant="dark"] {
  --mcf-button-background-color: ${primary_color} !important;
  --mcf-button-background-hover-color: ${hoverColor} !important;
  --mcf-button-color: ${textColor} !important;
  --mcf-button-hover-color: ${textHoverColor} !important;
  --mcf-checkbox-color: ${primary_color} !important;
}
        `;
      })()
    : '';

  // Inject theme CSS from preloaded content (base style.css is loaded via block.json)
  useEffect(() => {
    // Eindeutige IDs mit clientId (wichtig für mehrere Blocks!)
    const themeStyleId = `mcf-block-theme-${clientId}`;
    const customStyleId = `mcf-block-custom-${clientId}`;

    // Alte Style-Elemente entfernen
    document.getElementById(themeStyleId)?.remove();
    document.getElementById(customStyleId)?.remove();

    // CSS-Inhalte aus window.mcfBlockEditor holen
    const previewCSS = window.mcfBlockEditor?.previewCSS;
    if (!previewCSS) {
      console.error('Preview CSS not available in window.mcfBlockEditor');
      return;
    }

    // Theme CSS oder Custom CSS injizieren (nie beides!)
    if (theme_preset !== 'custom') {
      // Preset-Theme CSS injizieren
      const themeCSS = previewCSS.themes?.[theme_preset as 'default' | 'modern' | 'minimal'];
      if (themeCSS) {
        const themeStyleEl = document.createElement('style');
        themeStyleEl.id = themeStyleId;
        themeStyleEl.textContent = themeCSS;
        document.head.appendChild(themeStyleEl);
      }
    } else if (settings.styling.custom_css) {
      // Custom CSS injizieren
      const customStyleEl = document.createElement('style');
      customStyleEl.id = customStyleId;
      customStyleEl.textContent = settings.styling.custom_css;
      document.head.appendChild(customStyleEl);
    }

    return () => {
      // Cleanup beim Unmount
      document.getElementById(themeStyleId)?.remove();
      document.getElementById(customStyleId)?.remove();
    };
  }, [theme_preset, settings.styling.custom_css, clientId]);

  // Get label for a field (with fallback if REST API labels are empty)
  const getLabel = (fieldId: string) => {
    if (labels[fieldId]) {
      return labels[fieldId];
    }
    // Fallback: Capitalize field ID
    return fieldId.replace(/-/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
  };

  // Get placeholder for a field
  const getPlaceholder = (fieldId: string) => {
    return placeholders[fieldId] || '';
  };

  // Render a preview field
  const renderField = (
    fieldId: string,
    type: string = 'text',
    required: boolean = false,
    fullWidth: boolean = false
  ) => {
    const label = getLabel(fieldId);
    const explicitPlaceholder = getPlaceholder(fieldId);
    const isTextarea = fieldId === 'message';
    const inputId = `mcf-${fieldId}`;

    // Compute placeholder based on hide_labels setting
    const placeholder = hide_labels
      ? required
        ? `${label} *`
        : label
      : explicitPlaceholder || '';

    return (
      <div
        className={`mcf-field mcf-field-${fieldId}`}
        key={fieldId}
        data-full-width={fullWidth}>
        <label
          className={`mcf-label ${hide_labels ? 'mcf-sr-only' : ''}`}
          htmlFor={inputId}>
          {label}
          {required && <span className="required">*</span>}
        </label>
        {isTextarea ? (
          <textarea
            id={inputId}
            className="mcf-textarea"
            placeholder={placeholder}
            rows={4}
            readOnly
          />
        ) : (
          <input
            id={inputId}
            type={type}
            className="mcf-input"
            placeholder={placeholder}
            readOnly
          />
        )}
      </div>
    );
  };

  // Build the active fields array based on field_groups configuration
  const activeFields = useMemo(() => {
    const fields: JSX.Element[] = [];

    // Company (full width)
    if (field_groups.company.enabled) {
      fields.push(renderField('company', 'text', false, true));
    }

    // Name (single or split)
    if (field_groups.name.enabled) {
      if (field_groups.name.mode === 'single') {
        fields.push(renderField('name', 'text', true, true));
      } else {
        fields.push(renderField('first-name', 'text', true, false));
        fields.push(renderField('last-name', 'text', true, false));
      }
    }

    // Contact (email or email-phone)
    if (field_groups.contact.enabled) {
      if (field_groups.contact.mode === 'email-phone') {
        fields.push(renderField('email', 'email', true, false));
        fields.push(renderField('phone', 'tel', false, false));
      } else {
        fields.push(renderField('email', 'email', true, true));
      }
    }

    // Subject (full width)
    if (field_groups.subject.enabled) {
      fields.push(renderField('subject', 'text', true, true));
    }

    // Message (always enabled, full width)
    fields.push(renderField('message', 'text', true, true));

    // GDPR/Privacy field
    if (field_groups.gdpr.enabled) {
      const gdprMode = field_groups.gdpr.mode;
      const gdprText =
        gdprMode === 'optin'
          ? labels['gdpr-optin'] || optin_text
          : labels['gdpr-inform'] || inform_text;

      fields.push(
        <div className="mcf-field mcf-field-privacy" key="gdpr">
          {gdprMode === 'optin' ? (
            <label className="mcf-checkbox-label" htmlFor="mcf-privacy">
              <input
                type="checkbox"
                id="mcf-privacy"
                className="mcf-checkbox"
                readOnly
                disabled
              />
              <span className="mcf-checkbox-text">
                {gdprText}
                <span className="required">*</span>
              </span>
            </label>
          ) : (
            <p className="mcf-privacy-text">{gdprText}</p>
          )}
        </div>
      );
    }

    return fields;
  }, [settings]);

  // Build form class name
  const formClassName = `mcf-form mcf-theme-${theme_preset}`;

  return (
    <div className="mcf-block-preview">
      {/* Inject primary color CSS */}
      {primaryColorCSS && <style>{primaryColorCSS}</style>}

      {/* Form with theme and variant */}
      <div className={formClassName} data-theme-variant={variant}>
        <div className="mcf-notice" style={{ display: 'none' }}></div>

        <div className="mcf-grid">{activeFields}</div>

        {/* Submit button */}
        <div
          className={`mcf-field mcf-field-submit mcf-submit-${field_groups.submit.alignment}`}>
          <button type="button" className="mcf-submit-button" disabled>
            {getLabel('submit')}
          </button>
        </div>
      </div>
    </div>
  );
}
