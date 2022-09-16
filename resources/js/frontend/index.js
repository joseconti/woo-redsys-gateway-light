
import { sprintf, __ } from '@wordpress/i18n';
import { registerPaymentMethod } from '@woocommerce/blocks-registry';
import { decodeEntities } from '@wordpress/html-entities';
import { getSetting } from '@woocommerce/settings';

const settings            = getSetting( 'redsys_data', {} );
const settingsbizumredsys = getSetting( 'bizumredsys_data', {} );

const defaultLabel = __(
	'Redsys',
	'woo-redsys-gateway-light'
);
const defaultLabelBizum = __(
	'Bizum',
	'woo-redsys-gateway-light'
);

const label      = decodeEntities( settings.title ) || defaultLabel;
const labelbizum = decodeEntities( settingsbizumredsys.title ) || defaultLabelBizum;
/**
 * Content component
 */
const Content = () => {
	return decodeEntities( settings.description || '' );
};
const Contentbizum = () => {
	return decodeEntities( settingsbizumredsys.description || '' );
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

registerPaymentMethod( Redsys );
registerPaymentMethod( Bizum );
