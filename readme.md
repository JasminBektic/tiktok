# Configuration
1. Copy the content of .env.example file and paste into new .env file. Modify the section:
```
DB_NAME=tiktok
DB_HOST=localhost
DB_PORT=3306
DB_USER=admin
DB_PASSWORD=
```

# Usage
1. Before running it, vendor installation is needed
```
composer update 
```
2. Scraping can be triggered trough console, ie.
```
php bin/user_scrape
php bin/video_scrape
```