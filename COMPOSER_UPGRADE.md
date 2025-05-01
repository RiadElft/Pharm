# Future Composer Implementation

This project currently uses manual file inclusion for simplicity during initial development. 
A future upgrade to Composer is planned for better dependency management and autoloading.

## Why Upgrade to Composer?

1. **Dependency Management**
   - Manage third-party packages
   - Handle package versions
   - Security updates
   - Easy package installation/removal

2. **Autoloading**
   - Replace manual requires with PSR-4 autoloading
   - Automatic class loading
   - Better performance
   - Cleaner code

3. **Project Structure**
   - Standard PHP project structure
   - Better organization
   - Industry standard practices

## Required Changes for Composer Implementation

1. **Install Composer**
   ```bash
   # Download and install from
   https://getcomposer.org/Composer-Setup.exe
   ```

2. **Create composer.json**
   ```json
   {
       "name": "pharmacy/management-system",
       "description": "Pharmacy Management System with complete CRUD operations",
       "type": "project",
       "require": {
           "php": "^8.1",
           "ext-pdo": "*",
           "vlucas/phpdotenv": "^5.5",
           "phpmailer/phpmailer": "^6.8"
       },
       "autoload": {
           "psr-4": {
               "App\\": "app/"
           }
       }
   }
   ```

3. **Remove Manual Requires**
   - Remove manual requires from index.php
   - Add vendor/autoload.php require
   - Implement .env file for configuration

4. **Install Dependencies**
   ```bash
   composer install
   ```

## Benefits After Implementation

1. **Easier Updates**
   - `composer update` for all packages
   - Security patches
   - Version management

2. **Better Development**
   - Development dependencies
   - Testing tools
   - Code quality tools

3. **Industry Standard**
   - Better code sharing
   - Easier team onboarding
   - Standard practices

## Timeline
- Implement after basic functionality is complete
- Before moving to production
- When adding additional packages 