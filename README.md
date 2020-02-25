# cal-display
PHP web site to view Office 365 resource calendars and book meetings using the Graph API

To use this, first create an Enterprise Application in Azure Active Directory. Give it permission to read all user calendars (and edit if you want to be able to book meetings)

Get the Application ID from the AzureAD application, and enter it into config.php.

Edit the remaining values in config.php to match your tenant.