import { render } from '@wordpress/element';
import App from './App';
import './style.scss';

const root = document.getElementById('mcf-admin-root');

if (root) {
	render(<App />, root);
}
