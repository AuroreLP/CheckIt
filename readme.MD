# Checkit
Checkit is a PHP project and task management application designed to help you organize tasks across multiple projects efficiently.
This repository provides a Docker-based development and production environment using PHP 8.3, Nginx, and MySQL.

## Features

- Manage multiple projects and related tasks
- Simple web interface built in PHP
- MySQL database with initial schema (imported manually)
- PhpMyAdmin for database administration
- Ready-to-use Docker Compose setup for development and production

## Clone the repo

Clone the repository from GitHub and access the project :

```bash
git clone https://github.com/AuroreLP/CheckIt.git
cd Checkit
```

## Prerequisites

- **Docker** (>= 28.1.1)
- **Docker Compose**
- **Composer** (>= 2.8.6)

## Project Structure
.
├── Dockerfile.dev # Development Dockerfile
├── Dockerfile.prod # Production Dockerfile
├── compose.dev.yaml # Docker Compose for development
├── compose.prod.yaml # Docker Compose for production
├── Makefile # Make commands to start/stop containers
├── nginx/
│ ├── nginx.dev.conf
│ └── nginx.prod.conf
├── dump.sql # SQL dump to initialize the database
├── .env.example # Environment variable template
├── assets/ # CSS and images
├── lib/ # PHP classes
├── templates/ # Shared HTML templates
├── index.php and other PHP scripts


## Docker Environment Overview

The application runs in containers:
- PHP/Nginx container
	PHP 8.3 FPM with Nginx
	Composer installed
- MySQL 8.0 container
	Initializes the database from `dump.sql`
- PhpMyAdmin container
	Accessible locally for development

## How to Run

### Development

#### Configure environment variables

Copy `.env.example` to `.env` with this command : cp .env.example .env
and fill in your values:
```
MYSQL_HOST=your_mysql_host
MYSQL_DATABASE=checkit_db
MYSQL_USER=your_mysql_user
MYSQL_PASSWORD=your_mysql_password
MYSQL_PORT=3306
```

#### Start development environment

make up-dev
Application available at http://localhost:8088
PhpMyAdmin at http://localhost:8082

#### Stop development environment
make down-dev

### Production

1.	Create a .env.prod file with production credentials.
2.	Start production environment with make up-prod - Nginx listens on port 10000 (required by Render).
3.	Stop production environment with make down-prod

## Database Initialization

The database is initialized from dump.sql automatically when the MySQL container is started for the first time.
If needed, you can re-import manually with the command : 
docker exec -i mysql mysql -u <user> -p <password> < < dump.sql

## Default Ports
Environment	Application URL	PhpMyAdmin URL
Development	http://localhost:8088
http://localhost:8082

Production	http://localhost:10000
(PhpMyAdmin not included)

## Useful Commands
You can use the Makefile for convenience:
Command	Description
make up-dev	Start development environment
make down-dev	Stop development environment
make up-prod	Start production environment
make down-prod	Stop production environment

## Notes
Manual Database Import: If you reset MySQL data, you may need to re-import dump.sql.
Environment Variables: Always create a .env or .env.prod file before starting.
Render Compatibility: Production Nginx is configured to expose port 10000.

## License
MIT License (or specify if you have another license)
