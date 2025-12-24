import { useState, useMemo, useEffect, useRef } from '@wordpress/element';
import { Panel, PanelBody, ToggleControl, RadioControl, Modal, TextControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFFieldConfig } from '../types';

interface FieldGroupManagerProps {
	fields: MCFFieldConfig;
	customCSS: string;
	theme: string;
	variant: string;
	onFieldsChange: (fields: MCFFieldConfig) => void;
}

interface EditModalProps {
	fieldId: string;
	label: string;
	placeholder: string;
	onSave: (label: string, placeholder: string) => void;
	onClose: () => void;
}

function EditModal({ fieldId, label, placeholder, onSave, onClose }: EditModalProps) {
	const [editLabel, setEditLabel] = useState(label);
	const [editPlaceholder, setEditPlaceholder] = useState(placeholder);

	const displayFieldName = fieldId.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

	return (
		<Modal title={__('Edit Field', 'mcf')} onRequestClose={onClose}>
			<TextControl
				label={__('Custom Label', 'mcf')}
				value={editLabel}
				onChange={setEditLabel}
				placeholder={displayFieldName}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<TextControl
				label={__('Placeholder', 'mcf')}
				value={editPlaceholder}
				onChange={setEditPlaceholder}
				placeholder={editLabel || displayFieldName}
				__next40pxDefaultSize
				__nextHasNoMarginBottom
			/>
			<div style={{ marginTop: '20px', display: 'flex', gap: '8px', justifyContent: 'flex-end' }}>
				<Button variant="secondary" onClick={onClose}>
					{__('Cancel', 'mcf')}
				</Button>
				<Button
					variant="primary"
					onClick={() => {
						onSave(editLabel, editPlaceholder);
						onClose();
					}}
				>
					{__('Save', 'mcf')}
				</Button>
			</div>
		</Modal>
	);
}

