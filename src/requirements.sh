#!/bin/bash

sudo add-apt-repository universe -y
sudo add-apt-repository multiverse -y

sudo add-apt-repository ppa:ondrej/php -y

sudo apt-get update

sudo apt-get install -y \
    nmap \
    traceroute \
    whois \
    curl \
    dirb \
    iputils-ping \
    git \
    nikto \
    dnsutils \
    sslscan \
    whatweb \
    python3 \
    python3-pip \
    golang-go \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    php8.2-cli \
    php8.2-gd \
    php8.2-curl \
    php8.2-xml \
    php8.2-mbstring \
    php8.2-zip
