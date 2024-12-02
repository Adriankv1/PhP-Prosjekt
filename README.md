# Hotel Booking Project

This is the repository for Group 17's PHP project for hotel booking.

## Prerequisites

1. **XAMPP**: Ensure XAMPP is running with Apache and MySQL.
2. **Composer**: Install Composer from [here](https://getcomposer.org/Composer-Setup.exe).

## Installation

### Step 1: Composer Setup

1. Run the Composer installer.
2. Locate the `php.exe` file during installation.
3. Allow Composer to use the PHP path.
4. Complete the installation.

### Step 2: PHP Configuration

1. Open XAMPP/php folder in VSC or your preferred editor.
2. Open `php.ini`.
3. Search for `extension=gd`
4. Uncomment the line `extension=gd`.

### Step 3: Install Dependencies

In your terminal, navigate to the project directory and run:

```sh
composer require setasign/fpdf
composer require setasign/fpdi
```

If you encounter issues, try the following commands first:

```sh
rm -rf vendor
composer clear-cache
```
Then, run the dependency installation commands again.

Step 4: Database setup

1. Go to http://localhost/myphpadmin 
2. Import the SQL script from the config/MYSQL file to set up the database schema and initial data.

Running the Application
Open your browser and navigate to http://localhost/php-prosjekt/index.php to visit the application's homepage. From there, you can:

Create a new user through registration.
View your profile.
Book a room at our beautiful hotel.
Gain loyalty points for your stays through our loyalty program.
Get an overview of your stays and booking receipts.

INFORMATION ABOUT THE PROJECT:
# THIS IS A SCHOOL PROJECT, NOT A PROJECT BASED ON A REAL HOTEL. THE HOTEL "GUTTABAIS" IS ALL FICTIONAL.. SORRY FOR THOSE WHO HOPED THIS HOTEL WAS REAL :)

This project is based on MVC model, we believed this was the easiest way to have control over our project.

The group are formed by three students who have tried their best to vizualise their skills in creating a booking page for a hotel.
