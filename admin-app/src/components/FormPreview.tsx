import { useState, useMemo, useEffect } from '@wordpress/element';
import {
  Panel,
  PanelBody,
  ToggleControl,
  Modal,
  TextControl,
  Button,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
  Eye,
  EyeOff,
  RotateCw,
  CheckCircle2,
  AlertTriangle,
  Info,
  Pencil,
  AlignLeft,
  AlignRight,
  Settings,
} from 'lucide-react';
import type { MCFFieldConfig, MCFPrivacyTexts } from '../types';

interface FormPreviewProps {
  fields: MCFFieldConfig;
  customCSS: string;
  theme: string;
  variant: string;
  primaryColor?: string;
  privacyTexts: MCFPrivacyTexts;
  onFieldsChange: (fields: MCFFieldConfig) => void;
}

interface EditModalProps {
  fieldId: string;
  label: string;
  placeholder: string;
  onSave: (label: string, placeholder: string) => void;
  onClose: () => void;
}

interface FieldSettingsModalProps {
  fields: MCFFieldConfig;
  onClose: () => void;
  onFieldsChange: (fields: MCFFieldConfig) => void;
}

interface FieldRowProps {
  label: string;
  enabled?: boolean;
  onToggle?: (enabled: boolean) => void;
  children?: React.ReactNode;
  isStatic?: boolean;
}

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
    submit: __('Submit', 'mcf'),
    'gdpr-optin': __(
      'I consent to having you process my submitted information so you can respond to my inquiry.',
      'mcf'
    ),
    'gdpr-inform': __(
      'Your submitted information will only be processed to respond to your inquiry.',
      'mcf'
    ),
  };
  return (
    defaults[fieldId] ||
    fieldId.replace(/-/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
  );
};

function EditModal({
  fieldId,
  label,
  placeholder,
  onSave,
  onClose,
}: EditModalProps) {
  const [editLabel, setEditLabel] = useState(label);
  const [editPlaceholder, setEditPlaceholder] = useState(placeholder);

  const defaultLabel = getDefaultLabel(fieldId);

  return (
    <Modal
      title={__('Edit Field', 'mcf')}
      onRequestClose={onClose}
      className="mcf-edit-modal">
      <div className="mcf-modal-body">
        <TextControl
          label={__('Label', 'mcf')}
          value={editLabel}
          onChange={setEditLabel}
          placeholder={defaultLabel}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
        <TextControl
          label={__('Placeholder', 'mcf')}
          value={editPlaceholder}
          onChange={setEditPlaceholder}
          placeholder={editLabel || defaultLabel}
          __next40pxDefaultSize
          __nextHasNoMarginBottom
        />
      </div>
      <div className="mcf-modal-footer">
        <Button variant="secondary" onClick={onClose}>
          {__('Cancel', 'mcf')}
        </Button>
        <Button
          variant="primary"
          onClick={() => {
            onSave(editLabel, editPlaceholder);
            onClose();
          }}>
          {__('Apply', 'mcf')}
        </Button>
      </div>
    </Modal>
  );
}

