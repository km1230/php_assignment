--------------------------------------------------------------------------------------------------------
READ ME FIRST
* This is the schema of my PHP assignment 
* Please read this document
--------------------------------------------------------------------------------------------------------

General
* The operations are designed for better and simpler UX experience
	* no images upload is required for director
	* no CSV check since no actual credit card is involved
	* handy for _customer_ to make order from mobile devices
	* breadcrumbs for desktop views only
* All cafes work on school days (Monday to Friday)
* Login at the home page (index.php)
* All users can make orders, including cafe staff, managers and director 
* User current balances will be shown on index.php after logged in
* Login password for all dummy account is !23Qwe
    * Password is encrypted with SHA in database
* Dummy director account = 'DB0000'
* Manager account for each cafe
    * Lazenbys manager = 'CM0000'
    * The Ref manager = 'CM0001'
    * Trade Table manager = 'CM0002'
* Staff account = 'CSxxxx'
* Permission of staff is defined by level
	* Director = 400
	* All cafe managers = 3xx
	* All staff = 2xx
	* staff & manager for Lazenbys = xx1 (e.g. 201 for staff, 301 for manager)
	* staff & manager for The Ref = xx2 (e.g. 202 for staff, 302 for manager)
	* staff & manager for Trade Table = xx3 (e.g. 203 for staff, 303 for manager)
	* newly added staff by Director = 100
* Tables in databases include
	* users
	* lazenbys
	* ref
	* trade
	* orders
	* open
	* cart
* When registering or modifying account
    * Phone number must be 10 digits e.g. 0412312312
    * Credit card number must be valid i.e. starting with 4(Visa card), 5(Master card), 6(Discovery or Union)
    * Any errors detected will stop users from proceeding or submitting forms
* Security concerns
	* Forms which require customer's input are passed to database with mysqli prepare statement
	* Input fields in MyAccount, Allocate, SignUp pages are restricted by javascript before submitting
	* Some sensitive text input fields in index login, menu pages and master pages are cleaned by php function before transferring to databases.

--------------------------------------------------------------------------------------------------------

SignUp Page - registration.php
* Only words are allowed for user ID
	* Must be 2 alphabets followed by 4 digits
	* Check on duplicated user ID when focusout
* Password must contains 1 Uppercase + 1 Lowercase + digits + 1 symbols in any order
	* No whitespaces or blackslashes are allowed in password
	* confirmation of password is required
* Only words and whitespaces are allowed for user's Name
	* Must be beginned with uppercase
	* Must be end with lowercase
	* No consecutive whitespaces are allowed (i.e. 2 or more consecutive whitespaces)
* Email address in valid email format is required
	* Check on duplicated email address when focusout
* Phone number is required
	* 10 digits only
* Credit card is required
	* MasterCard, VisaCard, Discover/Union Card format (i.e card number begins with 4, 5 or 6)
* Any errors will disable sign-up button

--------------------------------------------------------------------------------------------------------

Cafe Menu Pages - lazenbys.php / ref.php / trade.php
* Opening-hour is displayed according to manager's time selection 
* Menu of the following working day is shown according to manage and director's selection
* Total price is calculated and shown at the bottom, based on the quantity of items being selected
* User balance is updated when choosing item
* No item selection, comment field or collection time for unauthenticated users
* Item quantity and 'collect time' are required for placing order
	* Select 0 for non-preferred items
* Remark for item is not mandatory upon ordering
* Collection time is restricted to 30mins after opening and 60mins before closing
* Brief detail of cart is shown on the same page
	* A button is provided for user to view their cart and check out
	* Unpaid items from previous dates will be deleted from database when customers enter the page
* When placing orders
	* A sum-up of item prices is shown in Sub-total
	* Items are added to cart
	* A brief detail of the cart is updated
* Buttons for staff are hidden until corresponding user is logged in
    * View Orders for staff and manager (Other cafe staff and manager cannot view the page)
    * Master page for director
    * Manager page for corresponding cafe manager and director

--------------------------------------------------------------------------------------------------------

MyCart - cart.php
* The page is restricted to authenticated users only
* Customer's order detail is shown in table
* A reminder will be shown instead if the cart is empty
* Unpaid items from previous dates will be deleted from database when customers enter the page
* Balances are shown
	* current wallet balance
	* total prices of order
	* calculated balance upon check out
