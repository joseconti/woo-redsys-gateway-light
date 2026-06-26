const settings = window.wc.wcSettings.getSetting('my_custom_gateway_data', {});
const label = window.my_custom_gateway_data.title
const Content = () => {
    return window.wp.htmlEntities.decodeEntities(settings.description || '');
};
const Block_Gateway = {
    name: 'inespay',
    label: label,
    content: Object(window.wp.element.createElement)(Content, null),
    edit: Object(window.wp.element.createElement)(Content, null),
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
};
window.wc.wcBlocksRegistry.registerPaymentMethod(Block_Gateway);