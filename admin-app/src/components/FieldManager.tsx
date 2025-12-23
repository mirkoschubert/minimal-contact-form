import { Panel, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { MCFFieldConfig } from '../types';

interface FieldManagerProps {
	fields: MCFFieldConfig;
	onFieldsChange: (fields: MCFFieldConfig) => void;
}

export default function FieldManager({ fields, onFieldsChange }: FieldManagerProps) {
	// TODO: Implement full drag & drop with @dnd-kit in Phase 4
	return (
		<Panel>
			<PanelBody title={__('Field Management', 'mcf')} initialOpen={true}>
				<p>{__('Field management with drag & drop will be implemented in Phase 4', 'mcf')}</p>
				<p>
					{__(
						'Features: Reorder fields, toggle enabled/disabled, custom labels & placeholders, custom fields',
						'mcf'
					)}
				</p>

				{fields?.order && (
					<div style={{ marginTop: '16px' }}>
						<strong>{__('Current field order:', 'mcf')}</strong>
						<ul style={{ marginTop: '8px' }}>
							{fields.order.map((fieldId) => (
								<li key={fieldId}>
									{fieldId} {fields.enabled?.[fieldId] ? '✓' : '✗'}
								</li>
							))}
						</ul>
					</div>
				)}
			</PanelBody>
		</Panel>
	);
}
