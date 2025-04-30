FROM php:8.2-apache

# Render'ın verdiği port numarasını al
ENV PORT 10000

# Apache'yi bu portu dinleyecek şekilde yapılandır
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

# Uygulama dosyalarını kopyala
COPY . /var/www/html/

# Dışa açılacak port
EXPOSE ${PORT}

# Apache'yi başlat
CMD ["apache2-foreground"]
