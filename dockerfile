FROM php:8.2-apache

# Render'ın atadığı PORT ortam değişkenini oku
ENV PORT=10000

# Apache'nin dinlediği portu bu değişkenle değiştir
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf && \
    sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-enabled/000-default.conf

# Projeni apache dizinine kopyala
COPY . /var/www/html/

# Render'a dışarı açılan portu bildir
EXPOSE ${PORT}

# Apache’yi foreground’da başlat (container canlı kalsın)
CMD ["apache2-foreground"]
