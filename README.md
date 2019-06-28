Engagement Cloud for Magento
==========================================

Release candidate - Fix coupon code check
-----------------------------------------

As part of a code audit in 2017 a change was made to how our Rule collection overide decided whether a couponCode was specified.
See: https://github.com/dotmailer/dotmailer-magento-extension/commit/73a5ca63a7b2a6858a02b57e1b3603f8f2e1fa1d#diff-eef8874db7445bbd017cd938af5bec92

This means that at unneecessarily large sql query is executed. Effectively adding a join on the coupon table which is redundant if couponCode is null.

We've rolled back that change and the code now has the decision logic as the core class it overides.
See: https://github.com/dotmailer/dotmailer-magento-extension/commit/57c62acdff49edb326fb3cee2dac95a6120f9706

This means the same sql is executed as would be without our extension enabled. If a coupon code is specified, we add a filter on expiration_date. This is required to support coupon codes with a different expiration date to the rule they link to.