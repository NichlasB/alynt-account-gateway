/**
 * Admin entry point.
 *
 * @package Alynt_Account_Gateway
 */

import './style.css';
import { alyntAgInitEmailSaveState } from './modules/email-save-state.js';
import { alyntAgInitTypographyPresets } from './modules/typography.js';
import { alyntAgInitColorControls } from './modules/colors.js';
import { alyntAgHandleMediaClick } from './modules/media.js';
import { alyntAgInitDashboardLinks } from './modules/dashboard-links.js';
import { alyntAgInitAdminFormState } from './modules/form-state.js';

alyntAgInitEmailSaveState();
alyntAgInitTypographyPresets();
alyntAgInitColorControls();
document.addEventListener( 'click', alyntAgHandleMediaClick );
alyntAgInitDashboardLinks();
alyntAgInitAdminFormState();
