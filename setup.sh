# Update system packages
sudo apt update
sudo apt upgrade

# Install Apache and PHP
sudo apt install apache2 php libapache2-mod-php php-gd

# Start Apache and enable it to start on boot
sudo systemctl start apache2
sudo systemctl enable apache2

# Create a directory for uploaded files
sudo mkdir -p /var/www/html/uploads
sudo chmod 777 /var/www/html/uploads  #for development only

# Create the main website files
sudo touch /var/www/html/index.php
sudo touch /var/www/html/download.php