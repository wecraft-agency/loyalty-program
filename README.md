# A Loyalty program for shopware

Loyalty program with points for Shopware.

Points getting added by order when products have points defined.

## What it does

- Manage rewards in admin. Of type "product" or "discount".
- Make customer gain points based on "price" or "points of product".
- Giving customer points based on payment status. When status isnt "paid" points are in "pending". When status changes to "paid" points get converted to actual credits. When points in "pending", and payment get canceled or fails the pending points of specific order getting removed of pending.
- Saves orders in custom entity redemption. To generate a "point history". Order + Customer is linked to each redemption. To make data easy understandable + display it in frontend.

## Account
- Rewards and Point history in account
- Adding reward products to cart via ajax and custom controller
- checking if basket current reward products and item to add are valid to buy with current points of customer. if not not addable
- Reward list: showing how many points left for certain rewards
- Create discounts by reward product, if type discount. Absolute and percentage value possible