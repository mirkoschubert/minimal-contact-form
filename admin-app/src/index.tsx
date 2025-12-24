import { createRoot } from '@wordpress/element';
import App from './App';
import './admin.scss';

const container = document.getElementById('mcf-admin-root');
const root = createRoot(container!);

if (root) {
	root.render(<App />);
}
