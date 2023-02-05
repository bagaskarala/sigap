# SIGAP (WORK IN PROGRESS)
Sistem Informasi Penerbitan dan Percetakan Buku GAMA PRESS

Developed for UGM PRESS internal system

## Feature
- **PUBLISHING SYSTEM**
  - User Management
  - Draft Management
  - Book Management
  - Document Management
  - User Access Control
  - Versioning Draft / Track draft progress
- **PRINTING SYSTEM** (coming soon)
- **WAREHOUSE SYSTEM** (coming soon)

## System
- PHP - Codeigniter 3
- CSS - Bootstrap 4
- JS - Jquery
- Database - Mysql

## How to run
- Xammpp with PHP 7.3
- Install composer
- You can run PHP and composer on terminal
- Create `.env.development` from `.env.example` and fill those credentials
- Configure database.php
- Run local webserver and mysql
- Import sigap.sql database using phpmyadmin
- Run `composer install`

### Formatting Convention
- Install **PHP intelephense** extension in vs code

### Migration and Seeding
- Run `php index.php tools help` to show available CLI commands
