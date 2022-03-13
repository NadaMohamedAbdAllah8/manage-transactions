# *Manage Transactions*

A mini-project using RESTAPIs to manage transaction using Laravel, with authentication using sanctum.
There are two roles: admin, customer
### Admins can

-Register
-Login
-Logout
-Create transactions
-View transactions
-Record payments
-View transaction's payments
-Generate reports
- Basic report showing the total transactions paid, outstanding, and over due amounts for a given range
- Month report showing the total transactions paid, outstanding, and over due amounts for each month of a given range

-Create categories to be used for the transactions
-View all categories
-Create subcategories to be used for the transactions
-View all subcategories

### Customers can

-Register
-Login
-Logout
-View their transactions

## Installation

Use the composer manager, apply these steps at the first time
composer install

#### Creating, and seeding the database

php artisan migrate
php artisan db:seed
## Run

php artisan serve

