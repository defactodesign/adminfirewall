# Defacto_Adminfirewall

Restrict access to the administration panel via an IP whitelist.

Tested with Magento 1.9.x+ most likely will work with earlier versions.

The module will prevent access to any administration area, regardless of 
the frontname used. This means it will protect against modules that incorrectly
add admin controllers without the administration prefix (Fixed in supee-6788).

Pull requests welcome.

developers@de-facto.com

## FAQs

### Why do you extend the event observer rather than defining your own observer for the same event?
It is imperative that the code in our event observer is processed before the default admin observer, otherwise
it will not prevent brute force login attempts against non-default admin frontnames. Since Magento has no
mechanism to control the order of event observers, rewritting the original observer is the cleanest option.
