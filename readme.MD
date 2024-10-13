# PHP API Boilerplate

This is a basic boilerplate for building a PHP-based API, following an MVC architecture. It includes essential components such as routing, database interaction, and structured models and controllers. You can use this project as a starting point for new PHP API projects.

## Project Structure

This project follows a modular MVC structure, with the following main components:

1. **Models**: Represents data and handles database interactions.
   - `Base.model.php`: The base model class for all models. Other models must inherit this class.

2. **Controllers**: Handles the logic for incoming API requests.
   - `Base.controller.php`: The base controller class contains common logic for all controllers. Other controllers must inherit this class.

3. **Enums**: Contains enumerations for constants or statuses used across the application.
   - `Base.enum.php`: The base enum class for all enums. Other enums must inherit this class.

4. **Exceptions**: Contains classes for error handling across the application.
   - `Base.exception.php`: The base exception class for handling errors. Other exception classes must inherit this class.

5. **Middlewares**: Classes that run before or after requests are handled. Often used for security and logging.
   - `Base.middleware.php`: The base middleware class for handling middleware operations. Other middleware classes must inherit this class.

6. **Public**: The root folder of the project. It contains files accessible by the web server.
   - `index.php`: The entry point of the application. You do not need to make changes to this file.
   - `router.rules.php`: Manages routing rules for the application. You can define the necessary endpoints in this file according to the needs of your application.

## Requirements

- **PHP 7.4+** (or later)
- **Composer** (for autoloading)
- **MySQL** (only MySQL for now)

## How To Use

You can download the repository or clone it via [Git](https://git-scm.com/downloads)
```bash
# Clone the repository and navigate to the project folder
$ git clone https://github.com/mertozturksh/php-api.git
$ cd php-api

# Install dependencies
$ composer install

# Update the `config/database.ini` file with your own database credentials.

# Start the web server
$ php -S localhost:8000 -t public
```