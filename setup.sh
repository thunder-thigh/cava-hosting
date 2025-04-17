# Update system packages
sudo apt update
sudo apt upgrade

# Install Apache and PHP
sudo apt install apache2 php libapache2-mod-php php-gd

# Start Apache and enable it to start on boot
sudo systemctl start apache2
sudo systemctl enable apache2