const PluginManager = window.PluginManager;

import AddRewardToCart from './loyalty-program/add-reward-to-cart/add-reward-to-cart.plugin';

PluginManager.register('AddRewardToCart', AddRewardToCart, '[data-add-reward-to-cart]');