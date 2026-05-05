import './bootstrap';
import './theme';
import './confirm-dialog';
import './notifications';

import 'vanilla-sonner/style.css';
import { toast } from 'vanilla-sonner';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

window.toast = toast;