* Customers can remove an item from the table before checkout
* Check out button will be disabled if
	* any checkbox is ticked in the table
	* wallet balance is less than total price
* Customers can access wallet in MyAccount via Recharge button
* Transaction proceed when Checkout button is clicked
	* Order detail will be added to `order` in database
	* Cart will be cleared
	* Wallet balance will be deducted
	* Customers will be redirected to view order history in MyAccount

--------------------------------------------------------------------------------------------------------

Cafe Order History - lazenbys_order.php / ref_order.php / trade_order.php
* Can only be viewed by director, corresponding cafe staff and manager 
* Only pre-orders for current date are shown
* Current date is shown
* A reminder of how many pre-orders for current day
* Table is ordered by collection time
* Order history of a cafe
	* Item Name
	* Quantity
	* Remark
	* Collect Time
	* Customer Name
	* A checkbox for staff to remove completed orders

--------------------------------------------------------------------------------------------------------

Cafe management page - lazenbys_manager.php / ref_manager.php / trade_manager.php
* Can only be accessed by corresponding cafe manager or director
* The entrance is provided in cafe menu page
* Items are displayed if weekday matches as assigned by director
* Manager can select which item to be displayed in the menu for customers
    * At least 1 item should be selected for the menu

--------------------------------------------------------------------------------------------------------

Mastering Cafe Menu - lazenbys_master.php / ref_master.php / trade_master.php
* Master page can only be accessed by director
* The entrance is provided in each cafe menu page
* Director can add new item to menu
* Director can edit names and prices of each item
* Director can assign the weekday option for a particular item
* Director can remove items from the list
	* At least 1 item should be left on the menu

--------------------------------------------------------------------------------------------------------

Account Page - account.php
* User can recharge wallet
	* decimal with 0.1 increment (i.e. 10 cents)
* User can choose to update account detail such as
	* Preferred name
	* Password
		* When user attempt to change password, new password must not be the same as old password
	* Phone number
	* Email
	* Credit card
* When updating account detail
	* Current password must be provided
	* All fields, except 'new password' & 'confirm new password', must be filled
	* 'Confirm new password' must be filled only if 'new password' is assigned
	* Email cannot be duplicated with other users' email address
* Order history is displayed in table
	* date for the order
	* cafe name
	* item name
	* quantity
	* remark
	* prices

--------------------------------------------------------------------------------------------------------

Allocation Page - allocate.php
* Can only be accessed by director
* Rules apply to adding new staff:
	* User id and email are checked with AJAX when adding new staff
	* Password is same as user id upon successfull addition
	* Default permission for new staff is 100 (i.e. not allocated yet)
* Rules apply to manpower allocation:
	* Each cafe has 1 manager only
	* There should be at least 1 staff + 1 manager in a cafe
	* Corresponding error will be displayed once director submitted

--------------------------------------------------------------------------------------------------------

Logout Page - logout.php
* User can logout by using
	* top menu bar, or
	* logout button at index page
* redirect to index page for successful logout
* can only be accessed by authenticated users

--------------------------------------------------------------------------------------------------------

Ajax - checkduplicated.php
* duplicated users detail are checked here 
	* email
	* user id (sid)
* called by reg.js / allocate.js

--------------------------------------------------------------------------------------------------------

Header - header.php
* connect to database via conn.php
* start of session
* define operation detail
	* time zone & current time
	* current date & following order date
* external sources
	* CSS - mystyle.css
	* CDN - jQuery

--------------------------------------------------------------------------------------------------------

Top menu bar - nav.php
* links to different pages
* displays links according to session
	* Dropdown menu for each hyperlink to cafe depends on user permission
		* Director can access all
		* Manager can see 'view orders' and 'manager' links
		* staff can see 'view orders' link only
	* displays 'Sign Up', 'MyAccount' and 'MyCart' for unauthenticated users
	* displays 'Logout' for authenticated users
	* displays  'Allocate' for Director only

--------------------------------------------------------------------------------------------------------

Footer - footer.php
* display footer for all pages

--------------------------------------------------------------------------------------------------------

References

* Pictures
	* Pexels 2018, 'Free stock photos', Pexels, viewed 24 Mar 2018, <https://www.pexels.com>
	* Freepik 2018, 'Free Vector icons', Flaticon, viewed 24 Mar 2018, <https://www.flaticon.com>

* Special Fonts
	* FontSpace 2018, 'FontSpace', viewed 08 May 2018, <http://www.fontspace.com>