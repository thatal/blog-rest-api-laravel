# Blog REST API Laravel

This is a Laravel based REST API for a blog application.
## Features

- **User Authentication:** Secure login and registration using **Sanctum**.
- **Role Permission:** Manage user roles and permissions.
- **CRUD Operations:** Create, read, update, and delete blog posts, categories and comments.
- **Database Migrations:** Easily set up and manage the database schema.
- **API Documentation:** Comprehensive documentation *coming soon*.
- **Testing:** Pest tests for ensuring code quality.
- **Error Handling:** Graceful error handling and validation.
- **Pagination:** Efficiently paginate large sets of data.
- **CORS Support:** Cross-Origin Resource Sharing enabled.

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL or any other supported database
- Git

## Installation

1. **Clone the repository:**

    ```sh
    git clone https://github.com/thatal/blog-api-laravel.git
    cd blog-api-laravel
    ```

2. **Install dependencies:**

    ```sh
    composer install
    npm install
    ```

3. **Copy the example environment file and configure the environment variables:**

    ```sh
    cp .env.example .env
    ```

    Update the `.env` file with your database and other configurations.

4. **Generate the application key:**

    ```sh
    php artisan key:generate
    ```

5. **Run the database migrations:**

    ```sh
    php artisan migrate
    ```

6. **Seed the database (optional):**

    ```sh
    php artisan db:seed
    ```

7. **Run the development server:**

    ```sh
    php artisan serve
    ```
## Running Tests

To run the tests, use the following command:

```sh
php artisan test
```
## Milestone

- **Version 1.0:**
    - Initial release with basic blog functionalities REST api.
    - User authentication and authorization.
    - CRUD operations for posts, categories and comments.
    - Basic testing setup.

- **Version 1.1:**
    - [ ] Adding slug support for SEO-friendly URLs. *(coming soon)*
    - [ ] Adding tags and media support. *(coming soon)*
    - [ ] API documentation using Swagger or Postman. *(coming soon)*
    - [ ] Improved error handling and validation. *(coming soon)*
    - [ ] Additional tests for improved code coverage. *(coming soon)*
    - [ ] Improved pagination, searching & sorting. *(coming soon)*

- **Version 1.2:**
    - [ ] Implementing caching for improved performance. *(pending)*
    - [ ] Implementing rate limiting for API requests. *(pending)*
    - [ ] Implementing email notifications for new posts. *(pending)*
    - [ ] Implementing a user profile page. *(pending)*
    - [ ] Adding support for multiple languages. *(pending)*
    - [ ] Implementing a search feature for posts. *(pending)*
    - [ ] Adding a feature to like and share posts. *(pending)*

## Contributing

Contributions are welcome! Here's how you can help:

- **Reporting issues:** If you find any bugs or issues, please open a GitHub issue.
- **Suggesting features:** If you have any suggestions or would like to request a feature, please open a GitHub issue.
- **Opening pull requests:** If you'd like to contribute directly to the codebase, please fork this repository and submit a pull request.
- **Ensuring tests pass:** Make sure all existing test cases pass before submitting a pull request.
- **Adding tests:** Include test cases for any new features or changes you introduce.


## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).