# Hotel Booking Project

This is the repository for Group 17's PHP project for hotel booking.

## Prerequisites

1. **XAMPP**: Ensure XAMPP is running with Apache and MySQL.
2. **Composer**: Install Composer from [here](https://getcomposer.org/Composer-Setup.exe).

## Getting Ready To Run Application

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

When done with steps, go back to workspace folder `PhP-prosjekt` in your editor before going to step 3.

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

# Database setup

1. Go to http://localhost/phpmyadmin 
2. Import the SQL script from the config/MYSQL file to set up the database schema and initial data.

# Running the Application
Open your browser and navigate to http://localhost/php-prosjekt/index.php to visit the application's homepage. From there, you can:

1. **Create a new user through registration.**
2. **View your profile.**
3. **Book a room at our beautiful hotel.**
4. **Gain loyalty points for your stays through our loyalty program.**
5. **Get an overview of your stays and booking receipts.**
6. **Update your preferences for hotel rooms and update your profile information.** 
7. **Delete your profile if you are unhappy with our hotel or other reasons to why you does not want to continue using our hotel.** (sorry if we could not meet up to your standards... we will strive to improve to win you back!)

# INFORMATION ABOUT THE PROJECT:

This project is based on MVC model, we believed this was the easiest way to have control over our project.

The group are formed by three students who have tried their best to vizualise their skills in creating a booking page for a hotel.

## THIS IS A SCHOOL PROJECT, NOT A PROJECT BASED ON A REAL HOTEL. THE HOTEL "GUTTABAIS" IS ALL FICTIONAL.. SORRY FOR THOSE WHO HOPED THIS HOTEL WAS REAL :)