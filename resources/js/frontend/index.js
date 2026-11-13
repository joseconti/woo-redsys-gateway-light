
/**
 * JSX is compiled to createElement() from @wordpress/element on purpose.
 *
 * The build tools' default (the automatic JSX runtime) makes the script
 * depend on the `react-jsx-runtime` handle, which WordPress only registers
 * from 6.6 on: on an older WordPress the script would not load and the
 * gateways would vanish from the Blocks checkout. `wp-element` exists on
 * every WordPress that has blocks and uses the site's own React.
 *
 * @jsxRuntime classic
 * @jsx createElement
 */
import { createElement } from '@wordpress/element';
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
	const icon = settings.icon;
	return (
		<div style={ { display: 'flex', alignItems: 'center' } }>
			<PaymentMethodLabel text={ label } />
			{ icon && (
				<img
					src={ icon }
					alt={ label }
					style={ { marginLeft: '8px', maxHeight: '24px' } }
				/>
			) }
		</div>
	);
};
const Labelbizum = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	const icon = settingsbizumredsys.icon;
	return (
		<div style={ { display: 'flex', alignItems: 'center' } }>
			<PaymentMethodLabel text={ labelbizum } />
			{ icon && (
				<img
					src={ icon }
					alt={ labelbizum }
					style={ { marginLeft: '8px', maxHeight: '24px' } }
				/>
			) }
		</div>
	);
};
const Labelgpayred = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	const icon = settingsgpayredsys.icon;
	return (
		<div style={ { display: 'flex', alignItems: 'center' } }>
			<PaymentMethodLabel text={ labelgpayred } />
			{ icon && (
				<img
					src={ icon }
					alt={ labelgpayred }
					style={ { marginLeft: '8px', maxHeight: '24px' } }
				/>
			) }
		</div>
	);
};
const Labelinespay = ( props ) => {
	const { PaymentMethodLabel } = props.components;
	const icon = settingsinespayredsys.icon;
	return (
		<div style={ { display: 'flex', alignItems: 'center' } }>
			<PaymentMethodLabel text={ labelinespay } />
			{ icon && (
				<img
					src={ icon }
					alt={ labelinespay }
					style={ { marginLeft: '8px', maxHeight: '24px' } }
				/>
			) }
		</div>
	);
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
/**
 * Inespay serves only the countries the plugin hands over (S-066): the
 * shipping country when there is one, else the billing country, the same
 * order the classic checkout uses. Without that list — the plugin handed no
 * data because the customer is elsewhere — it is not offered at all.
 *
 * @param {Object} cart Data the Blocks checkout passes to canMakePayment.
 * @return {boolean} Whether Inespay can be offered for this address.
 */
const canPayWithInespay = ( cart ) => {
	const countries = Array.isArray( settingsinespayredsys.countries ) ? settingsinespayredsys.countries : [];
	if ( ! countries.length ) {
		return false;
	}
	const shipping = ( cart && cart.shippingAddress && cart.shippingAddress.country ) || '';
	const billing = ( cart && cart.billingAddress && cart.billingAddress.country ) || ( cart && cart.billingData && cart.billingData.country ) || '';
	const country = ( shipping || billing ).toUpperCase();
	// No address typed yet: the server already decided from the session.
	return '' === country || countries.indexOf( country ) !== -1;
};
const Inespay = {
	name: "inespayredsys",
	label: <Labelinespay />,
	content: <Contentinespay />,
	edit: <Contentinespay />,
	canMakePayment: canPayWithInespay,
	ariaLabel: labelinespay,
	supports: {
		features: settingsinespayredsys.supports,
	},
};
registerPaymentMethod( Redsys );
registerPaymentMethod( Bizum );
registerPaymentMethod( GPayRed );
registerPaymentMethod( Inespay );
