# Defacto_Adminfirewall

Restrict access to the administration panel via an IP whitelist.

Tested with Magento 1.9.x+ most likely will work with earlier versions.

The module will prevent access to any administration area, regardless of 
the frontname used. This means it will protect against modules that incorrectly
add admin controllers without the administration prefix (Fixed in supee-6788).

Pull requests welcome.

developers@de-facto.com
