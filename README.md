# PHP Number Combination Application Deployment on AWS EC2

This project involves the deployment of a simple PHP Number Combination application on an Amazon EC2 instance. The setup follows cloud best practices by leveraging a Virtual Private Cloud (VPC) with a secure, scalable architecture.

## Resources Utilized

- **Amazon VPC**  
  - Configured with **public and private subnets** across **2 Availability Zones** for high availability and separation of concerns.

- **Internet Gateway**  
  - Attached to the VPC to allow **public internet access** for instances in the public subnet.

- **Security Groups**  
  - Custom security group created to allow:
    - **SSH (port 22)** for secure terminal access
    - **HTTP (port 80)** to serve the PHP web application

- **Amazon EC2 Instance**  
  - Launched in the **public subnet** to host the PHP application and make it accessible over the internet.

---

###  Deployment Script

```bash
#!/bin/bash
# Switch to root user
sudo su

# Update the Ubuntu OS
sudo apt update

# Install Apache2 Web Server
sudo apt install apache2 -y

# Install PHP and Apache PHP Module
sudo apt install php libapache2-mod-php -y

# Install Composer (PHP Package Manager)
sudo apt install composer -y

# Navigate to Web Server Root Directory
cd /var/www/html

# Remove Default Index File
sudo rm -rf index.html

# Create index.php file
sudo nano index.php
# Paste your PHP code here and save it

# Create composer.json 
sudo nano composer.json
# Paste your composer config here and save it

# Install project dependencies using Composer
sudo composer install

# Restart Apache2 to apply changes
sudo systemctl restart apache2
