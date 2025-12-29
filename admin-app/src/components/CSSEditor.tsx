import { useCallback, useMemo } from '@wordpress/element';
import CodeMirror from '@uiw/react-codemirror';
import { tokyoNight } from '@uiw/codemirror-theme-tokyo-night';
import { css } from '@codemirror/lang-css';
import { autocompletion, CompletionContext } from '@codemirror/autocomplete';

interface CSSEditorProps {
	value: string;
	onChange: (value: string) => void;
	height?: string;
}

/**
 * CodeMirror 6 CSS Editor Component using @uiw/react-codemirror
 *
 * Features:
 * - CSS syntax highlighting with Tokyo Night theme
 * - Autocomplete for MCF CSS variables (--mcf-*) and CSS classes (.mcf-*)
 * - Auto-closing brackets
 * - Line numbers and active line highlighting
 * - Bracket matching
 * - History (undo/redo)
 */
export default function CSSEditor({ value, onChange, height = '450px' }: CSSEditorProps) {
	// MCF CSS Variables for autocomplete (28 variables)
	const mcfVariables = useMemo(
		() => [
			// Layout & Spacing
			'--mcf-border-radius',
			'--mcf-item-spacing',
			'--mcf-item-padding',
			'--mcf-desktop-max-width',
			// Colors - Text
			'--mcf-text-color',
			'--mcf-placeholder-color',
			// Colors - Borders & Backgrounds
			'--mcf-border-color',
			'--mcf-item-background-color',
			// Colors - Interactive
			'--mcf-checkbox-color',
			'--mcf-error-color',
			'--mcf-success-color',
			'--mcf-warning-color',
			// Labels
			'--mcf-label-font-size',
			'--mcf-label-font-weight',
			// GDPR
			'--mcf-gdpr-font-size',
			// Buttons
			'--mcf-button-color',
			'--mcf-button-hover-color',
			'--mcf-button-background-color',
			'--mcf-button-background-hover-color',
			'--mcf-button-padding',
			'--mcf-button-font-weight',
			// Notice Messages
			'--mcf-success-text-color',
			'--mcf-success-bg-color',
			'--mcf-success-border-color',
			'--mcf-warning-text-color',
			'--mcf-warning-bg-color',
			'--mcf-warning-border-color',
			'--mcf-error-text-color',
			'--mcf-error-bg-color',
			'--mcf-error-border-color',
		],
		[]
	);

	// MCF CSS Classes for autocomplete
	const mcfClasses = useMemo(
		() => [
			'.mcf-form',
			'.mcf-field',
			'.mcf-field-group',
			'.mcf-field-row',
			'.mcf-input',
			'.mcf-textarea',
			'.mcf-label',
			'.mcf-submit-button',
			'.mcf-gdpr',
			'.mcf-gdpr-checkbox',
			'.mcf-gdpr-label',
			'.mcf-notice',
			'.mcf-notice-success',
			'.mcf-notice-error',
			'.mcf-notice-warning',
		],
		[]
	);

	// CSS Shortcuts for autocomplete
	const cssShortcuts = useMemo(
		() => [
			{
				label: 'form:light',
				detail: 'Light variant selector',
				insert: '.mcf-form[data-theme-variant="light"]',
			},
			{
				label: 'form:dark',
				detail: 'Dark variant selector',
				insert: '.mcf-form[data-theme-variant="dark"]',
			},
		],
		[]
	);

	/**
	 * Custom autocomplete for MCF CSS variables, classes, and shortcuts
	 */
	const mcfCompletion = useCallback(
		(context: CompletionContext) => {
			// Check for CSS variable (--mcf-)
			const varWord = context.matchBefore(/--mcf-[\w-]*/);
			if (varWord && (varWord.from !== varWord.to || context.explicit)) {
				return {
					from: varWord.from,
					options: mcfVariables.map((variable) => ({
						label: variable,
						type: 'variable',
						detail: 'MCF CSS Variable',
					})),
				};
			}

			// Check for CSS class (.mcf-)
			const classWord = context.matchBefore(/\.mcf-[\w-]*/);
			if (classWord && (classWord.from !== classWord.to || context.explicit)) {
				return {
					from: classWord.from,
					options: mcfClasses.map((className) => ({
						label: className,
						type: 'class',
						detail: 'MCF CSS Class',
					})),
				};
			}

			// Check for shortcuts (form:)
			const shortcutWord = context.matchBefore(/form:[\w-]*/);
			if (shortcutWord && (shortcutWord.from !== shortcutWord.to || context.explicit)) {
				return {
					from: shortcutWord.from,
					options: cssShortcuts.map((shortcut) => ({
						label: shortcut.label,
						type: 'keyword',
						detail: shortcut.detail,
						apply: shortcut.insert,
					})),
				};
			}

			return null;
		},
		[mcfVariables, mcfClasses, cssShortcuts]
	);

	// CodeMirror extensions
	const extensions = useMemo(
		() => [
			css(),
			autocompletion({
				override: [mcfCompletion],
				activateOnTyping: true,
			}),
		],
		[mcfCompletion]
	);

	return (
		<div className="mcf-css-editor">
			<CodeMirror
				value={value}
				height={height}
				theme={tokyoNight}
				extensions={extensions}
				onChange={onChange}
				basicSetup={{
					lineNumbers: true,
					highlightActiveLineGutter: true,
					highlightSpecialChars: true,
					history: true,
					foldGutter: false,
					drawSelection: true,
					dropCursor: true,
					allowMultipleSelections: true,
					indentOnInput: true,
					syntaxHighlighting: true,
					bracketMatching: true,
					closeBrackets: true,
					autocompletion: true,
					rectangularSelection: true,
					crosshairCursor: true,
					highlightActiveLine: true,
					highlightSelectionMatches: true,
					closeBracketsKeymap: true,
					defaultKeymap: true,
					searchKeymap: false,
					historyKeymap: true,
					foldKeymap: false,
					completionKeymap: true,
					lintKeymap: true,
				}}
			/>
		</div>
	);
}
