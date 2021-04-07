# PhysicalWallet
Laravel Project to simulate physical wallet
## Installation
Please first make sure a proper version of PHP and composer is installed on your system. 
To install this project and make it work, please first clone the whole project in a directory and run these commands in order:
```
composer install
php artisan migrate
php artisan serve 
```
After running the commands above, you will be able to send requests to the project and test different parts.
## Workflow
As the name would suggest, this application is built to simulate physical wallet properties and functions.
In this project, a user should first enroll with a unique email address and a safe password, and then can log in to create
and manage their wallets. After adding one or more wallets, the user can make transactions (either deposit or receive).
In this passage, all defined routes and their functions are describe in a consequential order.
### User Registration
**URL**: `http://domain.name/api/auth/register`  
**Method**: `POST`  
**Parameters**: `name`, `email`, `password`, `password_confirmation`  
**Response**: A warm "welcome" message will be returned if no problem occurs. Otherwise, an error message describing the problem.  
**Notes**: The password is validated through 4 rules. It should be at least 8 characters long and it sould contain at least
one letter, one digit and one of the special characters !,@,#,$,&
### User Login
**URL**: `http://domain.name/api/auth/login`  
**Method**: `POST`  
**Parameters**: `email`, `password`  
**Response**: If the credentials match an existing user, a message and a token will be returned.  
**Notes**: As the project utilizes *sanctum package* for authenticating, the token returned should be contained in all
of the protected routes as a *Bearer Authorization Header*.
### Add New Wallet
**URL**: `http://domain.name/api/wallets/add`  
**Method**: `POST`  
**Parameters**: `name`, `type`  
**Response**: If another wallet with the given name does not exist, just a message announcing a successful operation 
will be returned.  
**Notes**: Here, the field `type` must be either *credit card* or *cash*
### View Wallets
**URL**: `http://domain.name/api/wallets/{id?}`  
**Method**: `GET`  
**Parameters**: No parameters needed.  
**Response**: A Json array of the wallets created by the user. Each entry contains this information: `name`, `type`, 
`value`, `created_at`, `last_transaction_at`  
**Notes**: The field `id` at the end of route URL is optional. If set, the returned result will only contain the information
about the selected wallet.
### Delete Wallet
**URL**: `http://domain.name/api/wallets/{id}`  
**Method**: `DELETE`  
**Parameters**: No parameters needed.  
**Response**: A message showing whether the operation was successful, or an error has occurred.  
**Notes**: The wallet to be deleted must contain no credit, i.e its `value` should be zero.
### Make Transaction
**URL**: `http://domain.name/api/wallets/transaction/{id}`  
**Method**: `POST`  
**Parameters**: `value`  
**Response**: A message showing whether the operation was successful, or an error has occurred.  
**Notes**: Sign of `value` declares type of the transaction. if it is positive, a *deposit* transaction will be performed.
Moreover, the field `id` at the end of route URL is the id of the wallet that the transaction is being performed on.
### View Transactions
**URL**: `http://domain.name/api/wallets/transaction/{id?}`  
**Method**: `GET`  
**Parameters**: No parameters needed.  
**Response**: A Json array consisting of all requested transactions. Each entry in the array contains this information: 
`value`, `type (deposit or receive)`, `performed_at`  
**Notes**: The field `id` at the end of route URL is optional. if set, the returned array will only contain information
of transactions performed on the wallet with the given id.
###User Logout
**URL**: `http://domain.name/api/auth/logout`  
**Method**: `POST`  
**Parameters**: No parameters needed.  
**Response**: A message saying *Goodbye :)*  
**Notes**: After logging out, the token will be removed from database and will no longer work for protected routes.
## Final Notes
* Full Postman Collection of routes with examples is available in [this file](PhysicalWallet.postman_collection.json)
* Since the database and eloquent models does not use `SoftDeletes` trait, every record (such as a wallet ot a token) that
that is deleted cannot be queried or restored later.
* The project models and routes are so limited that I did not felt the need to use laravel `Policies` to protect models.
* As it can be inferred from the information above, all of the routes are defined in `api` section. Thus, all of them have
the `/api` prefix.
