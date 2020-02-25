# cal-display
PHP web site to view Office 365 resource calendars and book meetings using the Graph API

To use this, first create an Enterprise Application in Azure Active Directory. Give it permission to read all user calendars (and edit if you want to be able to book meetings)

Get the Application ID from the AzureAD application, and enter it into config.php.

Edit the remaining values in config.php to match your tenant.

Point your display devices to the three important files:
index.php - Meant for a main reception display, shows all the meetings for all the rooms that match your filter.

book.php - pass this a GET request setting room, for example https://example.com/book.php?room=room123@example.onmicrosoft.com - This shows the meetings only for that one room. Also allows you to book an impromptu meeting. Designed for a tablet inside the room or on the door.

waag.php - Week At A Glance. Shows all the events for all the rooms, but for the entire week. Helpful for a receptionist or whoever manages bookings to see them all at once. Pass this a number of weeks to add in order to show next week. eg https://example.com/waag.php?add=1 to view next week.