import { defineComponent } from 'vue';
import SvgIcon from '@/components/SvgIcon';

// register globally
defineComponent('svg-icon', SvgIcon);

const requireAll = requireContext => requireContext.keys().map(requireContext);
const req = require.context('./svg', false, /\.svg$/);
requireAll(req);
