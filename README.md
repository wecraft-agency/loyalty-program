# A Loyalty program for shopware

Loyalty program with points for Shopware.

Points getting added by order when products have points defined.

## What it does

- Manage rewards in admin. Of type "product" or "discount".
- Make customer gain points based on "price" or "points of product".
- Giving customer points based on payment status. When status isnt "paid" points are in "pending". When status changes to "paid" points get converted to actual credits. When points in "pending", and payment get canceled or fails the pending points of specific order getting removed of pending.
- Saves orders in custom entity redemption. To generate a "point history". Order + Customer is linked to each redemption. To make data easy understandable + display it in frontend.