export default function FieldGroupManager({
	fields,
	customCSS,
	theme,
	variant,
	onFieldsChange,
}: FieldGroupManagerProps) {
	const [editingField, setEditingField] = useState<string | null>(null);

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

	// Helper to update a specific field group
	const updateGroup = (group: keyof typeof field_groups, updates: Partial<typeof field_groups[typeof group]>) => {
		onFieldsChange({
			...fields,
			field_groups: {
				...field_groups,
				[group]: {
					...field_groups[group],
					...updates,
				},
			},
		});
	};

	// Helper to update labels
	const updateLabel = (fieldId: string, newLabel: string) => {
		onFieldsChange({
			...fields,
			labels: {
				...labels,
				[fieldId]: newLabel,
			},
		});
	};

	// Helper to update placeholders
	const updatePlaceholder = (fieldId: string, newPlaceholder: string) => {
		onFieldsChange({
			...fields,
			placeholders: {
				...placeholders,
				[fieldId]: newPlaceholder,
			},
		});
	};

	// Get label for a field
	const getLabel = (fieldId: string) => {
		return labels[fieldId] || fieldId.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
	};

	// Get placeholder for a field
	const getPlaceholder = (fieldId: string) => {
		return placeholders[fieldId] || '';
	};

	// Render a preview field
	const renderField = (fieldId: string, type: string = 'text', required: boolean = false, fullWidth: boolean = false) => {
		const label = getLabel(fieldId);
		const placeholder = getPlaceholder(fieldId);
		const isTextarea = fieldId === 'message';

		return (
			<div className="mcf-preview-field" key={fieldId} data-full-width={fullWidth}>
				<div className="mcf-preview-field-controls">
					<button
						type="button"
						className="mcf-field-control mcf-edit"
						onClick={() => setEditingField(fieldId)}
						title={__('Edit label & placeholder', 'mcf')}
					>
						<span className="dashicons dashicons-edit"></span>
					</button>
				</div>
				<div className="mcf-preview-field-content">
					<label className="mcf-preview-label">
						{label}
						{required && <span className="required">*</span>}
					</label>
					{isTextarea ? (
						<textarea
							className="mcf-preview-input"
							placeholder={placeholder || label}
							rows={4}
							readOnly
						/>
					) : (
						<input
							type={type}
							className="mcf-preview-input"
							placeholder={placeholder || label}
							readOnly
						/>
					)}
				</div>
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

		return fields;
	}, [field_groups, labels, placeholders]);

	return (
		<>
			<Panel>
				<PanelBody title={__('Form Fields', 'mcf')} initialOpen={true}>
					<div className="mcf-field-groups">
						{/* Company Toggle */}
						<div className="mcf-field-group">
							<ToggleControl
								label={__('Company Field', 'mcf')}
								checked={field_groups.company.enabled}
								onChange={(val) => updateGroup('company', { enabled: val })}
								help={__('Add an optional company/organization field', 'mcf')}
								__nextHasNoMarginBottom
							/>
						</div>

						{/* Name Mode */}
						<div className="mcf-field-group">
							<h4>{__('Name Field', 'mcf')}</h4>
							<RadioControl
								selected={field_groups.name.mode}
								options={[
									{ label: __('Single field (Full Name)', 'mcf'), value: 'single' },
									{ label: __('Split fields (First Name + Last Name)', 'mcf'), value: 'split' },
								]}
								onChange={(val) => updateGroup('name', { mode: val as 'single' | 'split' })}
								__nextHasNoMarginBottom
							/>
						</div>

						{/* Contact Mode */}
						<div className="mcf-field-group">
							<h4>{__('Contact Information', 'mcf')}</h4>
							<RadioControl
								selected={field_groups.contact.mode}
								options={[
									{ label: __('Email only', 'mcf'), value: 'email' },
									{ label: __('Email + Phone', 'mcf'), value: 'email-phone' },
								]}
								onChange={(val) => updateGroup('contact', { mode: val as 'email' | 'email-phone' })}
								__nextHasNoMarginBottom
							/>
						</div>

						{/* Subject Toggle */}
						<div className="mcf-field-group">
							<ToggleControl
								label={__('Subject Field', 'mcf')}
								checked={field_groups.subject.enabled}
								onChange={(val) => updateGroup('subject', { enabled: val })}
								help={__('Add a subject line field', 'mcf')}
								__nextHasNoMarginBottom
							/>
						</div>

						{/* Message - Always enabled */}
						<div className="mcf-field-group">
							<p className="description">
								{__('Message field is always enabled (required for contact forms)', 'mcf')}
							</p>
						</div>

						{/* Submit Alignment */}
						<div className="mcf-field-group">
							<h4>{__('Submit Button', 'mcf')}</h4>
							<RadioControl
								label={__('Alignment', 'mcf')}
								selected={field_groups.submit.alignment}
								options={[
									{ label: __('Left', 'mcf'), value: 'left' },
									{ label: __('Right', 'mcf'), value: 'right' },
								]}
								onChange={(val) => updateGroup('submit', { alignment: val as 'left' | 'right' })}
								__nextHasNoMarginBottom
							/>
						</div>
					</div>
				</PanelBody>

				<PanelBody title={__('Form Preview', 'mcf')} initialOpen={true}>
					<p className="description">
						{__('Click the edit icon on any field to customize its label and placeholder.', 'mcf')}
					</p>

					<div className="mcf-form-preview">
						{/* Inject custom CSS inline */}
						{customCSS && <style>{customCSS}</style>}

						{/* Form with theme variant data attribute */}
						<div className="mcf-form" data-theme-variant={variant}>
							<div className="mcf-preview-grid">
								{activeFields}
							</div>

							{/* Submit button */}
							<div className={`mcf-preview-submit mcf-submit-${field_groups.submit.alignment}`}>
								<button type="button" className="mcf-preview-button" disabled>
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
						updateLabel(editingField, label);
						updatePlaceholder(editingField, placeholder);
					}}
					onClose={() => setEditingField(null)}
				/>
			)}
		</>
	);
}
