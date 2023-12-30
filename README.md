<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.



# Loan Calculator

This is a Laravel project built with version 9.19.

## Getting Started

1. **Clone the repository:**

    ```bash
    git clone git@github.com:ferojk34/loanCalculator.git
    ```

2. **Navigate to the project directory:**

    ```bash
    cd loanCalculator
    ```
3. **Running the Application:**

    - Install PHP dependencies:

        ```bash
        composer install --ignore-platform-reqs
        ```
4. **Environment Setup:**

    - Copy the `.env.example` file to `.env`:
	
	  ```bash
        cp .env.example .env
        ```

   - Generate the application key:

        ```bash
        php artisan key:generate
        ```
		
	- Copy the `.env.example` file to `.env.testing`:

        ```bash
        cp .env.example .env.testing
		Make changes in env.testing:
         APP_ENV=testing
		 DB_DATABASE=loan_calculator_testing
        ```

5. **Configuration:**

    - Update the `.env` file with your database credentials, mail settings, and any other necessary configuration.

6. **Database Setup:**

    - Run migrations to create database tables:

        ```bash
        php artisan migrate
        ```
		
		  Generate testing tables using below the command:

        ```bash
     php artisan migration --env=testing
        ```
		
    - Serve the application:

        ```bash
        php artisan serve
        ```

        Your application should now be running at `http://localhost:8000`.

7. **Run Unit Testing:**

    - Run the below command on terminal to perform unit testing:

       ```bash
         php artisan test Modules\LoanCalculator\Tests\Unit\LoanCalculatorTest.php
        ```
		