function FieldRow({
  label,
  enabled,
  onToggle,
  children,
  isStatic,
}: FieldRowProps) {
  return (
    <div className="mcf-field-row">
      <div className="mcf-field-row-content">
        <div className="mcf-field-row-header">
          <span className="mcf-field-row-label">{label}</span>
          {(children || onToggle || isStatic) && (
            <div className="mcf-field-row-controls">
              {children}
              {!isStatic && onToggle && (
                <Button
                  icon={enabled ? <Eye size={18} /> : <EyeOff size={18} />}
                  iconSize={18}
                  label={
                    enabled ? __('Hide field', 'mcf') : __('Show field', 'mcf')
                  }
                  onClick={() => onToggle(!enabled)}
                  size="small"
                />
              )}
              {isStatic && (
                <Button
                  icon={<Eye size={18} />}
                  iconSize={18}
                  label={__('Always enabled', 'mcf')}
                  size="small"
                  disabled
                />
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

function NameModeToggle({
  mode,
  onChange,
}: {
  mode: 'single' | 'split';
  onChange: (mode: 'single' | 'split') => void;
}) {
  const toggleMode = () => {
    onChange(mode === 'single' ? 'split' : 'single');
  };

  return (
    <Button
      icon={<RotateCw size={18} />}
      iconSize={18}
      label={
        mode === 'single'
          ? __('Switch to split name fields', 'mcf')
          : __('Switch to single name field', 'mcf')
      }
      onClick={toggleMode}
      size="small"
    />
  );
}

function ContactModeToggle({
  mode,
  onChange,
}: {
  mode: 'email' | 'email-phone';
  onChange: (mode: 'email' | 'email-phone') => void;
}) {
  const toggleMode = () => {
    onChange(mode === 'email' ? 'email-phone' : 'email');
  };

  return (
    <Button
      icon={<RotateCw size={18} />}
      iconSize={18}
      label={
        mode === 'email'
          ? __('Add phone field', 'mcf')
          : __('Remove phone field', 'mcf')
      }
      onClick={toggleMode}
      size="small"
    />
  );
}

function SubmitAlignmentToggle({
  alignment,
  onChange,
}: {
  alignment: 'left' | 'right';
  onChange: (alignment: 'left' | 'right') => void;
}) {
  const toggleAlignment = () => {
    onChange(alignment === 'left' ? 'right' : 'left');
  };

  return (
    <Button
      icon={alignment === 'left' ? <AlignLeft size={18} /> : <AlignRight size={18} />}
      iconSize={18}
      label={
        alignment === 'left'
          ? __('Align right', 'mcf')
          : __('Align left', 'mcf')
      }
      onClick={toggleAlignment}
      size="small"
    />
  );
}

function PrivacyModeToggle({
  mode,
  onChange,
}: {
  mode: 'inform' | 'optin';
  onChange: (mode: 'inform' | 'optin') => void;
}) {
  const toggleMode = () => {
    onChange(mode === 'inform' ? 'optin' : 'inform');
  };

  return (
    <Button
      icon={mode === 'inform' ? <Info size={18} /> : <CheckCircle2 size={18} />}
      iconSize={18}
      label={
        mode === 'inform'
          ? __('Switch to Opt-in', 'mcf')
          : __('Switch to Inform', 'mcf')
      }
      onClick={toggleMode}
      size="small"
    />
  );
}

const getNoticeMessage = (type: 'success' | 'error' | 'warning'): string => {
  switch (type) {
    case 'success':
      return __('Thank you for your message! We will get back to you soon.', 'mcf');
    case 'error':
      return __('There was an error submitting your form. Please check your entries and try again.', 'mcf');
    case 'warning':
      return __('Please note: This is a warning message example.', 'mcf');
  }
};

function NoticeTypeSelector({
  noticeType,
  onChange,
}: {
  noticeType: 'success' | 'error' | 'warning';
  onChange: (type: 'success' | 'error' | 'warning') => void;
}) {
  return (
    <div className="mcf-notice-type-controls">
      <Button
        icon={<CheckCircle2 size={18} />}
        iconSize={18}
        label={__('Success', 'mcf')}
        onClick={() => onChange('success')}
        className={`mcf-field-control mcf-notice-control ${noticeType === 'success' ? 'active' : ''}`}
        size="small"
        style={{
          backgroundColor: noticeType === 'success' ? '#d1e7dd' : undefined,
        }}
      />
      <Button
        icon={<AlertTriangle size={18} />}
        iconSize={18}
        label={__('Error', 'mcf')}
        onClick={() => onChange('error')}
        className={`mcf-field-control mcf-notice-control ${noticeType === 'error' ? 'active' : ''}`}
        size="small"
        style={{
          backgroundColor: noticeType === 'error' ? '#f8d7da' : undefined,
        }}
      />
      <Button
        icon={<Info size={18} />}
        iconSize={18}
        label={__('Warning', 'mcf')}
        onClick={() => onChange('warning')}
        className={`mcf-field-control mcf-notice-control ${noticeType === 'warning' ? 'active' : ''}`}
        size="small"
        style={{
          backgroundColor: noticeType === 'warning' ? '#fff3cd' : undefined,
        }}
      />
    </div>
  );
}

function FieldSettingsModal({
  fields,
  onClose,
  onFieldsChange,
}: FieldSettingsModalProps) {
  const [localFields, setLocalFields] = useState<MCFFieldConfig>(fields);

  // Update local state when props change
  useEffect(() => {
    setLocalFields(fields);
  }, [fields]);

  const { field_groups, labels } = localFields;

  // Helper to update a specific field group
  const updateGroup = (
    group: keyof typeof field_groups,
    updates: Partial<(typeof field_groups)[typeof group]>
  ) => {
    setLocalFields({
      ...localFields,
      field_groups: {
        ...field_groups,
        [group]: {
          ...field_groups[group],
          ...updates,
        },
      },
    });
  };

  // Helper to get label
  const getLabel = (fieldId: string) => {
    return (
      labels[fieldId] ||
      fieldId.replace(/-/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase())
    );
  };

  const handleSave = () => {
    onFieldsChange(localFields);
    onClose();
  };

  return (
    <Modal
      title={__('Field Settings', 'mcf')}
      onRequestClose={onClose}
      className="mcf-field-settings-modal">
      <div className="mcf-modal-body">
        {/* Hide Labels Toggle */}
        <div className="mcf-hide-labels-section">
          <ToggleControl
            label={__('Hide Labels Visually', 'mcf')}
            checked={localFields.hide_labels ?? false}
            onChange={(val) =>
              setLocalFields({ ...localFields, hide_labels: val })
            }
            help={__(
              'Labels will be hidden visually but remain accessible to screen readers for better accessibility.',
              'mcf'
            )}
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
            onToggle={(val) => updateGroup('name', { enabled: val })}>
            <NameModeToggle
              mode={field_groups.name.mode}
              onChange={(mode) => updateGroup('name', { mode })}
            />
          </FieldRow>
        ) : (
          <div className="mcf-field-row-grid">
            <FieldRow label={getLabel('first-name')} />
            <FieldRow
              label={getLabel('last-name')}
              enabled={field_groups.name.enabled}
              onToggle={(val) => updateGroup('name', { enabled: val })}>
              <NameModeToggle
                mode={field_groups.name.mode}
                onChange={(mode) => updateGroup('name', { mode })}
              />
            </FieldRow>
          </div>
        )}

        {/* Contact Fields - Dynamic based on mode */}
        {field_groups.contact.mode === 'email' ? (
          <FieldRow label={getLabel('email')} isStatic>
            <ContactModeToggle
              mode={field_groups.contact.mode}
              onChange={(mode) => updateGroup('contact', { mode })}
            />
          </FieldRow>
        ) : (
          <div className="mcf-field-row-grid">
            <FieldRow label={getLabel('email')} />
            <FieldRow label={getLabel('phone')} isStatic>
              <ContactModeToggle
                mode={field_groups.contact.mode}
                onChange={(mode) => updateGroup('contact', { mode })}
              />
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
          <PrivacyModeToggle
            mode={field_groups.gdpr.mode}
            onChange={(mode) => updateGroup('gdpr', { mode })}
          />
        </FieldRow>

        {/* Submit Alignment */}
        <FieldRow label={getLabel('submit')} isStatic>
          <SubmitAlignmentToggle
            alignment={field_groups.submit.alignment}
            onChange={(alignment) => updateGroup('submit', { alignment })}
          />
        </FieldRow>
      </div>

      <div className="mcf-modal-footer">
        <Button variant="secondary" onClick={onClose}>
          {__('Cancel', 'mcf')}
        </Button>
        <Button variant="primary" onClick={handleSave}>
          {__('Apply', 'mcf')}
        </Button>
      </div>
    </Modal>
  );
}

export default function FormPreview({
  fields,
  customCSS,
  theme,
  variant,
  primaryColor,
  privacyTexts,
  onFieldsChange,
}: FormPreviewProps) {
  const [editingField, setEditingField] = useState<string | null>(null);
  const [isFieldSettingsOpen, setIsFieldSettingsOpen] = useState(false);
  const [noticeType, setNoticeType] = useState<'success' | 'error' | 'warning'>('success');

  // Helper functions for color calculations
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

  // Generate primary color CSS client-side (LIVE, no API calls)
  const generatePrimaryColorCSS = (color: string): string => {
    if (!color) return '';

    const hoverColor = adjustBrightness(color, -20);
    const textColor = getContrastColor(color);
    const textHoverColor = getContrastColor(hoverColor);

    return `
#mcf-preview[data-theme-variant="light"],
#mcf-preview[data-theme-variant="dark"] {
  --mcf-button-background-color: ${color} !important;
  --mcf-button-background-hover-color: ${hoverColor} !important;
  --mcf-button-color: ${textColor} !important;
  --mcf-button-hover-color: ${textHoverColor} !important;
  --mcf-checkbox-color: ${color} !important;
}`;
  };

  const primaryColorCSS = generatePrimaryColorCSS(primaryColor || '');

  // State for Base + Theme CSS from API
  const [baseThemeCSS, setBaseThemeCSS] = useState<string>('');

  // Load Base + Theme CSS via API (only when theme changes)
  useEffect(() => {
    const loadBaseThemeCSS = async () => {
      try {
        const response = await fetch(
          `${window.mcfAdmin.apiUrl}/preview-css?theme=${theme}`,
          {
            headers: {
              'X-WP-Nonce': window.mcfAdmin.nonce,
            },
          }
        );

        const data = await response.json();

        if (data.success) {
          setBaseThemeCSS(data.css);
        }
      } catch (error) {
        console.error('Failed to load base/theme CSS:', error);
      }
    };

    loadBaseThemeCSS();
  }, [theme]); // Only trigger on theme change

  // Consolidate all CSS layers and inject into document head
  useEffect(() => {
    // Layer 1: Base + Theme CSS (from API)
    let consolidatedCSS = baseThemeCSS;

    // Layer 2: Custom CSS (if theme is 'custom')
    if (theme === 'custom' && customCSS) {
      consolidatedCSS += '\n\n' + customCSS;
    }

    // Layer 3: Primary Color CSS (client-side generated)
    if (primaryColor) {
      consolidatedCSS += '\n\n' + primaryColorCSS;
    }

    // Inject into document head
    const styleElement = document.getElementById('mcf-preview-css');

    if (styleElement) {
      styleElement.textContent = consolidatedCSS;
    } else if (consolidatedCSS) {
      const style = document.createElement('style');
      style.id = 'mcf-preview-css';
      style.textContent = consolidatedCSS;
      document.head.appendChild(style);
    }

    return () => {
      document.getElementById('mcf-preview-css')?.remove();
    };
  }, [baseThemeCSS, theme, customCSS, primaryColor, primaryColorCSS]);

  if (!fields?.field_groups) {
    return (
      <Panel>
        <PanelBody title={__('Form Fields', 'mcf')} initialOpen={true}>
          <p>{__('Loading fields...', 'mcf')}</p>
        </PanelBody>
      </Panel>
    );
  }

  const { field_groups, labels, placeholders } = fields;

  // Get label for a field
  const getLabel = (fieldId: string) => {
    // Return custom label OR default label (translated!)
    return labels[fieldId] || getDefaultLabel(fieldId);
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
    const hideLabels = fields.hide_labels ?? false;

    // Compute placeholder based on hide_labels setting
    const computePlaceholder = () => {
      if (hideLabels) {
        // Labels are hidden → Placeholder = Label + asterisk (if required)
        return required ? `${label} *` : label;
      } else {
        // Labels are visible → Only use explicitly entered placeholder
        return explicitPlaceholder || '';
      }
    };

    const placeholder = computePlaceholder();

    return (
      <div
        className={`mcf-field mcf-field-${fieldId}`}
        key={fieldId}
        data-full-width={fullWidth}>
        <div className="mcf-preview-field-controls">
          <Button
            icon={<Pencil size={18} />}
            iconSize={18}
            label={__('Edit label & placeholder', 'mcf')}
            onClick={() => setEditingField(fieldId)}
            className="mcf-field-control mcf-edit"
            size="small"
          />
        </div>
        <label
          className={`mcf-label ${hideLabels ? 'mcf-sr-only' : ''}`}
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
        // Email and Phone side by side
        fields.push(renderField('email', 'email', true, false));
        fields.push(renderField('phone', 'tel', false, false));
      } else {
        // Email full width
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
          ? labels['gdpr-optin'] ||
            privacyTexts.optin_text ||
            __('I agree to the privacy policy', 'mcf')
          : labels['gdpr-inform'] ||
            privacyTexts.inform_text ||
            __(
              'Your data will be processed according to our privacy policy',
              'mcf'
            );

      fields.push(
        <div className="mcf-field mcf-field-privacy" key="gdpr">
          {gdprMode === 'optin' ? (
            <label className="mcf-checkbox-label" htmlFor="mcf-privacy">
              <input
                type="checkbox"
                id="mcf-privacy"
                className="mcf-checkbox"
                readOnly
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
  }, [fields, privacyTexts]);

  return (
    <>
      <Panel>
        <PanelBody title={__('Form Preview', 'mcf')} initialOpen={true}>
          <div className="mcf-preview-header">
            <p className="description">
              {__(
                'Click the edit icon on any field to customize its label and placeholder.',
                'mcf'
              )}
            </p>
            <Button
              variant="secondary"
              onClick={() => setIsFieldSettingsOpen(true)}
              className="mcf-edit-fields-btn">
              {__('Edit Fields', 'mcf')}
              <Settings size={16} />
            </Button>
          </div>

          <div className="mcf-form-preview">
            {/* Form with theme variant data attribute */}
            <div
              id="mcf-preview"
              className="mcf-form mcf-contact-form"
              data-theme-variant={variant}>
              {/* Notice Preview with Controls */}
              <div className="mcf-notice-wrapper" style={{ position: 'relative' }}>
                <NoticeTypeSelector
                  noticeType={noticeType}
                  onChange={setNoticeType}
                />
                <div className={`mcf-notice show ${noticeType}`}>
                  {getNoticeMessage(noticeType)}
                </div>
              </div>

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
        </PanelBody>
      </Panel>

      {/* Edit Modal */}
      {editingField && (
        <EditModal
          fieldId={editingField}
          label={labels[editingField] || ''}
          placeholder={placeholders[editingField] || ''}
          onSave={(label, placeholder) => {
            // Update both label and placeholder in a single call to avoid race condition
            onFieldsChange({
              ...fields,
              labels: {
                ...labels,
                [editingField]: label,
              },
              placeholders: {
                ...placeholders,
                [editingField]: placeholder,
              },
            });
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
  );
}
