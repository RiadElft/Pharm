# Pharmacy Management System

A comprehensive pharmacy management system built with PHP 8.1+, featuring inventory management, sales tracking, and customer management.

## Features

- Product Management (CRUD operations)
- Category Management
- Supplier Management
- Inventory/Stock Management
- Customer Management
- User Management (Pharmacists/Admins)
- Sales Record Management
- Stock Alerts (Low stock, Expiring items)
- Sales Reports

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd pharmacy
```

2. Install dependencies:
```bash
composer install
```

3. Create a copy of `.env.example` and rename it to `.env`:
```bash
cp .env.example .env
```

4. Update the `.env` file with your database credentials and other configuration settings.

5. Create the database and tables:
```bash
mysql -u your_username -p < database/schema.sql
```

6. Set up your web server to point to the `public` directory.

7. Make sure the storage directory is writable:
```bash
chmod -R 775 storage/
```

## Project Structure

```
pharmacy/
├── app/
│   ├── Config/
│   ├── Controllers/
│   ├── Models/
│   └── Views/
├── database/
│   └── schema.sql
├── public/
│   ├── css/
│   ├── js/
│   └── index.php
├── storage/
│   ├── logs/
│   └── uploads/
├── vendor/
├── .env.example
├── composer.json
└── README.md
```

## Usage

1. Access the application through your web browser
2. Log in with your credentials
3. Navigate through the dashboard to manage:
   - Products
   - Categories
   - Suppliers
   - Inventory
   - Customers
   - Sales
   - Reports

## Security

- All passwords are hashed using PHP's password_hash()
- SQL injection prevention using prepared statements
- XSS protection
- CSRF protection
- Input validation and sanitization

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 