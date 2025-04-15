import DomAccess from 'src/helper/dom-access.helper';
import FormSerializeUtil from 'src/utility/form/form-serialize.util';
import Iterator from 'src/helper/iterator.helper';

const { PluginBaseClass } = window;

export default class AddRewardToCartPlugin extends PluginBaseClass {

    static options = {
        redirectSelector: '[name="redirectTo"]',
        redirectTo: 'frontend.cart.offcanvas',
    };

    init() {
        this._getForm();

        if (!this._form) {
            throw new Error(`No form found for the plugin: ${this.constructor.name}`);
        }

        this._prepareFormRedirect();

        this._registerEvents();
    }

    _prepareFormRedirect() {
        try {
            const redirectInput = DomAccess.querySelector(this._form, this.options.redirectSelector);

            redirectInput.value = this.options.redirectTo;
        } catch (e) {
            // preparations are not needed if fields are not available
        }
    }

    _getForm() {
        if (this.el && this.el.nodeName === 'FORM') {
            this._form = this.el;
        } else {
            this._form = this.el.closest('form');
        }
    }

    _registerEvents() {
        this.el.addEventListener('submit', this._formSubmit.bind(this));
    }

    _formSubmit(event) {
        event.preventDefault();

        const requestUrl = DomAccess.getAttribute(this._form, 'action');
        const formData = FormSerializeUtil.serialize(this._form);

        this.$emitter.publish('beforeFormSubmit', formData);

        this._openOffCanvasCarts(requestUrl, formData);
    }

    _openOffCanvasCarts(requestUrl, formData) {
        const offCanvasCartInstances = window.PluginManager.getPluginInstances('OffCanvasCart');
        Iterator.iterate(offCanvasCartInstances, instance => this._openOffCanvasCart(instance, requestUrl, formData));
    }

    _openOffCanvasCart(instance, requestUrl, formData) {
        instance.openOffCanvas(requestUrl, formData, () => {
            this.$emitter.publish('openOffCanvasCart');
        });
    }
}