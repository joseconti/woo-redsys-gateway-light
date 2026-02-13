
import { sprintf, __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';

const settings              = getSetting( 'redsys_data', {} );
const settingsbizumredsys   = getSetting( 'bizumredsys_data', {} );
const settingsgpayredsys    = getSetting( 'googlepayredirecredsys_data', {} );
const settingsinespayredsys = getSetting( 'inespayredsys_data', {} );

const defaultLabel = __(
	'Redsys',
	'woo-redsys-gateway-light'
);
const defaultLabelBizum = __(
	'Bizum',
	'woo-redsys-gateway-light'
);
const defaultLabelGpayRed = __(
	'Google Pay',
	'woo-redsys-gateway-light'
);
const defaultLabelInespay = __(
	'Inespay Bank Transfer',
	'woo-redsys-gateway-light'
);

const label         = decodeEntities( settings.title ) || defaultLabel;
const labelbizum    = decodeEntities( settingsbizumredsys.title ) || defaultLabelBizum;
const labelgpayred  = decodeEntities( settingsgpayredsys.title ) || defaultLabelGpayRed;
const labelinespay  = decodeEntities( settingsinespayredsys.title ) || defaultLabelInespay;
/**
 * Content component
 */
const Content = () => {
	return decodeEntities( settings.description || '' );
};
const Contentbizum = () => {
	return decodeEntities( settingsbizumredsys.description || '' );
};
const Contengpayred = () => {
	return decodeEntities( settingsgpayredsys.description || '' );
};
const Contentinespay = () => {
	return decodeEntities( settingsinespayredsys.description || '' );
};
/**
 * Label component
 *
 * @param {*} props Props from payment API.
 */
const Label = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	return <PaymentMethodLabel text={ label } />;
};
const Labelbizum = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	return <PaymentMethodLabel text={ labelbizum } />;
};
const Labelgpayred = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	return <PaymentMethodLabel text={ labelgpayred } />;
};
const Labelinespay = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	return <PaymentMethodLabel text={ labelinespay } />;
};

/**
 * Dummy payment method config object.
 */
const Redsys = {
	name: "redsys",
	label: <Label />,
	content: <Content />,
	edit: <Content />,
	canMakePayment: () => true,
	ariaLabel: label,
	supports: {
		features: settings.supports,
	},
};
const Bizum = {
	name: "bizumredsys",
	label: <Labelbizum />,
	content: <Contentbizum />,
	edit: <Contentbizum />,
	canMakePayment: () => true,
	ariaLabel: labelbizum,
	supports: {
		features: settingsbizumredsys.supports,
	},
};
const GPayRed = {
	name: "googlepayredirecredsys",
	label: <Labelgpayred />,
	content: <Contengpayred />,
	edit: <Contengpayred />,
	canMakePayment: () => true,
	ariaLabel: labelgpayred,
	supports: {
		features: settingsgpayredsys.supports,
	},
};
const Inespay = {
	name: "inespayredsys",
	label: <Labelinespay />,
	content: <Contentinespay />,
	edit: <Contentinespay />,
	canMakePayment: () => true,
	ariaLabel: labelinespay,
	supports: {
		features: settingsinespayredsys.supports,
	},
};
registerPaymentMethod( Redsys );
registerPaymentMethod( Bizum );
registerPaymentMethod( GPayRed );
registerPaymentMethod( Inespay );
