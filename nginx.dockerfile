# Usamos la imagen base de Nginx
FROM nginx:alpine

# Copiamos tu archivo de configuración local DENTRO de la imagen
# Esto reemplaza la necesidad de un volumen en tiempo de ejecución
COPY nginx.conf /etc/nginx/conf.d/default.conf