FROM php:8.2-apache

# Render'ın atadığı PORT ortam değişkenini kullan
ENV PORT=10000

# Apache yapılandırmasını Render'ın PORT değişkenine göre güncelle
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf && \
    sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-enabled/000-default.conf

# Uygulama dosyalarını kopyala
COPY . /var/www/html/

# Apache'yi başlat
CMD ["apache2-foreground"]

# Dışa açılacak port
EXPOSE ${PORT}
