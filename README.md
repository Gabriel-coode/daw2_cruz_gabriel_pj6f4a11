# Automotive Order Management App

## Description

This is a web application for managing customer orders in an automotive store.

## Features

- User authentication.
- Create new orders.
- View all orders.
- Search for an order by its ID.
- Calculate order prices with 21% VAT.

## Technologies

- HTML
- CSS
- JavaScript
- PHP
- JSON
- Apache (XAMPP)
- Git and GitHub

## HTTPS Configuration

The application is designed to run locally using Apache and PHP through XAMPP.

1. Copy or clone the repository into `C:\xampp\htdocs\orderManagementApp`.
2. Start Apache from the XAMPP Control Panel.
3. Make sure Apache SSL is enabled and port 443 is available.
4. Open the application at:

   `https://localhost/orderManagementApp/application/client/index.html`

The local XAMPP installation may use a self-signed SSL certificate. In that case, the browser may display a certificate trust warning.

This warning does not necessarily mean that HTTPS encryption is disabled. A trusted certificate is required to eliminate the warning.

The application uses relative paths so navigation and requests to the PHP server can operate through HTTPS.

**Note:** The Apache SSL configuration is part of the local XAMPP installation and is not included in the project's Git repository.