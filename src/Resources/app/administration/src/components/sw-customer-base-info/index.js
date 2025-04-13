import template from './template.html.twig';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Component.override('sw-customer-base-info', {
    template,

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    }
})