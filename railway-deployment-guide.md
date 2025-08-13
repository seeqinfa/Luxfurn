# Deploying LuxFurn Project to Railway

This guide will help you deploy your LuxFurn furniture e-commerce project with integrated Rasa chatbot to Railway.

## Project Structure Overview

Your project consists of:
- **Web Application**: PHP-based e-commerce site with MySQL database
- **Rasa Chatbot**: Python-based conversational AI server
- **Database**: MySQL database with multiple tables

## Step 1: Prepare Your Project for Railway

### 1.1 Create Railway Configuration Files

First, we need to create configuration files for Railway deployment.

### 1.2 Set Up Environment Variables

Create a `.env` file in your project root (this will be used locally, Railway will have its own env vars):

```env
# Database Configuration
MYSQLHOST=localhost
MYSQLPORT=3306
MYSQLUSER=root
MYSQLPASSWORD=
MYSQLDATABASE=luxfurn

# Rasa Configuration
RASA_SERVER_URL=http://localhost:5005
```

## Step 2: Deploy the Database

### 2.1 Create MySQL Database Service

1. Go to [Railway](https://railway.app) and sign in
2. Click "New Project"
3. Select "Add MySQL" from the database options
4. Railway will create a MySQL instance and provide connection details

### 2.2 Import Your Database Schema

1. Get your MySQL connection details from Railway dashboard
2. Use a MySQL client (like phpMyAdmin, MySQL Workbench, or command line) to connect
3. Import your database schema by running the SQL files in this order:
   ```sql
   -- Run these files in order:
   1. DB/luxfurn.sql (users table)
   2. DB/furnitures.sql
   3. DB/orders.sql
   4. DB/order_items.sql
   5. DB/chatbot_reviews.sql
   6. DB/instruction_manuals.sql
   7. DB/support_tickets.sql
   8. DB/support_ticket_roles.sql
   9. DB/ticket_replies.sql
   10. DB/chat_messages.sql
   ```

## Step 3: Deploy the Web Application

### 3.1 Prepare PHP Application

Railway doesn't natively support PHP, so we'll use a Docker approach or deploy to a PHP-compatible service.

**Option A: Use Railway with Docker (Recommended)**

Create a `Dockerfile` in your `web` directory:

```dockerfile
FROM php:8.1-apache

# Install required PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Expose port 80
EXPOSE 80
```

**Option B: Use a PHP hosting service like Heroku or traditional web hosting**

### 3.2 Update Configuration for Production

Update your `web/config.php` to use environment variables:

```php
<?php
// Use Railway environment variables
$host = getenv('MYSQLHOST') ?: '127.0.0.1';
$port = getenv('MYSQLPORT') ?: '3306';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'luxfurn';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$conn = new mysqli($host, $user, $pass, $db, (int)$port);
if ($conn->connect_error) { die('mysqli connect error: ' . $conn->connect_error); }
?>
```

## Step 4: Deploy the Rasa Chatbot

### 4.1 Create Rasa Service on Railway

1. In your Railway project, click "Add Service"
2. Connect your GitHub repository
3. Select the root directory (where your `rasa` folder is)

### 4.2 Create Railway Configuration for Rasa

Create a `railway.toml` file in your project root:

```toml
[build]
builder = "DOCKERFILE"
dockerfilePath = "rasa/Dockerfile"

[deploy]
startCommand = "rasa run --enable-api --cors '*' --port $PORT"
healthcheckPath = "/"
healthcheckTimeout = 300
restartPolicyType = "ON_FAILURE"
restartPolicyMaxRetries = 10
```

### 4.3 Create Dockerfile for Rasa

Create `rasa/Dockerfile`:

```dockerfile
FROM python:3.9-slim

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements and install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy Rasa files
COPY . .

# Train the model
RUN rasa train

# Expose port
EXPOSE $PORT

# Start Rasa server
CMD ["rasa", "run", "--enable-api", "--cors", "*", "--port", "$PORT"]
```

### 4.4 Create Requirements File

Create `rasa/requirements.txt`:

```txt
rasa==3.6.0
mysql-connector-python==8.0.33
```

## Step 5: Configure Environment Variables in Railway

### 5.1 Database Service Variables
Railway will automatically provide these for your MySQL service:
- `MYSQLHOST`
- `MYSQLPORT`
- `MYSQLUSER`
- `MYSQLPASSWORD`
- `MYSQLDATABASE`

### 5.2 Web Application Variables
Add these to your web service:
- `RASA_SERVER_URL` = `https://your-rasa-service.railway.app`

### 5.3 Rasa Service Variables
Add these to your Rasa service:
- `MYSQLHOST` = (copy from database service)
- `MYSQLPORT` = (copy from database service)
- `MYSQLUSER` = (copy from database service)
- `MYSQLPASSWORD` = (copy from database service)
- `MYSQLDATABASE` = (copy from database service)

## Step 6: Update Application URLs

### 6.1 Update Chatbot Integration

In your `web/header.php`, update the Rasa server URL:

```javascript
// Replace localhost URL with Railway URL
const rasaRes = await fetch("https://your-rasa-service.railway.app/webhooks/rest/webhook", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
        sender: "<?=$_SESSION['user_id']?>|<?=$_SESSION['username']?>",
        message: userText
    })
});
```

## Step 7: Deploy and Test

### 7.1 Deploy Services

1. **Deploy Database**: Already done in Step 2
2. **Deploy Rasa Service**: 
   - Push your code to GitHub
   - Railway will automatically build and deploy
   - Wait for the build to complete
3. **Deploy Web Application**:
   - If using Docker on Railway, push your code
   - If using external hosting, upload files via FTP/deployment tools

### 7.2 Test the Deployment

1. **Test Database Connection**: Check if your web app can connect to the Railway MySQL database
2. **Test Web Application**: Navigate to your deployed web app URL
3. **Test Rasa Integration**: Try using the chatbot on your website
4. **Test All Features**: 
   - User registration/login
   - Product browsing
   - Cart functionality
   - Order placement
   - Admin features
   - Support tickets

## Step 8: Post-Deployment Configuration

### 8.1 Set Up Domain and SSL

1. Configure custom domain in Railway (if desired)
2. Railway provides SSL certificates automatically

### 8.2 Monitor and Scale

1. Monitor your services in Railway dashboard
2. Set up alerts for downtime
3. Scale resources as needed

## Troubleshooting Common Issues

### Database Connection Issues
- Verify environment variables are correctly set
- Check database credentials in Railway dashboard
- Ensure database is accessible from your services

### Rasa Service Issues
- Check build logs in Railway dashboard
- Verify Python dependencies are correctly installed
- Ensure model training completes successfully

### CORS Issues
- Make sure Rasa is configured with `--cors "*"`
- Update frontend URLs to use HTTPS

### File Upload Issues
- Ensure proper file permissions in Docker container
- Consider using cloud storage for uploaded files

## Security Considerations

1. **Environment Variables**: Never commit sensitive data to your repository
2. **Database Security**: Use strong passwords and limit database access
3. **HTTPS**: Ensure all communications use HTTPS
4. **Input Validation**: Validate all user inputs on both frontend and backend

## Cost Optimization

1. **Resource Monitoring**: Monitor CPU and memory usage
2. **Database Optimization**: Optimize queries and add indexes where needed
3. **Caching**: Implement caching for frequently accessed data
4. **Image Optimization**: Optimize images to reduce bandwidth usage

This deployment guide should help you get your LuxFurn project running on Railway. The key is to properly configure the environment variables and ensure all services can communicate with each other.