# Usamos la imagen base de Nginx
FROM nginx:alpine

# Copiamos tu archivo de configuración (nginx.conf)
COPY nginx.conf /etc/nginx/conf.d/default.conf


WORKDIR /var/www/html
COPY . .

# Damos permisos a Nginx para leer la carpeta pública
RUN chmod -R 755 /var/www/html/public
